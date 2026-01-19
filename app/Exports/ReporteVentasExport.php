<?php

namespace App\Exports;

use App\Services\MetricasService;
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
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Reporte consolidado de ventas con múltiples hojas
 * Incluye: Resumen KPIs, Detalle de Cotizaciones, Ranking de Vendedores
 */
class ReporteVentasExport implements WithMultipleSheets
{
    protected Carbon $fechaInicio;
    protected Carbon $fechaFin;
    protected array $metricas;

    public function __construct(?Carbon $fechaInicio = null, ?Carbon $fechaFin = null)
    {
        $this->fechaInicio = $fechaInicio ?? Carbon::now()->startOfMonth();
        $this->fechaFin = $fechaFin ?? Carbon::now()->endOfMonth();

        // Obtener métricas del servicio
        $metricasService = app(MetricasService::class);
        $this->metricas = [
            'resumen' => $metricasService->getResumenVentas($this->fechaInicio, $this->fechaFin),
            'cotizaciones' => $metricasService->getCotizacionesPorEstado($this->fechaInicio, $this->fechaFin),
            'vendedores' => $metricasService->getTopVendedores($this->fechaInicio, $this->fechaFin, 20),
            'productos' => $metricasService->getTopProductos($this->fechaInicio, $this->fechaFin, 20),
        ];
    }

    public function sheets(): array
    {
        return [
            new ResumenKPIsSheet($this->metricas, $this->fechaInicio, $this->fechaFin),
            new VendedoresSheet($this->metricas['vendedores']),
            new ProductosVendidosSheet($this->metricas['productos']),
        ];
    }
}

/**
 * Hoja 1: Resumen de KPIs
 */
class ResumenKPIsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    protected array $metricas;
    protected Carbon $fechaInicio;
    protected Carbon $fechaFin;

    public function __construct(array $metricas, Carbon $fechaInicio, Carbon $fechaFin)
    {
        $this->metricas = $metricas;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        $resumen = $this->metricas['resumen'];
        $cotizaciones = $this->metricas['cotizaciones'];

        return collect([
            ['Período', $this->fechaInicio->format('d/m/Y') . ' - ' . $this->fechaFin->format('d/m/Y')],
            ['', ''],
            ['RESUMEN GENERAL', ''],
            ['Total Ventas', '$' . number_format($resumen['total_ventas'], 0, ',', '.')],
            ['Total Transacciones', $resumen['total_transacciones']],
            ['Promedio por Venta', '$' . number_format($resumen['promedio_venta'], 0, ',', '.')],
            ['', ''],
            ['VENTAS POR CANAL', ''],
            ['Cotizaciones Aplicadas', '$' . number_format($resumen['cotizaciones']['monto'], 0, ',', '.') . ' (' . $resumen['cotizaciones']['cantidad'] . ')'],
            ['Punto de Venta', '$' . number_format($resumen['pdv']['monto'], 0, ',', '.') . ' (' . $resumen['pdv']['cantidad'] . ')'],
            ['', ''],
            ['COTIZACIONES POR ESTADO', ''],
            ['Pendientes', $cotizaciones['pendientes']['cantidad'] . ' - $' . number_format($cotizaciones['pendientes']['monto'], 0, ',', '.') . ' (' . $cotizaciones['pendientes']['porcentaje'] . '%)'],
            ['Aplicadas', $cotizaciones['aplicadas']['cantidad'] . ' - $' . number_format($cotizaciones['aplicadas']['monto'], 0, ',', '.') . ' (' . $cotizaciones['aplicadas']['porcentaje'] . '%)'],
            ['Rechazadas', $cotizaciones['rechazadas']['cantidad'] . ' - $' . number_format($cotizaciones['rechazadas']['monto'], 0, ',', '.') . ' (' . $cotizaciones['rechazadas']['porcentaje'] . '%)'],
            ['', ''],
            ['INDICADORES', ''],
            ['Tasa de Conversión', $cotizaciones['tasa_conversion'] . '%'],
            ['Total Cotizaciones', $cotizaciones['total']['cantidad']],
            ['Valor Total Cotizado', '$' . number_format($cotizaciones['total']['monto'], 0, ',', '.')],
        ]);
    }

    public function headings(): array
    {
        return ['Métrica', 'Valor'];
    }

    public function title(): string
    {
        return 'Resumen KPIs';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF84D5'] // Miracle Pink
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Estilo para títulos de sección
                $sectionRows = [3, 8, 12, 17];
                foreach ($sectionRows as $row) {
                    $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '382E65']], // Miracle Dark Purple
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'BCA9F5'] // Miracle Lilac
                        ],
                    ]);
                }

                // Ancho de columnas
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(40);

                // Bordes
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:B{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
            },
        ];
    }
}

