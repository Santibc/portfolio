<?php

namespace App\Exports;

use App\Models\StockProducto;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Exporta el stock a Excel, una fila por registro (producto/variante/ubicación),
 * respetando los filtros de ubicación, producto y estado de stock.
 */
class StockPorUbicacionExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /** @var array{ubicacion_id:?int, producto_id:?int, estado:?string} */
    private array $filtros;

    public function __construct(array $filtros = [])
    {
        $this->filtros = $filtros;
    }

    public function query()
    {
        $q = StockProducto::query()
            ->with(['producto', 'variante', 'ubicacionRelacion'])
            ->whereHas('producto', fn($p) => $p->where('eliminado', false));

        if (!empty($this->filtros['ubicacion_id'])) {
            $q->where('ubicacion_id', $this->filtros['ubicacion_id']);
        }

        if (!empty($this->filtros['producto_id'])) {
            $q->where('producto_id', $this->filtros['producto_id']);
        }

        switch ($this->filtros['estado'] ?? null) {
            case 'con_stock':
                $q->whereRaw('(cantidad_disponible - cantidad_reservada) > 0');
                break;
            case 'sin_stock':
                $q->whereRaw('(cantidad_disponible - cantidad_reservada) <= 0');
                break;
            case 'stock_bajo':
                $q->whereRaw('(cantidad_disponible - cantidad_reservada) <= stock_minimo')
                  ->where('alerta_stock_bajo', true);
                break;
        }

        return $q->orderBy('producto_id')->orderBy('variante_producto_id')->orderBy('ubicacion_id');
    }

    public function headings(): array
    {
        return [
            'Referencia',
            'Producto',
            'Variante',
            'Ubicación',
            'Tipo',
            'Disponible',
            'Reservado',
            'Stock Real',
            'Stock Mínimo',
            'Estado',
        ];
    }

    /**
     * @param StockProducto $s
     */
    public function map($s): array
    {
        $disponible = (int) $s->cantidad_disponible;
        $reservado = (int) $s->cantidad_reservada;
        $real = $disponible - $reservado;

        if ($real <= 0) {
            $estado = 'Sin stock';
        } elseif ($s->alerta_stock_bajo && $real <= $s->stock_minimo) {
            $estado = 'Stock bajo';
        } else {
            $estado = 'Con stock';
        }

        return [
            $s->producto->referencia ?? '',
            $s->producto->nombre ?? '',
            $s->variante->nombre_variante ?? '',
            $s->ubicacionRelacion->nombre ?? 'Sin ubicación',
            $s->ubicacionRelacion ? ucfirst($s->ubicacionRelacion->tipo) : '',
            $disponible,
            $reservado,
            $real,
            (int) $s->stock_minimo,
            $estado,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
