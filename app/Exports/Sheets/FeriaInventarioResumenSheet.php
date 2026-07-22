<?php

namespace App\Exports\Sheets;

use App\Models\Feria;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\StockProducto;
use App\Models\VarianteProducto;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Hoja 1 del Excel de la feria: cuadre por producto/tono — CARGADO al stand,
 * VENDIDO, DEVUELTO al CEDI y STOCK ACTUAL. (Movimientos desde la creación de la feria.)
 */
class FeriaInventarioResumenSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private Feria $feria;

    public function __construct(Feria $feria)
    {
        $this->feria = $feria;
    }

    public function collection(): Collection
    {
        $ubicId = $this->feria->ubicacion_id;
        $desde = $this->feria->created_at;

        $movs = MovimientoStock::selectRaw("
                producto_id, variante_producto_id,
                SUM(CASE WHEN tipo_movimiento='entrada' AND origen='traslado' THEN cantidad ELSE 0 END) AS cargado,
                SUM(CASE WHEN tipo_movimiento='salida'  AND origen='venta'    THEN cantidad ELSE 0 END) AS vendido,
                SUM(CASE WHEN tipo_movimiento='salida'  AND origen='traslado' THEN cantidad ELSE 0 END) AS devuelto
            ")
            ->where('ubicacion_id', $ubicId)
            ->when($desde, fn($q) => $q->where('created_at', '>=', $desde))
            ->groupBy('producto_id', 'variante_producto_id')
            ->get()
            ->keyBy(fn($m) => $m->producto_id . '|' . ($m->variante_producto_id ?? ''));

        $stocks = StockProducto::where('ubicacion_id', $ubicId)
            ->get()
            ->keyBy(fn($s) => $s->producto_id . '|' . ($s->variante_producto_id ?? ''));

        $keys = $movs->keys()->merge($stocks->keys())->unique();

        $rows = $keys->map(function ($key) use ($movs, $stocks) {
            [$pid, $vid] = array_pad(explode('|', $key, 2), 2, '');
            $m = $movs->get($key);
            $s = $stocks->get($key);
            return [
                'producto_id' => (int) $pid,
                'variante_producto_id' => $vid !== '' ? (int) $vid : null,
                'cargado' => (int) ($m->cargado ?? 0),
                'vendido' => (int) ($m->vendido ?? 0),
                'devuelto' => (int) ($m->devuelto ?? 0),
                'actual' => (int) ($s->cantidad_disponible ?? 0),
            ];
        })->filter(fn($r) => $r['cargado'] || $r['vendido'] || $r['devuelto'] || $r['actual']);

        $productos = Producto::whereIn('id', $rows->pluck('producto_id')->unique())->get()->keyBy('id');
        $varIds = $rows->pluck('variante_producto_id')->filter()->unique();
        $variantes = $varIds->isNotEmpty()
            ? VarianteProducto::whereIn('id', $varIds)->get()->keyBy('id')
            : collect();

        return $rows->map(function ($r) use ($productos, $variantes) {
            $p = $productos->get($r['producto_id']);
            $v = $r['variante_producto_id'] ? $variantes->get($r['variante_producto_id']) : null;
            return [
                'referencia' => $v?->referencia_variante ?: ($p?->referencia ?? ''),
                'producto' => $p?->nombre ?? ('ID ' . $r['producto_id']),
                'variante' => $v?->nombre_variante ?? '',
                'cargado' => $r['cargado'],
                'vendido' => $r['vendido'],
                'devuelto' => $r['devuelto'],
                'actual' => $r['actual'],
            ];
        })->sortBy([['producto', 'asc'], ['variante', 'asc']])->values();
    }

    public function headings(): array
    {
        return [
            'Referencia',
            'Producto',
            'Variante / Tono',
            'Cargado al stand',
            'Vendido',
            'Devuelto al CEDI',
            'Stock actual en el stand',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    public function title(): string
    {
        return 'Resumen';
    }
}