/**
 * Hoja 2: Ranking de Vendedores
 */
class VendedoresSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    protected array $vendedores;

    public function __construct(array $vendedores)
    {
        $this->vendedores = $vendedores;
    }

    public function collection()
    {
        return collect($this->vendedores)->map(function ($v, $index) {
            return [
                $index + 1,
                $v['vendedor'],
                $v['total_cotizaciones'],
                '$' . number_format($v['monto_total'], 0, ',', '.'),
                $v['aplicadas'],
                '$' . number_format($v['monto_aplicadas'], 0, ',', '.'),
                $v['pendientes'],
                $v['rechazadas'],
                $v['tasa_conversion'] . '%',
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'Vendedor',
            'Total Cotiz.',
            'Monto Cotizado',
            'Aplicadas',
            'Monto Aplicadas',
            'Pendientes',
            'Rechazadas',
            'Conversión',
        ];
    }

    public function title(): string
    {
        return 'Ranking Vendedores';
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

                // Colorear filas según tasa de conversión
                for ($row = 2; $row <= $lastRow; $row++) {
                    $conversionCell = $sheet->getCell("I{$row}")->getValue();
                    $conversion = (float) str_replace('%', '', $conversionCell);

                    $bgColor = 'FFFFFF';
                    if ($conversion >= 70) {
                        $bgColor = 'D4EDDA'; // Verde claro - excelente
                    } elseif ($conversion >= 50) {
                        $bgColor = 'FFF3CD'; // Amarillo claro - bueno
                    } elseif ($conversion < 30 && $conversion > 0) {
                        $bgColor = 'F8D7DA'; // Rojo claro - bajo
                    }

                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $bgColor],
                        ],
                    ]);
                }

                // Destacar al #1
                if ($lastRow >= 2) {
                    $sheet->getStyle("A2:I2")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFE4F3'], // Rosa muy claro
                        ],
                    ]);
                }

                // Bordes
                $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
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
 * Hoja 3: Productos más vendidos
 */
class ProductosVendidosSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    protected array $productos;

    public function __construct(array $productos)
    {
        $this->productos = $productos;
    }

    public function collection()
    {
        return collect($this->productos)->map(function ($p, $index) {
            return [
                $index + 1,
                $p['referencia'],
                $p['nombre'],
                $p['cantidad_vendida'],
                '$' . number_format($p['monto_total'], 0, ',', '.'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            '#',
            'Referencia',
            'Producto',
            'Cantidad Vendida',
            'Monto Total',
        ];
    }

    public function title(): string
    {
        return 'Top Productos';
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

                // Destacar top 3
                $topColors = ['FFE4F3', 'FFF1DD', 'E8F6F6']; // Rosa, Beige, Aqua claro
                for ($i = 0; $i < min(3, $lastRow - 1); $i++) {
                    $row = $i + 2;
                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $topColors[$i]],
                        ],
                    ]);
                }

                // Bordes
                $sheet->getStyle("A1:E{$lastRow}")->applyFromArray([
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
