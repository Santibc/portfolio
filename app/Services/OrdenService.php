<?php

namespace App\Services;

use App\Models\AsignacionPieza;
use App\Models\Orden;
use App\Models\OrdenBosquejo;
use App\Models\OrdenItem;
use App\Models\OrdenPieza;
use App\Models\Pago;
use App\Models\User;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\File;
use App\Helpers\ImageHelper;
use Intervention\Image\Facades\Image;

class OrdenService
{
    protected OrdenEstadoService $estadoService;

    public function __construct(OrdenEstadoService $estadoService)
    {
        $this->estadoService = $estadoService;
    }

    /**
     * Guarda orden como borrador. Validacion minima.
     */
    public function guardarBorrador(array $data, User $user, ?Orden $orden = null): Orden
    {
        if ($orden) {
            $orden->update([
                'cliente_id' => $data['cliente_id'] ?? $orden->cliente_id,
                'fecha_entrega' => $data['fecha_entrega'] ?? null,
                'hora_entrega' => $data['hora_entrega'] ?? null,
                'notas' => $data['notas'] ?? null,
            ]);
        } else {
            $orden = Orden::create([
                'cliente_id' => $data['cliente_id'],
                'creado_por' => $user->id,
                'estado_trabajo' => 'borrador',
                'fecha_entrega' => $data['fecha_entrega'] ?? null,
                'hora_entrega' => $data['hora_entrega'] ?? null,
                'notas' => $data['notas'] ?? null,
            ]);
        }

        // Sincronizar entidades hijas
        if (isset($data['items'])) {
            $this->sincronizarItems($orden, $data['items']);
        }

        $bosquejoMap = [];
        if (isset($data['bosquejos'])) {
            $bosquejoMap = $this->sincronizarBosquejos($orden, $data['bosquejos']);
        }

        if (isset($data['piezas'])) {
            $this->sincronizarPiezas($orden, $data['piezas'], $bosquejoMap);
        }

        if (isset($data['pagos'])) {
            $this->sincronizarPagos($orden, $data['pagos'], $user);
        }

        if (!empty($data['firma_data'])) {
            $ruta = $this->guardarFirma($orden, $data['firma_data']);
            $orden->update(['ruta_firma_cliente' => $ruta]);
        }

        // Preservar/crear asignacion de operario para ordenes ya generadas
        if ($orden->estado_trabajo !== 'borrador' && !empty($data['operario_id'])) {
            $this->actualizarAsignacionOperario($orden, (int) $data['operario_id'], $user);
        }

        // Recalcular totales
        $this->estadoService->recalcularTotales($orden);
        $orden->save();

        return $orden;
    }

    /**
     * Genera orden con validacion completa y numero consecutivo.
     */
    public function generarOrden(array $data, User $user, ?Orden $orden = null): Orden
    {
        // Validar server-side
        $errores = $this->validarParaGenerar($data);
        if (!empty($errores)) {
            throw new \Illuminate\Validation\ValidationException(
                \Illuminate\Support\Facades\Validator::make([], []),
                response()->json(['success' => false, 'message' => 'Falta diligenciar informacion para poder GENERAR ORDEN', 'errores' => $errores], 422)
            );
        }

        // Guardar borrador primero (persiste todos los datos)
        $orden = $this->guardarBorrador($data, $user, $orden);

        // Asignar numero consecutivo y marcar como generada
        $orden->numero_orden = $this->estadoService->generarNumeroConsecutivo();
        $orden->estado_trabajo = 'generada';
        $orden->save();

        $piezas = $orden->piezas()->get();

        if ($piezas->isEmpty()) {
            // Venta directa: sin piezas
            $orden->estado_trabajo = 'ejecutada';
            $orden->estado_entrega = 'entregada';
        } else {
            // Piezas sin operario -> completada inmediatamente
            foreach ($piezas->where('requiere_operario', false) as $pieza) {
                $pieza->update(['estado' => 'completada', 'porcentaje_avance' => 100]);
            }

            // Piezas con operario -> asignar
            $piezasConOperario = $piezas->where('requiere_operario', true);
            if ($piezasConOperario->isNotEmpty()) {
                $operarioId = $data['operario_id'];
                $this->crearAsignacionesIniciales($orden, (int) $operarioId, $user, $piezasConOperario);
                NotificacionService::ordenGenerada($orden, (int) $operarioId);
            }

            // Recalcular estado basado en piezas actualizadas
            $orden->estado_trabajo = $this->estadoService->recalcularEstadoTrabajo($orden->fresh());
        }

        // Recalcular estado de pago
        $orden->estado_pago = $this->estadoService->recalcularEstadoPago($orden);
        $orden->save();

        return $orden;
    }

