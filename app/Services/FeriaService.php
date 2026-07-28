<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Feria;
use App\Models\ItemTrasladoStock;
use App\Models\ListaPrecio;
use App\Models\MovimientoStock;
use App\Models\PrecioProducto;
use App\Models\PrecioVariante;
use App\Models\Producto;
use App\Models\StockProducto;
use App\Models\TrasladoStock;
use App\Models\VarianteProducto;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class FeriaService
{
    /**
     * Crea una feria completa: ubicación (nueva o existente) + lista de precios propia
     * (copiada de una lista base, sin tocar las listas regulares) + caja de POS.
     *
     * @param array $datos nombre, fecha_inicio, fecha_fin, notas, lista_precio_base_id,
     *                     ubicacion_modo ('nueva'|'existente'), ubicacion_id, ubicacion_nombre
     */
    public function crearFeria(array $datos, int $usuarioId): Feria
    {
        return DB::transaction(function () use ($datos, $usuarioId) {
            // 1. Ubicación de la feria
            if (($datos['ubicacion_modo'] ?? 'nueva') === 'existente') {
                $ubicacion = Ubicacion::findOrFail($datos['ubicacion_id']);
            } else {
                $ubicacion = Ubicacion::create([
                    'nombre' => $datos['ubicacion_nombre'] ?: ('Feria ' . $datos['nombre']),
                    'codigo' => $this->codigoUnico('ubicaciones', 'FERIA'),
                    'tipo' => Ubicacion::TIPO_TIENDA,
                    'es_principal' => false,
                    'activo' => true,
                ]);
            }

            // 2. Lista de precios propia, copiada de la base
            $listaBaseId = $datos['lista_precio_base_id'];
            $listaBase = ListaPrecio::findOrFail($listaBaseId);

            $listaFeria = ListaPrecio::create([
                'nombre' => 'Feria: ' . $datos['nombre'],
                'codigo' => $this->codigoUnico('listas_precios', 'feria'),
                'descripcion' => 'Lista de precios de la feria «' . $datos['nombre'] . '» (copiada de ' . $listaBase->nombre . ').',
                'activo' => true,
                'orden' => 99,
            ]);

            $this->copiarPreciosLista($listaBaseId, $listaFeria->id);

            // 3. Caja de POS de la feria (apunta a la ubicación y a la lista de la feria)
            $caja = Caja::create([
                'nombre' => 'Caja Feria ' . $datos['nombre'],
                'codigo' => $this->codigoUnico('cajas', 'FER'),
                'ubicacion_id' => $ubicacion->id,
                'lista_precio_id' => $listaFeria->id,
                'estado' => 'cerrada',
                'activo' => true,
            ]);

            // 4. La feria
            $feria = Feria::create([
                'nombre' => $datos['nombre'],
                'ubicacion_id' => $ubicacion->id,
                'lista_precio_id' => $listaFeria->id,
                'lista_precio_base_id' => $listaBaseId,
                'caja_id' => $caja->id,
                'fecha_inicio' => $datos['fecha_inicio'] ?? null,
                'fecha_fin' => $datos['fecha_fin'] ?? null,
                'estado' => Feria::ESTADO_BORRADOR,
                'notas' => $datos['notas'] ?? null,
                'created_by' => $usuarioId,
            ]);

            return $feria;
        });
    }

    /**
     * Copia todos los precios (productos y ajustes de variante) de una lista a otra.
     * INSERT ... SELECT para que sea rápido; la lista destino está vacía (recién creada).
     */
    public function copiarPreciosLista(int $listaBaseId, int $listaDestinoId): void
    {
        DB::insert(
            'INSERT INTO precios_productos (producto_id, lista_precio_id, precio, activo, created_at, updated_at)
             SELECT producto_id, ?, precio, activo, NOW(), NOW() FROM precios_productos WHERE lista_precio_id = ?',
            [$listaDestinoId, $listaBaseId]
        );

        DB::insert(
            'INSERT INTO precios_variantes (variante_producto_id, lista_precio_id, ajuste_precio, activo, created_at, updated_at)
             SELECT variante_producto_id, ?, ajuste_precio, activo, NOW(), NOW() FROM precios_variantes WHERE lista_precio_id = ?',
            [$listaDestinoId, $listaBaseId]
        );
    }

    /**
     * Preparar inventario de la feria: mueve productos desde la Bodega Principal (CEDI)
     * a la ubicación de la feria, en UN solo paso (traslado auto-enviado y recibido),
     * de modo que el stock queda listo en el stand de inmediato.
     */
    public function prepararInventario(Feria $feria, array $items, int $usuarioId): TrasladoStock
    {
        $bodega = $this->bodegaPrincipal();

        return $this->moverInventario(
            $bodega->id,
            $feria->ubicacion_id,
            $items,
            $usuarioId,
            'Preparación de inventario para la feria: ' . $feria->nombre
        );
    }

    /**
     * Devolver una línea de inventario del stand de la feria de vuelta a la Bodega Principal.
     */
    public function devolverInventario(Feria $feria, array $item, int $usuarioId): TrasladoStock
    {
        $bodega = $this->bodegaPrincipal();

        return $this->moverInventario(
            $feria->ubicacion_id,
            $bodega->id,
            [$item],
            $usuarioId,
            'Devolución de inventario de la feria: ' . $feria->nombre
        );
    }

    /**
     * Mueve inventario entre dos ubicaciones en un solo paso, dejando un traslado
     * COMPLETADO para trazabilidad + los movimientos de salida (origen) y entrada (destino).
     * En el origen se descuenta SOLO lo disponible (no toca reservas de cotizaciones).
     *
     * @param array $items cada uno: producto_id, variante_producto_id (opcional), cantidad
     */
    public function moverInventario(int $origenId, int $destinoId, array $items, int $usuarioId, string $notas): TrasladoStock
    {
        return DB::transaction(function () use ($origenId, $destinoId, $items, $usuarioId, $notas) {
            $origen = Ubicacion::findOrFail($origenId);

            // 1. Validar disponibilidad real en el origen (disponible - reservado).
            $errores = [];
            foreach ($items as $it) {
                $varId = !empty($it['variante_producto_id']) ? (int) $it['variante_producto_id'] : null;
                $cant = (int) $it['cantidad'];
                $stock = $this->stockDe($it['producto_id'], $varId, $origenId, true);
                $disp = $stock ? ($stock->cantidad_disponible - $stock->cantidad_reservada) : 0;
                if ($disp < $cant) {
                    $nombre = Producto::find($it['producto_id'])?->nombre ?? ('ID ' . $it['producto_id']);
                    $errores[] = "Stock insuficiente en {$origen->nombre} para {$nombre}: disponible {$disp}, solicitado {$cant}.";
                }
            }
            if ($errores) {
                throw new RuntimeException(implode(' ', $errores));
            }

            // 2. Traslado registrado como completado (envío + recepción inmediatos).
            $traslado = TrasladoStock::create([
                'numero_traslado' => TrasladoStock::generarNumeroTraslado(),
                'ubicacion_origen_id' => $origenId,
                'ubicacion_destino_id' => $destinoId,
                'estado' => TrasladoStock::ESTADO_COMPLETADO,
                'tipo_operacion' => TrasladoStock::TIPO_OPERACION_GENERAL,
                'notas' => $notas,
                'usuario_creador_id' => $usuarioId,
                'usuario_receptor_id' => $usuarioId,
                'enviado_en' => now(),
                'recibido_en' => now(),
            ]);

            // 3. Por cada línea: salida en origen (solo disponible) + entrada en destino.
            foreach ($items as $it) {
                $varId = !empty($it['variante_producto_id']) ? (int) $it['variante_producto_id'] : null;
                $cant = (int) $it['cantidad'];

                ItemTrasladoStock::create([
                    'traslado_stock_id' => $traslado->id,
                    'producto_id' => $it['producto_id'],
                    'variante_producto_id' => $varId,
                    'cantidad' => $cant,
                ]);

                // Salida del origen: descuenta disponible SIN tocar cantidad_reservada.
                $stockOrigen = $this->stockDe($it['producto_id'], $varId, $origenId, true);
                $antes = $stockOrigen->cantidad_disponible;
                $stockOrigen->cantidad_disponible -= $cant;
                $stockOrigen->save();
                MovimientoStock::create([
                    'producto_id' => $it['producto_id'],
                    'variante_producto_id' => $varId,
                    'ubicacion_id' => $origenId,
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $cant,
                    'stock_anterior' => $antes,
                    'stock_nuevo' => $stockOrigen->cantidad_disponible,
                    'referencia_documento' => $traslado->numero_traslado,
                    'origen' => 'traslado',
                    'motivo' => $notas,
                    'usuario_id' => $usuarioId,
                ]);

                // Entrada en destino (crea el registro si no existe).
                $stockDestino = StockProducto::firstOrCreate(
                    ['producto_id' => $it['producto_id'], 'variante_producto_id' => $varId, 'ubicacion_id' => $destinoId],
                    ['cantidad_disponible' => 0, 'cantidad_reservada' => 0, 'stock_minimo' => 0, 'alerta_stock_bajo' => true]
                );
                $stockDestino->entrada($cant, 'traslado', $traslado->numero_traslado, $notas);
            }

            return $traslado;
        });
    }

    /**
     * Precio ACTUAL de un producto/variante en la lista de la feria.
     */
    public function precioActualFeria(Feria $feria, int $productoId, ?int $varianteId): ?float
    {
        $listaId = $feria->lista_precio_id;

        if ($varianteId) {
            $v = VarianteProducto::find($varianteId);
            return $v ? $v->getPrecioFinal($listaId) : null;
        }

        $p = Producto::find($productoId);
        return $p ? $p->getPrecioPorLista($listaId) : null;
    }

    /**
     * Fija el precio ABSOLUTO de un producto/variante en la lista de la feria.
     * Para variantes, guarda el ajuste (= precio − precio base del producto en esa lista).
     */
    public function fijarPrecioFeria(Feria $feria, int $productoId, ?int $varianteId, float $precio): void
    {
        $listaId = $feria->lista_precio_id;
        $precio = round($precio, 2);

        if ($varianteId) {
            $producto = Producto::findOrFail($productoId);
            $precioBase = $producto->getPrecioPorLista($listaId);
            if ($precioBase === null) {
                PrecioProducto::updateOrCreate(
                    ['producto_id' => $productoId, 'lista_precio_id' => $listaId],
                    ['precio' => $precio, 'activo' => true]
                );
                $precioBase = $precio;
            }
            PrecioVariante::updateOrCreate(
                ['variante_producto_id' => $varianteId, 'lista_precio_id' => $listaId],
                ['ajuste_precio' => round($precio - $precioBase, 2), 'activo' => true]
            );
        } else {
            PrecioProducto::updateOrCreate(
                ['producto_id' => $productoId, 'lista_precio_id' => $listaId],
                ['precio' => $precio, 'activo' => true]
            );
        }
    }

    /**
     * F2 — Precios masivos / promociones: aplica a un conjunto de productos/variantes de la
     * feria un precio fijo, un descuento % o un aumento %, calculado sobre el precio actual
     * de la feria. Devuelve el detalle [producto_id, variante_producto_id, precio] aplicado.
     *
     * @param array  $items cada uno: producto_id, variante_producto_id (opcional)
     * @param string $tipo  'fijo' | 'descuento_pct' | 'aumento_pct'
     */
    public function aplicarPreciosMasivos(Feria $feria, array $items, string $tipo, float $valor): array
    {
        return DB::transaction(function () use ($feria, $items, $tipo, $valor) {
            $aplicados = [];

            foreach ($items as $it) {
                $productoId = (int) $it['producto_id'];

                // "Todos los tonos": aplica la operación a CADA tono según su propio precio actual.
                // Si vienen `variante_ids` (los tonos cargados en el stand) se limita a esos;
                // si no, cae al comportamiento previo (todas las variantes del producto).
                if (!empty($it['todas_variantes'])) {
                    $idsTonos = !empty($it['variante_ids']) && is_array($it['variante_ids'])
                        ? array_values(array_unique(array_map('intval', $it['variante_ids'])))
                        : Producto::with('variantes')->find($productoId)?->variantes->pluck('id')->all() ?? [];

                    if (!empty($idsTonos)) {
                        foreach ($idsTonos as $varId) {
                            $actual = $this->precioActualFeria($feria, $productoId, $varId) ?? 0;
                            $nuevo = $this->calcularPrecioNuevo($tipo, $valor, $actual);
                            $this->fijarPrecioFeria($feria, $productoId, $varId, $nuevo);
                            $aplicados[] = ['producto_id' => $productoId, 'variante_producto_id' => $varId, 'precio' => $nuevo];
                        }
                        continue;
                    }
                }

                $varianteId = !empty($it['variante_producto_id']) ? (int) $it['variante_producto_id'] : null;
                $actual = $this->precioActualFeria($feria, $productoId, $varianteId) ?? 0;
                $nuevo = $this->calcularPrecioNuevo($tipo, $valor, $actual);
                $this->fijarPrecioFeria($feria, $productoId, $varianteId, $nuevo);
                $aplicados[] = ['producto_id' => $productoId, 'variante_producto_id' => $varianteId, 'precio' => $nuevo];
            }

            return $aplicados;
        });
    }

    private function calcularPrecioNuevo(string $tipo, float $valor, float $actual): float
    {
        $nuevo = match ($tipo) {
            'fijo' => $valor,
            'descuento_pct' => $actual * (1 - $valor / 100),
            'aumento_pct' => $actual * (1 + $valor / 100),
            default => $actual,
        };

        return max(0, round($nuevo, 2));
    }

    /**
     * Devuelve TODO el inventario disponible del stand a la Bodega Principal en un solo
     * traslado (para el cierre de la feria).
     */
    public function devolverTodoInventario(Feria $feria, int $usuarioId): TrasladoStock
    {
        $bodega = $this->bodegaPrincipal();

        $items = StockProducto::where('ubicacion_id', $feria->ubicacion_id)
            ->whereRaw('(cantidad_disponible - cantidad_reservada) > 0')
            ->get()
            ->map(fn($s) => [
                'producto_id' => $s->producto_id,
                'variante_producto_id' => $s->variante_producto_id,
                'cantidad' => max(0, $s->cantidad_disponible - $s->cantidad_reservada),
            ])
            ->filter(fn($i) => $i['cantidad'] > 0)
            ->values()
            ->all();

        if (empty($items)) {
            throw new RuntimeException('El stand no tiene inventario disponible para devolver.');
        }

        return $this->moverInventario(
            $feria->ubicacion_id,
            $bodega->id,
            $items,
            $usuarioId,
            'Devolución TOTAL de inventario de la feria: ' . $feria->nombre
        );
    }

    /**
     * Cierra la feria con CONTEO FÍSICO: por cada producto se indica cuánto regresó
     * realmente. Devuelve lo contado al CEDI (traslado) y registra la diferencia
     * (lo que faltó por robo/novedad/daño) como MERMA (salida origen 'novedad'),
     * dejando el stand en 0 y cuadrado. Luego marca la feria como cerrada.
     *
     * @param array $conteos cada uno: producto_id, variante_producto_id (opcional), cantidad_fisica
     */
    public function cerrarConConteo(Feria $feria, array $conteos, int $usuarioId): array
    {
        return DB::transaction(function () use ($feria, $conteos, $usuarioId) {
            $ubic = $feria->ubicacion_id;

            $conteoMap = collect($conteos)->keyBy(
                fn($c) => $c['producto_id'] . '|' . (!empty($c['variante_producto_id']) ? $c['variante_producto_id'] : '')
            );

            $stocks = StockProducto::where('ubicacion_id', $ubic)
                ->where('cantidad_disponible', '>', 0)
                ->get();

            $devolver = [];
            $planMermas = [];
            foreach ($stocks as $s) {
                $key = $s->producto_id . '|' . ($s->variante_producto_id ?? '');
                $disp = (int) $s->cantidad_disponible;
                $fisico = isset($conteoMap[$key]) ? (int) $conteoMap[$key]['cantidad_fisica'] : 0;
                $fisico = max(0, min($fisico, $disp)); // entre 0 y lo que dice el sistema

                if ($fisico > 0) {
                    $devolver[] = ['producto_id' => $s->producto_id, 'variante_producto_id' => $s->variante_producto_id, 'cantidad' => $fisico];
                }
                $merma = $disp - $fisico;
                if ($merma > 0) {
                    $planMermas[] = ['id' => $s->id, 'cantidad' => $merma];
                }
            }

            // 1) Devolver al CEDI SOLO lo que realmente regresó.
            $traslado = null;
            if (!empty($devolver)) {
                $bodega = $this->bodegaPrincipal();
                $traslado = $this->moverInventario($ubic, $bodega->id, $devolver, $usuarioId, 'Cierre de feria (conteo físico): ' . $feria->nombre);
            }

            // 2) Registrar el faltante como MERMA (salida por novedad) -> stand queda en 0.
            $totalMerma = 0;
            foreach ($planMermas as $pm) {
                $stock = StockProducto::find($pm['id']);
                if ($stock) {
                    $stock->salida($pm['cantidad'], 'novedad', 'MERMA-FERIA-' . $feria->id, 'Faltante/merma al cerrar la feria: ' . $feria->nombre);
                    $totalMerma += $pm['cantidad'];
                }
            }

            // 3) Cerrar la feria.
            $feria->update(['estado' => Feria::ESTADO_CERRADA]);

            return [
                'traslado' => $traslado,
                'total_devuelto' => collect($devolver)->sum('cantidad'),
                'total_merma' => $totalMerma,
            ];
        });
    }

    /**
     * Recibe en la feria un traslado que venía EN TRÁNSITO hacia su ubicación: suma el stock
     * al stand (entrada + movimiento) y marca el traslado como completado.
     */
    public function recibirTraslado(TrasladoStock $traslado, int $usuarioId): void
    {
        if ($traslado->estado !== TrasladoStock::ESTADO_EN_TRANSITO) {
            throw new RuntimeException('El traslado no está en tránsito, no se puede recibir.');
        }

        DB::transaction(function () use ($traslado, $usuarioId) {
            $destinoId = $traslado->ubicacion_destino_id;

            foreach ($traslado->items as $item) {
                $stock = StockProducto::firstOrCreate(
                    ['producto_id' => $item->producto_id, 'variante_producto_id' => $item->variante_producto_id, 'ubicacion_id' => $destinoId],
                    ['cantidad_disponible' => 0, 'cantidad_reservada' => 0, 'stock_minimo' => 0, 'alerta_stock_bajo' => true]
                );
                $stock->entrada($item->cantidad, 'traslado', $traslado->numero_traslado, 'Recepción de traslado en feria');
            }

            $traslado->completar($usuarioId);
        });
    }

    private function bodegaPrincipal(): Ubicacion
    {
        return Ubicacion::where('tipo', Ubicacion::TIPO_BODEGA)
            ->where('es_principal', true)
            ->where('activo', true)
            ->firstOrFail();
    }

    private function stockDe(int $productoId, ?int $varianteId, int $ubicacionId, bool $lock = false): ?StockProducto
    {
        $q = StockProducto::where('producto_id', $productoId)
            ->where('ubicacion_id', $ubicacionId)
            ->when($varianteId, fn($x) => $x->where('variante_producto_id', $varianteId), fn($x) => $x->whereNull('variante_producto_id'));

        if ($lock) {
            $q->lockForUpdate();
        }

        return $q->first();
    }

    /**
     * Genera un código único para una tabla dada, con prefijo.
     */
    private function codigoUnico(string $tabla, string $prefijo): string
    {
        do {
            $codigo = $prefijo . '-' . strtoupper(Str::random(6));
        } while (DB::table($tabla)->where('codigo', $codigo)->exists());

        return $codigo;
    }
}
