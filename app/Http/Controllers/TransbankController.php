<?php

namespace App\Http\Controllers;

use App\Models\TransaccionPago;
use App\Models\Compra;
use App\Models\PuntoCliente;
use App\Services\TransbankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TransbankController extends Controller
{
    protected TransbankService $transbank;

    public function __construct(TransbankService $transbank)
    {
        $this->transbank = $transbank;
    }

    /**
     * Manejar el retorno desde WebPay
     */
    public function retorno(Request $request, string $referencia)
    {
        Log::info('Retorno WebPay recibido', [
            'referencia' => $referencia,
            'token_ws' => $request->input('token_ws'),
            'TBK_TOKEN' => $request->input('TBK_TOKEN'),
            'TBK_ORDEN_COMPRA' => $request->input('TBK_ORDEN_COMPRA'),
            'TBK_ID_SESION' => $request->input('TBK_ID_SESION'),
        ]);

        // Buscar la transacción
        $transaccion = TransaccionPago::where('referencia_transaccion', $referencia)->first();

        if (!$transaccion) {
            Log::error('Transacción no encontrada', ['referencia' => $referencia]);
            return redirect()->route('tienda.empresa')
                ->with('error', 'Transacción no encontrada');
        }

        $compra = $transaccion->compra;

        // Verificar si el usuario canceló o hubo timeout
        if ($request->has('TBK_TOKEN') && !$request->has('token_ws')) {
            // Usuario canceló en el formulario de pago
            Log::info('Usuario canceló el pago', ['referencia' => $referencia]);

            $transaccion->update([
                'estado' => 'cancelada',
                'mensaje_error' => 'Pago cancelado por el usuario',
                'fecha_procesamiento' => now(),
            ]);

            $compra->update(['estado' => 'cancelada']);
            $this->liberarStock($compra);

            return redirect()->route('tienda.pago.rechazado', [
                'referencia' => $referencia
            ])->with('error', 'El pago fue cancelado');
        }

        if ($request->has('TBK_ORDEN_COMPRA') && !$request->has('token_ws')) {
            // Timeout - el usuario no completó a tiempo
            Log::info('Timeout en el pago', ['referencia' => $referencia]);

            $transaccion->update([
                'estado' => 'expirada',
                'mensaje_error' => 'Tiempo de espera agotado',
                'fecha_procesamiento' => now(),
            ]);

            $compra->update(['estado' => 'cancelada']);
            $this->liberarStock($compra);

            return redirect()->route('tienda.pago.rechazado', [
                'referencia' => $referencia
            ])->with('error', 'El tiempo para completar el pago ha expirado');
        }

        // Obtener el token
        $token = $request->input('token_ws');

        if (!$token) {
            Log::error('Token no recibido', ['referencia' => $referencia]);

            return redirect()->route('tienda.pago.rechazado', [
                'referencia' => $referencia
            ])->with('error', 'Error en el proceso de pago');
        }

        // Confirmar la transacción con WebPay
        $resultado = $this->transbank->confirmarTransaccion($token);

        if (!$resultado['success']) {
            Log::error('Error confirmando transacción', [
                'referencia' => $referencia,
                'error' => $resultado['error'] ?? 'Error desconocido'
            ]);

            $transaccion->update([
                'estado' => 'error',
                'mensaje_error' => $resultado['error'] ?? 'Error al confirmar transacción',
                'fecha_procesamiento' => now(),
            ]);

            $compra->update(['estado' => 'cancelada']);
            $this->liberarStock($compra);

            return redirect()->route('tienda.pago.rechazado', [
                'referencia' => $referencia
            ])->with('error', 'Error al procesar el pago');
        }

        // Procesar según el resultado
        return $this->procesarResultado($transaccion, $compra, $resultado, $token);
    }

    /**
     * Procesar el resultado de la transacción
     */
    protected function procesarResultado(
        TransaccionPago $transaccion,
        Compra $compra,
        array $resultado,
        string $token
    ) {
        DB::beginTransaction();

        try {
            $esExitosa = TransbankService::esTransaccionExitosa($resultado);

            if ($esExitosa) {
                // Transacción aprobada
                $transaccion->update([
                    'estado' => 'aprobada',
                    'id_transaccion_pasarela' => $resultado['authorization_code'] ?? null,
                    'codigo_autorizacion' => $resultado['authorization_code'] ?? null,
                    'metodo_pago' => TransbankService::interpretarTipoPago($resultado['payment_type_code'] ?? ''),
                    'fecha_procesamiento' => now(),
                    'respuesta_pasarela' => $resultado,
                ]);

                // Actualizar compra
                $compra->update(['estado' => 'pagada']);

                // Generar comisión
                $compra->generarComision();

                // Registrar puntos por compra (1 punto por cada $100 CLP)
                $this->registrarPuntosCompra($compra);

                // Registrar evento
                $transaccion->registrarEvento('PAYMENT_APPROVED', $resultado, request()->ip());

                DB::commit();

                Log::info('Pago aprobado', [
                    'referencia' => $transaccion->referencia_transaccion,
                    'authorization_code' => $resultado['authorization_code'] ?? null
                ]);

                return redirect()->route('tienda.pago.confirmacion', [
                    'referencia' => $transaccion->referencia_transaccion
                ]);

            } else {
                // Transacción rechazada
                $mensajeError = TransbankService::interpretarCodigoRespuesta(
                    $resultado['response_code'] ?? -99
                );

                $transaccion->update([
                    'estado' => 'rechazada',
                    'mensaje_error' => $mensajeError,
                    'fecha_procesamiento' => now(),
                    'respuesta_pasarela' => $resultado,
                ]);

                $compra->update(['estado' => 'cancelada']);
                $this->liberarStock($compra);

                // Registrar evento
                $transaccion->registrarEvento('PAYMENT_REJECTED', $resultado, request()->ip());

                DB::commit();

                Log::info('Pago rechazado', [
                    'referencia' => $transaccion->referencia_transaccion,
                    'response_code' => $resultado['response_code'] ?? null,
                    'mensaje' => $mensajeError
                ]);

                return redirect()->route('tienda.pago.rechazado', [
                    'referencia' => $transaccion->referencia_transaccion
                ])->with('error', $mensajeError);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error procesando resultado WebPay', [
                'referencia' => $transaccion->referencia_transaccion,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('tienda.pago.rechazado', [
                'referencia' => $transaccion->referencia_transaccion
            ])->with('error', 'Error interno al procesar el pago');
        }
    }

    /**
     * Liberar stock de productos
     */
    protected function liberarStock(Compra $compra): void
    {
        foreach ($compra->items as $item) {
            if ($item->producto && $item->producto->controlar_stock) {
                $stock = $item->variante_producto_id
                    ? $item->producto->stock()->where('variante_producto_id', $item->variante_producto_id)->first()
                    : $item->producto->stockPrincipal;

                if ($stock) {
                    $stock->entrada(
                        $item->cantidad,
                        'devolucion',
                        $compra->numero_compra,
                        'Pago cancelado/rechazado'
                    );
                }
            }
        }
    }

    /**
     * Consultar estado de una transacción
     */
    public function consultarEstado(string $token)
    {
        $resultado = $this->transbank->consultarEstado($token);

        return response()->json($resultado);
    }

    /**
     * Registrar puntos por compra
     * Regla: 1 punto por cada $100 CLP de compra
     */
    protected function registrarPuntosCompra(Compra $compra): void
    {
        // Solo si la compra tiene un usuario autenticado
        if (!$compra->user_id) {
            return;
        }

        // Calcular puntos (1 punto por cada $100 CLP)
        $puntos = floor($compra->total / 100);

        if ($puntos > 0) {
            PuntoCliente::registrarPuntos(
                $compra->user_id,
                $compra->empresa_id,
                $puntos,
                'ganados',
                "Compra #{$compra->numero_compra}",
                $compra->id
            );

            Log::info("Puntos registrados: {$puntos} puntos para usuario {$compra->user_id} por compra {$compra->numero_compra}");
        }
    }
}
