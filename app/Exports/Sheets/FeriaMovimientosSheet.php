<?php

namespace App\Exports\Sheets;

use App\Models\Feria;
use App\Models\MovimientoStock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Hoja 2 del Excel de la feria: cada movimiento del stand con su FECHA Y HORA exacta
 * (cargues desde el CEDI, ventas del POS, devoluciones, ajustes).
 */
class FeriaMovimientosSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private Feria $feria;

    public function __construct(Feria $feria)
    {
        $this->feria = $feria;
    }

    public function collection(): Collection
    {
        $desde = $this->feria->created_at;

        return MovimientoStock::with(['producto', 'variante', 'usuario'])
            ->where('ubicacion_id', $this->feria->ubicacion_id)
            ->when($desde, fn($q) => $q->where('created_at', '>=', $desde))
            ->orderBy('created_at')
            ->get()
            ->map(function ($m) {
                $tipo = match ($m->tipo_movimiento) {
                    'entrada' => 'Entrada',
                    'salida' => 'Salida',
                    'ajuste' => 'Ajuste',
                    'reserva' => 'Reserva',
                    'liberacion' => 'Liberación',
                    default => $m->tipo_movimiento,
                };
                $origen = match ($m->origen) {
                    'traslado' => 'Traslado (CEDI ↔ stand)',
                    'venta' => 'Venta POS',
                    'ajuste_inventario' => 'Ajuste de inventario',
                    default => $m->origen,
                };
                $producto = $m->producto?->nombre ?? ('ID ' . $m->producto_id);
                if ($m->variante && $m->variante->nombre_variante) {
                    $producto .= ' — ' . $m->variante->nombre_variante;
                }

                return [
                    'fecha_hora' => $m->created_at?->format('d/m/Y H:i:s'),
                    'tipo' => $tipo,
                    'origen' => $origen,
                    'producto' => $producto,
                    'cantidad' => (int) $m->cantidad,
                    'stock_anterior' => (int) $m->stock_anterior,
                    'stock_nuevo' => (int) $m->stock_nuevo,
                    'referencia' => $m->referencia_documento ?? '',
                    'usuario' => $m->usuario?->name ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Fecha y hora',
            'Tipo',
            'Origen',
            'Producto',
            'Cantidad',
            'Stock anterior',
            'Stock nuevo',
            'Referencia',
            'Usuario',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    public function title(): string
    {
        return 'Movimientos';
    }
}
