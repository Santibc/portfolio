<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\PuntoCliente;
use App\Models\FechaEspecialCliente;
use App\Models\ConfiguracionPuntos;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PuntosController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Obtener empresa del contexto
        $empresaId = session('empresa_id') ?? \App\Models\Empresa::where('activo', true)->first()?->id ?? 1;

        // Obtener configuración de puntos
        $config = ConfiguracionPuntos::getConfiguracion($empresaId);

        // Balance de puntos
        $balance = PuntoCliente::getBalance($user->id);

        // Historial de puntos
        $historial = PuntoCliente::porUsuario($user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Código de referido
        if (!$user->codigo_referido) {
            $user->update([
                'codigo_referido' => strtoupper(Str::random(8))
            ]);
        }

        // Referidos del usuario
        $referidos = User::where('referido_por', $user->id)->get();

        // Fechas especiales
        $fechasEspeciales = FechaEspecialCliente::porUsuario($user->id)
            ->activas()
            ->orderByRaw('DATE_FORMAT(fecha, "%m-%d")')
            ->get();

        // Productos sugeridos basados en próximas fechas especiales
        $productosSugeridos = $this->obtenerProductosSugeridos($fechasEspeciales);

        return view('cliente.puntos.index', compact(
            'user', 'balance', 'historial', 'referidos', 'fechasEspeciales', 'productosSugeridos', 'config'
        ));
    }

    protected function obtenerProductosSugeridos($fechasEspeciales)
    {
        // Si hay fechas próximas (en los próximos 30 días), buscar productos relacionados
        $proximaFecha = $fechasEspeciales->first(function ($fecha) {
            return $fecha->dias_restantes >= 0 && $fecha->dias_restantes <= 30;
        });

        $query = Producto::where('activo', true)
            ->where('eliminado', false);

        if ($proximaFecha) {
            // Buscar por palabras clave según el tipo de ocasión
            $keywords = match ($proximaFecha->tipo) {
                'cumpleanos' => ['cumpleanos', 'celebracion', 'feliz', 'especial'],
                'aniversario' => ['amor', 'romantico', 'aniversario', 'rosas', 'corazon'],
                'dia_madre' => ['madre', 'mama', 'especial', 'amor'],
                'dia_padre' => ['padre', 'papa'],
                default => []
            };

            if (!empty($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhere('nombre', 'like', "%{$keyword}%")
                          ->orWhere('descripcion', 'like', "%{$keyword}%");
                    }
                });
            }
        }

        $productos = $query->inRandomOrder()->limit(4)->get();

        // Si no hay productos específicos, mostrar productos destacados
        if ($productos->isEmpty()) {
            $productos = Producto::where('activo', true)
                ->where('eliminado', false)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        return $productos;
    }

    public function storeFechaEspecial(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'receptor' => ['nullable', 'string', 'max:255'],
            'tipo' => ['required', 'in:cumpleanos,aniversario,dia_madre,dia_padre,navidad,otro'],
            'fecha' => ['required', 'date'],
            'notas' => ['nullable', 'string', 'max:500'],
            'dias_anticipacion' => ['nullable', 'integer', 'min:1', 'max:60'],
            'descuento_especial' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        FechaEspecialCliente::create([
            ...$validated,
            'user_id' => Auth::id(),
            'recordar_dias_antes' => true,
            'dias_anticipacion' => $validated['dias_anticipacion'] ?? 5,
            'descuento_especial' => $validated['descuento_especial'] ?? 0,
        ]);

        $mensaje = 'Fecha especial agregada. Te recordaremos cuando se acerque.';
        if (isset($validated['descuento_especial']) && $validated['descuento_especial'] > 0) {
            $mensaje .= " Recibirás un cupón del {$validated['descuento_especial']}% de descuento.";
        }

        return redirect()->route('cliente.puntos')
            ->with('success', $mensaje);
    }

    public function destroyFechaEspecial(FechaEspecialCliente $fechaEspecial)
    {
        if ($fechaEspecial->user_id !== Auth::id()) {
            abort(403);
        }

        $fechaEspecial->delete();

        return redirect()->route('cliente.puntos')
            ->with('success', 'Fecha especial eliminada.');
    }

    public function compartirReferido()
    {
        $user = Auth::user();

        if (!$user->codigo_referido) {
            $user->update([
                'codigo_referido' => strtoupper(Str::random(8))
            ]);
        }

        return response()->json([
            'codigo' => $user->codigo_referido,
            'url' => route('register.cliente') . '?ref=' . $user->codigo_referido,
        ]);
    }

    public function storeFecha(Request $request)
    {
        return $this->storeFechaEspecial($request);
    }

    public function destroyFecha(FechaEspecialCliente $fecha)
    {
        return $this->destroyFechaEspecial($fecha);
    }

    public function canjear(Request $request)
    {
        $user = Auth::user();

        // Obtener configuración
        $empresaId = session('empresa_id') ?? \App\Models\Empresa::where('activo', true)->first()?->id ?? 1;
        $config = ConfiguracionPuntos::getConfiguracion($empresaId);

        if (!$config || !$config->sistema_activo) {
            return redirect()->route('cliente.puntos')
                ->with('error', 'El sistema de puntos no está activo.');
        }

        $request->validate([
            'puntos' => ['required', 'integer', 'min:' . $config->canje_minimo],
        ]);

        $puntosACanjear = $request->puntos;

        // Validar máximo si está configurado
        if ($config->canje_maximo && $puntosACanjear > $config->canje_maximo) {
            return redirect()->route('cliente.puntos')
                ->with('error', "No puedes canjear más de {$config->canje_maximo} puntos por vez.");
        }

        $balance = PuntoCliente::getBalance($user->id);

        if ($balance < $puntosACanjear) {
            return redirect()->route('cliente.puntos')
                ->with('error', 'No tienes suficientes puntos para canjear.');
        }

        // Calcular descuento usando la configuración
        $descuento = $config->calcularDescuentoPorPuntos($puntosACanjear);

        $resultado = PuntoCliente::canjearPuntos(
            $user->id,
            $puntosACanjear,
            "Canje por descuento de \${$descuento}"
        );

        if ($resultado) {
            // Guardar código de descuento en sesión para usar en checkout
            $codigoDescuento = 'PUNTOS' . strtoupper(Str::random(6));

            session([
                'descuento_puntos' => [
                    'codigo' => $codigoDescuento,
                    'valor' => $descuento,
                    'puntos' => $puntosACanjear,
                ]
            ]);

            return redirect()->route('cliente.puntos')
                ->with('success', "Has canjeado {$puntosACanjear} puntos por un descuento de \${$descuento}. Usa el código {$codigoDescuento} en tu próxima compra.");
        }

        return redirect()->route('cliente.puntos')
            ->with('error', 'Error al canjear los puntos.');
    }
}