    /**
     * Sube un bosquejo temporal (mid-wizard, via AJAX).
     */
    public function subirBosquejoTemporal($archivo, string $tipoOrigen, ?int $ordenId = null, ?string $nombre = null, ?int $plantillaId = null): array
    {
        // Determinar carpeta destino
        if ($ordenId) {
            $uploadPath = public_path("uploads/ordenes/{$ordenId}/bosquejos");
        } else {
            $sessionKey = session()->getId();
            $uploadPath = public_path("uploads/ordenes/temp_{$sessionKey}");
        }

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $extension = $archivo->getClientOriginalExtension();
        $timestamp = time();
        $uniqueId = uniqid();
        $fileName = "bosquejo_{$timestamp}_{$uniqueId}.{$extension}";
        $thumbName = "thumb_{$timestamp}_{$uniqueId}.{$extension}";

        // Guardar archivo original
        $archivo->move($uploadPath, $fileName);

        $relativePath = $ordenId
            ? "uploads/ordenes/{$ordenId}/bosquejos/{$fileName}"
            : "uploads/ordenes/temp_" . session()->getId() . "/{$fileName}";

        // Hacer cuadrada y generar miniatura
        $thumbRelative = $relativePath;
        try {
            ImageHelper::makeSquare("{$uploadPath}/{$fileName}");
            ImageHelper::makeSquareThumbnail("{$uploadPath}/{$fileName}", "{$uploadPath}/{$thumbName}");
            $thumbRelative = $ordenId
                ? "uploads/ordenes/{$ordenId}/bosquejos/{$thumbName}"
                : "uploads/ordenes/temp_" . session()->getId() . "/{$thumbName}";
        } catch (\Exception $e) {
            // Si falla thumbnail, usar original
        }

        return [
            'nombre' => $nombre ?: pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME),
            'tipo_origen' => $tipoOrigen,
            'ruta_archivo' => $relativePath,
            'ruta_miniatura' => $thumbRelative,
            'plantilla_bosquejo_id' => $plantillaId,
        ];
    }

    /**
     * Sube una imagen desde base64 (dibujo tablet / firma).
     */
    public function subirBase64ComoBosquejo(string $base64Data, string $tipoOrigen, ?int $ordenId = null, ?string $nombre = null): array
    {
        // Determinar carpeta destino
        if ($ordenId) {
            $uploadPath = public_path("uploads/ordenes/{$ordenId}/bosquejos");
        } else {
            $sessionKey = session()->getId();
            $uploadPath = public_path("uploads/ordenes/temp_{$sessionKey}");
        }

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Decodificar base64
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
        $imageData = base64_decode($imageData);

        $timestamp = time();
        $uniqueId = uniqid();
        $fileName = "bosquejo_{$timestamp}_{$uniqueId}.png";
        $thumbName = "thumb_{$timestamp}_{$uniqueId}.png";

        file_put_contents("{$uploadPath}/{$fileName}", $imageData);

        $relativePath = $ordenId
            ? "uploads/ordenes/{$ordenId}/bosquejos/{$fileName}"
            : "uploads/ordenes/temp_" . session()->getId() . "/{$fileName}";

        // Hacer cuadrada y generar miniatura
        $thumbRelative = $relativePath;
        try {
            ImageHelper::makeSquare("{$uploadPath}/{$fileName}");
            ImageHelper::makeSquareThumbnail("{$uploadPath}/{$fileName}", "{$uploadPath}/{$thumbName}");
            $thumbRelative = $ordenId
                ? "uploads/ordenes/{$ordenId}/bosquejos/{$thumbName}"
                : "uploads/ordenes/temp_" . session()->getId() . "/{$thumbName}";
        } catch (\Exception $e) {
            // Si falla thumbnail, usar original
        }

        return [
            'nombre' => $nombre ?: "Dibujo " . date('d/m/Y H:i'),
            'tipo_origen' => $tipoOrigen,
            'ruta_archivo' => $relativePath,
            'ruta_miniatura' => $thumbRelative,
            'plantilla_bosquejo_id' => null,
        ];
    }

    /**
     * Validacion server-side para generar orden.
     */
    public function validarParaGenerar(array $data): array
    {
        $errores = [];

        if (empty($data['cliente_id'])) {
            $errores[] = 'Debe seleccionar un cliente.';
        }

        if (empty($data['items']) || count($data['items']) === 0) {
            $errores[] = 'Debe agregar al menos un item.';
        } else {
            foreach ($data['items'] as $i => $item) {
                $num = $i + 1;
                if (empty($item['descripcion'])) {
                    $errores[] = "Item {$num}: falta descripcion.";
                }
                if (empty($item['cantidad']) || $item['cantidad'] <= 0) {
                    $errores[] = "Item {$num}: cantidad debe ser mayor a 0.";
                }
                if (!isset($item['precio_unitario']) || $item['precio_unitario'] < 0) {
                    $errores[] = "Item {$num}: precio no valido.";
                }
            }
        }

        $piezas = $data['piezas'] ?? [];
        $algunaRequiereOperario = collect($piezas)->contains(fn($p) => ($p['requiere_operario'] ?? true));
        if ($algunaRequiereOperario && empty($data['operario_id'])) {
            $errores[] = 'Debe seleccionar un operario cuando hay piezas que lo requieren.';
        }

        return $errores;
    }

    // ========================================
    // Metodos protegidos de sincronizacion
    // ========================================

    /**
     * Sincroniza items: delete-and-recreate.
     */
    protected function sincronizarItems(Orden $orden, array $items): void
    {
        $orden->items()->delete();

        foreach ($items as $item) {
            $cantidad = floatval($item['cantidad'] ?? 0);
            $precioUnitario = floatval($item['precio_unitario'] ?? 0);
            $porcentajeIva = floatval($item['porcentaje_iva'] ?? 19);
            $subtotal = $cantidad * $precioUnitario;
            $montoIva = $subtotal * ($porcentajeIva / 100);

            OrdenItem::create([
                'orden_id' => $orden->id,
                'catalogo_item_id' => $item['catalogo_item_id'] ?? null,
                'codigo' => $item['codigo'] ?? null,
                'descripcion' => $item['descripcion'] ?? '',
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'porcentaje_iva' => $porcentajeIva,
                'categoria' => $item['categoria'] ?? 'servicio',
                'subtotal' => $subtotal,
                'monto_iva' => $montoIva,
                'total' => $subtotal + $montoIva,
            ]);
        }
    }

    /**
     * Sincroniza bosquejos: mueve archivos de temp si es necesario, crea registros.
     * Retorna mapa [indice_array => db_id] para resolver referencias de piezas.
     */
    protected function sincronizarBosquejos(Orden $orden, array $bosquejos): array
    {
        // Obtener IDs existentes para comparar
        $existingIds = $orden->bosquejos()->pluck('id')->toArray();
        $keepIds = [];
        $indexToIdMap = [];

        $ordenPath = public_path("uploads/ordenes/{$orden->id}/bosquejos");
        if (!File::exists($ordenPath)) {
            File::makeDirectory($ordenPath, 0755, true);
        }

        foreach ($bosquejos as $index => $bosquejo) {
            // Si ya tiene ID, mantener el registro existente
            if (!empty($bosquejo['id'])) {
                $keepIds[] = $bosquejo['id'];
                $indexToIdMap[$index] = $bosquejo['id'];
                continue;
            }

            // Mover archivo de temp si es necesario
            $rutaArchivo = $bosquejo['ruta_archivo'] ?? '';
            $rutaMiniatura = $bosquejo['ruta_miniatura'] ?? $rutaArchivo;

            if (str_contains($rutaArchivo, 'temp_')) {
                $rutaArchivo = $this->moverArchivoDeTemp($rutaArchivo, $orden->id);
                if ($rutaMiniatura && str_contains($rutaMiniatura, 'temp_')) {
                    $rutaMiniatura = $this->moverArchivoDeTemp($rutaMiniatura, $orden->id);
                }
            }

            $registro = OrdenBosquejo::create([
                'orden_id' => $orden->id,
                'plantilla_bosquejo_id' => $bosquejo['plantilla_bosquejo_id'] ?? null,
                'tipo_origen' => $bosquejo['tipo_origen'] ?? 'archivo_local',
                'nombre' => $bosquejo['nombre'] ?? 'Bosquejo ' . ($index + 1),
                'ruta_archivo' => $rutaArchivo,
                'ruta_miniatura' => $rutaMiniatura,
                'orden_visual' => $index,
            ]);

            $keepIds[] = $registro->id;
            $indexToIdMap[$index] = $registro->id;
        }

        // Eliminar bosquejos que ya no estan en la lista
        $toDelete = array_diff($existingIds, $keepIds);
        if (!empty($toDelete)) {
            $oldBosquejos = OrdenBosquejo::whereIn('id', $toDelete)->get();
            foreach ($oldBosquejos as $old) {
                if ($old->ruta_archivo && File::exists(public_path($old->ruta_archivo))) {
                    File::delete(public_path($old->ruta_archivo));
                }
                if ($old->ruta_miniatura && $old->ruta_miniatura !== $old->ruta_archivo && File::exists(public_path($old->ruta_miniatura))) {
                    File::delete(public_path($old->ruta_miniatura));
                }
                $old->delete();
            }
        }

        return $indexToIdMap;
    }

    /**
     * Mueve un archivo de la carpeta temp a la carpeta final de la orden.
     */
    protected function moverArchivoDeTemp(string $rutaRelativa, int $ordenId): string
    {
        $fullPath = public_path($rutaRelativa);
        if (!File::exists($fullPath)) {
            return $rutaRelativa;
        }

        $fileName = basename($rutaRelativa);
        $destDir = public_path("uploads/ordenes/{$ordenId}/bosquejos");
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        File::move($fullPath, "{$destDir}/{$fileName}");

        return "uploads/ordenes/{$ordenId}/bosquejos/{$fileName}";
    }

    /**
     * Sincroniza piezas: delete-and-recreate.
     * Resuelve bosquejo_index usando el mapa de sincronizarBosquejos.
     */
    protected function sincronizarPiezas(Orden $orden, array $piezas, array $bosquejoMap = []): void
    {
        $orden->piezas()->delete();

        foreach ($piezas as $index => $pieza) {
            $letra = $this->obtenerLetraPieza($index);
            $nombre = $pieza['nombre'] ?? "Pieza {$letra}";
            $cantidad = intval($pieza['cantidad'] ?? 1);
            $material = $pieza['material'] ?? null;
            $calibre = $pieza['calibre'] ?? null;
            $notas = $pieza['notas'] ?? null;

            // Resolver bosquejo_index a DB ID usando el mapa
            $ordenBosquejoId = null;
            if (isset($pieza['bosquejo_index']) && $pieza['bosquejo_index'] !== null && $pieza['bosquejo_index'] !== '') {
                $bosquejoIndex = (int) $pieza['bosquejo_index'];
                $ordenBosquejoId = $bosquejoMap[$bosquejoIndex] ?? null;
            }
            // Fallback: si viene orden_bosquejo_id directo (compatibilidad)
            if (!$ordenBosquejoId && !empty($pieza['orden_bosquejo_id'])) {
                $ordenBosquejoId = $pieza['orden_bosquejo_id'];
            }

            // Auto-generar especificacion
            $partes = [$cantidad];
            if ($nombre) $partes[] = $nombre;
            if ($calibre) $partes[] = $calibre;
            if ($material) $partes[] = $material;
            $especificacion = implode(' - ', $partes);

            OrdenPieza::create([
                'orden_id' => $orden->id,
                'orden_bosquejo_id' => $ordenBosquejoId,
                'nombre' => $nombre,
                'nombre_automatico' => "Pieza {$letra}",
                'cantidad' => $cantidad,
                'material' => $material,
                'calibre' => $calibre,
                'especificacion' => $especificacion,
                'notas' => $notas,
                'porcentaje_avance' => 0,
                'estado' => 'pendiente',
                'requiere_operario' => (bool) ($pieza['requiere_operario'] ?? true),
                'orden_visual' => $index,
            ]);
        }
    }

    /**
     * Sincroniza pagos: delete-and-recreate.
     * Usa forceDelete() para no dejar pagos soft-deleted (que la UI mostraria como "rechazados").
     * El rechazo real de un pago se hace por ContabilidadController::rechazarPago.
     */
    protected function sincronizarPagos(Orden $orden, array $pagos, User $user): void
    {
        $orden->pagos()->forceDelete();

        $autoAprueba = $user->hasAnyRole(['Administrador', 'Contabilidad']);

        foreach ($pagos as $pago) {
            $monto = floatval($pago['monto'] ?? 0);
            if ($monto <= 0) continue;

            Pago::create([
                'orden_id' => $orden->id,
                'monto' => $monto,
                'metodo_pago' => $pago['metodo_pago'] ?? 'efectivo',
                'referencia_pago' => $pago['referencia_pago'] ?? null,
                'registrado_por' => $user->id,
                'aprobado' => $autoAprueba,
                'aprobado_por' => $autoAprueba ? $user->id : null,
            ]);
        }
    }

    /**
     * Guarda firma del cliente como imagen PNG.
     */
    protected function guardarFirma(Orden $orden, string $firmaBase64): string
    {
        $firmaPath = public_path("uploads/ordenes/{$orden->id}/firma");
        if (!File::exists($firmaPath)) {
            File::makeDirectory($firmaPath, 0755, true);
        }

        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $firmaBase64);
        $imageData = base64_decode($imageData);

        $fileName = "firma_" . time() . ".png";
        file_put_contents("{$firmaPath}/{$fileName}", $imageData);

        return "uploads/ordenes/{$orden->id}/firma/{$fileName}";
    }

    /**
     * Actualiza la asignacion de operario en piezas de una orden ya generada.
     * Se usa al editar ordenes que ya pasaron de borrador.
     */
    protected function actualizarAsignacionOperario(Orden $orden, int $operarioId, User $asignadoPor): void
    {
        $piezas = $orden->piezas()->where('requiere_operario', true)->get();

        foreach ($piezas as $pieza) {
            $pieza->update(['operario_actual_id' => $operarioId]);

            // Recrear asignacion (las anteriores se eliminaron por CASCADE al sincronizar piezas)
            AsignacionPieza::create([
                'orden_pieza_id' => $pieza->id,
                'orden_id' => $orden->id,
                'asignado_desde_id' => null,
                'asignado_a_id' => $operarioId,
                'asignado_por_id' => $asignadoPor->id,
                'tipo_asignacion' => 'inicial',
                'porcentaje_al_asignar' => $pieza->porcentaje_avance ?? 0,
                'activa' => true,
            ]);
        }

        // Piezas sin operario -> asegurar estado completada
        $orden->piezas()->where('requiere_operario', false)->update([
            'estado' => 'completada',
            'porcentaje_avance' => 100,
            'operario_actual_id' => null,
        ]);
    }

    /**
     * Crea asignaciones iniciales al generar orden.
     */
    protected function crearAsignacionesIniciales(Orden $orden, int $operarioId, User $asignadoPor, $piezas = null): void
    {
        $piezas = $piezas ?? $orden->piezas()->get();

        foreach ($piezas as $pieza) {
            AsignacionPieza::create([
                'orden_pieza_id' => $pieza->id,
                'orden_id' => $orden->id,
                'asignado_desde_id' => null,
                'asignado_a_id' => $operarioId,
                'asignado_por_id' => $asignadoPor->id,
                'tipo_asignacion' => 'inicial',
                'porcentaje_al_asignar' => 0,
                'activa' => true,
            ]);

            $pieza->update(['operario_actual_id' => $operarioId]);
        }
    }

    /**
     * Copia archivos de bosquejo de una orden a otra.
     */
    public function copiarArchivosBosquejo(OrdenBosquejo $bosquejo, int $nuevaOrdenId): array
    {
        $destDir = public_path("uploads/ordenes/{$nuevaOrdenId}/bosquejos");
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $rutaArchivo = $bosquejo->ruta_archivo;
        $rutaMiniatura = $bosquejo->ruta_miniatura;

        if ($rutaArchivo && File::exists(public_path($rutaArchivo))) {
            $ext = pathinfo($rutaArchivo, PATHINFO_EXTENSION);
            $newFile = uniqid('bosquejo_') . '.' . $ext;
            File::copy(public_path($rutaArchivo), "{$destDir}/{$newFile}");
            $rutaArchivo = "uploads/ordenes/{$nuevaOrdenId}/bosquejos/{$newFile}";
        }

        if ($rutaMiniatura && $rutaMiniatura !== $bosquejo->ruta_archivo && File::exists(public_path($rutaMiniatura))) {
            $ext = pathinfo($rutaMiniatura, PATHINFO_EXTENSION);
            $newThumb = uniqid('thumb_') . '.' . $ext;
            File::copy(public_path($rutaMiniatura), "{$destDir}/{$newThumb}");
            $rutaMiniatura = "uploads/ordenes/{$nuevaOrdenId}/bosquejos/{$newThumb}";
        } else {
            $rutaMiniatura = $rutaArchivo;
        }

        return [
            'ruta_archivo' => $rutaArchivo,
            'ruta_miniatura' => $rutaMiniatura,
        ];
    }

    /**
     * Obtiene la letra correspondiente al indice (A, B, C... Z, AA, AB...).
     */
    protected function obtenerLetraPieza(int $index): string
    {
        $letra = '';
        $index++;
        while ($index > 0) {
            $index--;
            $letra = chr(65 + ($index % 26)) . $letra;
            $index = intdiv($index, 26);
        }
        return $letra;
    }
}
