<?php

namespace App\Exports;

use App\Models\SolicitudCotizacion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * Exportación de solicitudes con múltiples hojas y filtros avanzados
 */
class SolicitudesExport implements WithMultipleSheets
{
    protected $solicitudes;
    protected ?string $estado;
    protected ?int $vendedorId;
    protected ?Carbon $fechaDesde;
    protected ?Carbon $fechaHasta;

    public function __construct(
        $solicitudes = null,
        ?string $estado = null,
        ?int $vendedorId = null,
        ?string $fechaDesde = null,
        ?string $fechaHasta = null
    ) {
        $this->estado = $estado;
        $this->vendedorId = $vendedorId;
        $this->fechaDesde = $fechaDesde ? Carbon::parse($fechaDesde)->startOfDay() : null;
        $this->fechaHasta = $fechaHasta ? Carbon::parse($fechaHasta)->endOfDay() : null;

        // Si se pasan solicitudes directamente, usarlas
        if ($solicitudes !== null) {
            $this->solicitudes = $solicitudes;
        } else {
            // Construir query con filtros
            $this->solicitudes = $this->buildQuery();
        }
    }

    /**
     * Construir query con filtros
     */
    protected function buildQuery()
    {
        $query = SolicitudCotizacion::with([
            'cliente.ciudad',
            'cliente.pais',
            'cliente.vendedor',
            'cliente.listaPrecio',
            'items',
            'aplicadaPor',
            'createdBy'
        ]);

        if ($this->estado) {
            $query->where('estado', $this->estado);
        }

        if ($this->vendedorId) {
            $query->where('created_by', $this->vendedorId);
        }

        if ($this->fechaDesde) {
            $query->where('created_at', '>=', $this->fechaDesde);
        }

        if ($this->fechaHasta) {
            $query->where('created_at', '<=', $this->fechaHasta);
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function sheets(): array
    {
        return [
            new ResumenSheet($this->solicitudes),
            new DetalleSheet($this->solicitudes),
            new ProductosSheet($this->solicitudes)
        ];
    }
}

/**
 * Hoja 1: Resumen de Solicitudes con colores por estado
 */
class ResumenSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    protected $solicitudes;

    public function __construct($solicitudes)
    {
        $this->solicitudes = $solicitudes;
    }

    public function collection()
    {
        return $this->solicitudes;
    }

    public function map($solicitud): array
    {
        return [
            $solicitud->numero_solicitud,
            $solicitud->created_at->format('d/m/Y H:i'),
            $solicitud->cliente->numero_identificacion ?? '',
            $solicitud->cliente->nombre_contacto ?? '',
            $solicitud->cliente->email ?? '',
            $solicitud->cliente->telefono ?? '',
            ($solicitud->cliente->ciudad->nombre ?? '') . ', ' . ($solicitud->cliente->pais->nombre ?? ''),
            $solicitud->createdBy->name ?? $solicitud->cliente->vendedor->name ?? '',
            $solicitud->cliente->listaPrecio?->nombre ?? 'Sin lista',
            $solicitud->total_items,
            $solicitud->monto_total,
            $solicitud->estado,
            $solicitud->estado === 'aplicada' && $solicitud->aplicada_en ? $solicitud->aplicada_en->format('d/m/Y H:i') : '',
            $solicitud->aplicadaPor?->name ?? '',
            $solicitud->notas_cliente ?? '',
            $solicitud->observaciones_admin ?? ''
        ];
    }

    public function headings(): array
    {
        return [
            'Nº Solicitud',
            'Fecha Solicitud',
            'NIT/CC Cliente',
            'Nombre Cliente',
            'Email Cliente',
            'Teléfono Cliente',
            'Ciudad',
            'Vendedor',
            'Lista de Precios',
            'Total Items',
            'Monto Total',
            'Estado',
            'Fecha Aplicación',
            'Aplicada Por',
            'Notas Cliente',
            'Observaciones Admin'
        ];
    }

    public function title(): string
    {
        return 'Resumen Solicitudes';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF84D5'] // Miracle Pink
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Colorear filas según estado
                for ($row = 2; $row <= $lastRow; $row++) {
                    $estado = $sheet->getCell("L{$row}")->getValue();

                    $bgColor = 'FFFFFF';
                    switch ($estado) {
                        case 'pendiente':
                            $bgColor = 'FFF3CD'; // Amarillo - pendiente
                            break;
                        case 'aplicada':
                            $bgColor = 'D4EDDA'; // Verde - aplicada
                            break;
                        case 'rechazada':
                            $bgColor = 'F8D7DA'; // Rojo - rechazada
                            break;
                    }

                    $sheet->getStyle("A{$row}:P{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $bgColor],
                        ],
                    ]);
                }

