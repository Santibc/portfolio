<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Repositories\DashboardRepository;

class SalesDashboardService
{
    public function __construct(
        private DashboardRepository $dashboardRepository
    ) {}

    /**
     * Obtener datos completos del dashboard vendedor
     */
    public function getDashboardData(User $user): array
    {
        return [
            'estadisticas' => $this->getEstadisticasVendedor($user),
            'prospectos' => $this->getProspectos($user),
            'prospectos_por_estado' => $this->getProspectosPorEstado($user),
            'conversion_mensual' => $this->getConversionMensual($user),
        ];
    }

    /**
     * Estadísticas del vendedor
     */
    private function getEstadisticasVendedor(User $user): array
    {
        $prospectos = $this->dashboardRepository->getProspectosVendedor($user);
        $tasaConversion = $this->dashboardRepository->getTasaConversion($user);
        $inversionGenerada = $this->dashboardRepository->getInversionGeneradaVendedor($user);

        return [
            [
                'titulo' => 'Mis Prospectos',
                'valor' => $prospectos->count(),
                'icono' => 'fas fa-user-tie',
                'color' => 'primary',
                'descripcion' => 'Total de prospectos',
            ],
            [
                'titulo' => 'Tasa de Conversión',
                'valor' => number_format($tasaConversion, 1) . '%',
                'icono' => 'fas fa-chart-line',
                'color' => 'success',
                'descripcion' => 'Prospectos convertidos',
            ],
            [
                'titulo' => 'Inversión Generada',
                'valor' => $this->formatearMoneda($inversionGenerada),
                'icono' => 'fas fa-dollar-sign',
                'color' => 'warning',
                'descripcion' => 'Por tus prospectos',
            ],
            [
                'titulo' => 'Activos',
                'valor' => $prospectos->whereIn('estado', ['interesado', 'negociacion'])->count(),
                'icono' => 'fas fa-fire',
                'color' => 'info',
                'descripcion' => 'Prospectos en seguimiento',
            ],
        ];
    }

    /**
     * Lista de prospectos del vendedor
     */
    private function getProspectos(User $user): array
    {
        $prospectos = $this->dashboardRepository->getProspectosVendedor($user);

        return $prospectos->take(10)->map(function ($prospecto) {
            return [
                'id' => $prospecto->id,
                'nombre' => $prospecto->nombre,
                'email' => $prospecto->email,
                'telefono' => $prospecto->telefono,
                'estado' => $prospecto->estado,
                'interes' => $prospecto->nivel_interes,
                'ultima_actividad' => $prospecto->updated_at,
                'dias_sin_contacto' => now()->diffInDays($prospecto->updated_at),
            ];
        })->toArray();
    }

    /**
     * Prospectos agrupados por estado
     */
    private function getProspectosPorEstado(User $user): array
    {
        $prospectosPorEstado = $this->dashboardRepository->getProspectosPorEstado($user);

        $labels = [
            'nuevo' => 'Nuevo',
            'contactado' => 'Contactado',
            'en_seguimiento' => 'En Seguimiento',
            'interesado' => 'Interesado',
            'negociacion' => 'Negociación',
            'convertido' => 'Convertido',
            'perdido' => 'Perdido',
            'inactivo' => 'Inactivo',
        ];

        return [
            'labels' => $prospectosPorEstado->map(fn($item) => $labels[$item->estado] ?? $item->estado)->toArray(),
            'data' => $prospectosPorEstado->pluck('total')->toArray(),
            'backgroundColor' => [
                '#6c757d', // nuevo - gris
                '#17a2b8', // contactado - cyan
                '#ffc107', // en_seguimiento - amarillo
                '#28a745', // interesado - verde
                '#007bff', // negociacion - azul
                '#20c997', // convertido - teal
                '#dc3545', // perdido - rojo
                '#6c757d', // inactivo - gris
            ],
        ];
    }

    /**
     * Conversión mensual (últimos 12 meses)
     */
    private function getConversionMensual(User $user): array
    {
        $conversiones = \DB::table('prospectos')
            ->where('asignado_a', $user->id)
            ->select(
                \DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                \DB::raw('COUNT(*) as total'),
                \DB::raw('SUM(CASE WHEN estado = "convertido" THEN 1 ELSE 0 END) as convertidos')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        $labels = $conversiones->pluck('mes')->map(function ($mes) {
            return \Carbon\Carbon::createFromFormat('Y-m', $mes)->locale('es')->isoFormat('MMM YYYY');
        })->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Prospectos Nuevos',
                    'data' => $conversiones->pluck('total')->toArray(),
                    'borderColor' => '#007bff',
                    'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Convertidos',
                    'data' => $conversiones->pluck('convertidos')->toArray(),
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.1)',
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    /**
     * Formatear valores monetarios de forma compacta
     */
    private function formatearMoneda(float $valor): string
    {
        if ($valor >= 1000000) {
            // Millones: 3.5M, 10M, etc.
            $millones = $valor / 1000000;
            if ($millones >= 10) {
                return '$' . number_format($millones, 0) . 'M';
            } else {
                return '$' . number_format($millones, 1, ',', '.') . 'M';
            }
        } elseif ($valor >= 1000) {
            // Miles: 500K, 999K, etc.
            $miles = $valor / 1000;
            return '$' . number_format($miles, 0) . 'K';
        } else {
            // Valores pequeños: formato normal
            return '$' . number_format($valor, 0, ',', '.');
        }
    }
}