                // Formato de moneda para columna K (Monto Total)
                $sheet->getStyle("K2:K{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0');

                // Bordes
                $sheet->getStyle("A1:P{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Congelar encabezado
                $sheet->freezePane('A2');
            },
        ];
    }
}

/**
 * Hoja 2: Detalle de Items por Solicitud
 */
class DetalleSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    protected $solicitudes;

    public function __construct($solicitudes)
    {
        $this->solicitudes = $solicitudes;
    }

    public function collection()
    {
        $detalles = collect();

        foreach ($this->solicitudes as $solicitud) {
            foreach ($solicitud->items as $item) {
                $detalles->push([
                    'solicitud' => $solicitud,
                    'item' => $item
                ]);
            }
        }

        return $detalles;
    }

    public function map($row): array
    {
        $solicitud = $row['solicitud'];
        $item = $row['item'];

        return [
            $solicitud->numero_solicitud,
            $solicitud->created_at->format('d/m/Y'),
            $solicitud->cliente->nombre_contacto ?? '',
            $item->referencia_producto,
            $item->nombre_producto,
            $item->info_variante ?? '-',
            $item->cantidad,
            $item->precio_unitario,
            $item->precio_total,
            $solicitud->estado
        ];
    }

    public function headings(): array
    {
        return [
            'Nº Solicitud',
            'Fecha',
            'Cliente',
            'Referencia',
            'Producto',
            'Variante',
            'Cantidad',
            'Precio Unit.',
            'Subtotal',
            'Estado'
        ];
    }

    public function title(): string
    {
        return 'Detalle Items';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'BCA9F5'] // Miracle Lilac
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Colorear filas según estado
                for ($row = 2; $row <= $lastRow; $row++) {
                    $estado = $sheet->getCell("J{$row}")->getValue();

                    $bgColor = 'FFFFFF';
                    switch ($estado) {
                        case 'pendiente':
                            $bgColor = 'FFF3CD';
                            break;
                        case 'aplicada':
                            $bgColor = 'D4EDDA';
                            break;
                        case 'rechazada':
                            $bgColor = 'F8D7DA';
                            break;
                    }

                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $bgColor],
                        ],
                    ]);
                }

                // Formato de moneda
                $sheet->getStyle("H2:I{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0');

                // Bordes
                $sheet->getStyle("A1:J{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Congelar encabezado
                $sheet->freezePane('A2');
            },
        ];
    }
}

/**
 * Hoja 3: Resumen de Productos más solicitados
 */
class ProductosSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    protected $solicitudes;

    public function __construct($solicitudes)
    {
        $this->solicitudes = $solicitudes;
    }

    public function collection()
    {
        $productos = collect();
        $productosAgrupados = [];

        foreach ($this->solicitudes as $solicitud) {
            foreach ($solicitud->items as $item) {
                $key = $item->referencia_producto . '|' . ($item->info_variante ?? 'Sin variante');

                if (!isset($productosAgrupados[$key])) {
                    $productosAgrupados[$key] = [
                        'referencia' => $item->referencia_producto,
                        'nombre' => $item->nombre_producto,
                        'variante' => $item->info_variante ?? 'Sin variante',
                        'cantidad_total' => 0,
                        'veces_solicitado' => 0,
                        'monto_total' => 0
                    ];
                }

                $productosAgrupados[$key]['cantidad_total'] += $item->cantidad;
                $productosAgrupados[$key]['veces_solicitado']++;
                $productosAgrupados[$key]['monto_total'] += $item->precio_total;
            }
        }

        foreach ($productosAgrupados as $producto) {
            $productos->push($producto);
        }

        return $productos->sortByDesc('cantidad_total');
    }

    public function map($producto): array
    {
        return [
            $producto['referencia'],
            $producto['nombre'],
            $producto['variante'],
            $producto['cantidad_total'],
            $producto['veces_solicitado'],
            $producto['monto_total']
        ];
    }

    public function headings(): array
    {
        return [
            'Referencia',
            'Producto',
            'Variante',
            'Cantidad Total',
            'Veces Solicitado',
            'Monto Total'
        ];
    }

    public function title(): string
    {
        return 'Resumen Productos';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '28A745'] // Verde
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Destacar top 3 productos
                $topColors = ['FFE4F3', 'FFF1DD', 'E8F6F6'];
                for ($i = 0; $i < min(3, $lastRow - 1); $i++) {
                    $row = $i + 2;
                    $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $topColors[$i]],
                        ],
                    ]);
                }

                // Formato de moneda
                $sheet->getStyle("F2:F{$lastRow}")->getNumberFormat()->setFormatCode('$#,##0');

                // Bordes
                $sheet->getStyle("A1:F{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Congelar encabezado
                $sheet->freezePane('A2');
            },
        ];
    }
}
