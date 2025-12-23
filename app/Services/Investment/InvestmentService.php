<?php

namespace App\Services\Investment;

use App\Models\User;
use App\Models\Proyecto;
use App\Models\Inversion;
use App\Models\Billetera;
use App\Services\Wallet\WalletService;
use App\Services\Dividend\DividendCalculatorService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvestmentService
{
    public function __construct(
        private WalletService $walletService
    ) {}

    /**
     * Validar si usuario puede invertir en un proyecto
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function canInvest(User $user, Proyecto $proyecto, float $monto): array
    {
        // Validar estado del proyecto
        if ($proyecto->estado !== 'en_recaudacion') {
            return [
                'success' => false,
                'message' => 'Este proyecto no está disponible para inversión en este momento.'
            ];
        }

        // Validar que el proyecto no esté totalmente fondeado
        $montoRestante = $proyecto->monto_objetivo - $proyecto->monto_recaudado;
        if ($montoRestante <= 0) {
            return [
                'success' => false,
                'message' => 'Este proyecto ya alcanzó su meta de recaudación.'
            ];
        }

        // Validar monto mínimo
        if ($monto < $proyecto->inversion_minima) {
            return [
                'success' => false,
                'message' => 'El monto mínimo de inversión es $' . number_format($proyecto->inversion_minima, 0, ',', '.')
            ];
        }

        // Validar monto máximo
        if ($monto > $proyecto->inversion_maxima) {
            return [
                'success' => false,
                'message' => 'El monto máximo de inversión es $' . number_format($proyecto->inversion_maxima, 0, ',', '.')
            ];
        }

        // Validar que el monto no exceda el restante del proyecto
        if ($monto > $montoRestante) {
            return [
                'success' => false,
                'message' => 'El monto excede lo que el proyecto necesita. Monto disponible: $' . number_format($montoRestante, 0, ',', '.')
            ];
        }

        // Validar saldo disponible
        $billetera = $this->walletService->getOrCreateWallet($user);
        if ($billetera->saldo_disponible < $monto) {
            return [
                'success' => false,
                'message' => 'Saldo insuficiente. Tienes $' . number_format($billetera->saldo_disponible, 0, ',', '.') . ' disponibles.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Puede invertir'
        ];
    }

    /**
     * Crear una inversión completa (crear, pagar, activar)
     */
    public function createInvestment(User $user, Proyecto $proyecto, float $monto): Inversion
    {
        return DB::transaction(function () use ($user, $proyecto, $monto) {
            // Obtener billetera
            $billetera = $this->walletService->getOrCreateWallet($user);

            // Calcular fecha de vencimiento
            $fechaVencimiento = Carbon::now()->addMonths($proyecto->duracion_meses);

            // Crear la inversión
            $inversion = Inversion::create([
                'codigo_inversion' => $this->generateInvestmentCode(),
                'usuario_id' => $user->id,
                'proyecto_id' => $proyecto->id,
                'monto_invertido' => $monto,
                'valor_actual' => $monto,
                'ganancia_acumulada' => 0,
                'dividendos_acumulados' => 0,
                'fecha_inversion' => Carbon::now(),
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => 'pendiente_pago',
                'disponible_trading' => false,
                'contrato_id' => null,
            ]);

            // Procesar pago (descontar de billetera)
            $this->processPayment($inversion, $billetera, $proyecto);

            // Activar inversión
            $this->activateInvestment($inversion);

            // Programar dividendos para esta inversión
            $dividendCalculator = app(DividendCalculatorService::class);
            $dividendCalculator->scheduleDividendsForInvestment($inversion);

            // Actualizar monto recaudado del proyecto
            $proyecto->increment('monto_recaudado', $monto);

            // Verificar si el proyecto alcanzó su meta
            if ($proyecto->monto_recaudado >= $proyecto->monto_objetivo) {
                $proyecto->update(['estado' => 'fondeado']);
            }

            return $inversion->fresh();
        });
    }

    /**
     * Procesar pago de inversión
     */
    private function processPayment(Inversion $inversion, Billetera $billetera, Proyecto $proyecto): void
    {
        // Descontar de saldo disponible
        $this->walletService->deductFunds(
            $billetera,
            $inversion->monto_invertido,
            'inversion',
            "Inversión en {$proyecto->nombre} - {$inversion->codigo_inversion}",
            $inversion
        );

        // Mover a saldo invertido
        $this->walletService->investFunds($billetera, $inversion->monto_invertido);
    }

    /**
     * Activar inversión después de pago exitoso
     */
    private function activateInvestment(Inversion $inversion): void
    {
        $inversion->update([
            'estado' => 'activa'
        ]);
    }

    /**
     * Calcular retornos estimados para mostrar en formulario
     */
    public function calculateEstimatedReturns(Proyecto $proyecto, float $monto): array
    {
        $roiAnual = $proyecto->roi_anual / 100;
        $duracionMeses = $proyecto->duracion_meses;

        // Retorno mensual estimado
        $retornoMensual = ($monto * $roiAnual) / 12;

        // Retorno total al final del período
        $retornoTotal = ($monto * $roiAnual * $duracionMeses) / 12;

        // Valor total al vencimiento
        $valorAlVencimiento = $monto + $retornoTotal;

        return [
            'monto_invertido' => $monto,
            'retorno_mensual' => round($retornoMensual, 2),
            'retorno_mensual_formateado' => '$' . number_format($retornoMensual, 0, ',', '.'),
            'retorno_total' => round($retornoTotal, 2),
            'retorno_total_formateado' => '$' . number_format($retornoTotal, 0, ',', '.'),
            'valor_vencimiento' => round($valorAlVencimiento, 2),
            'valor_vencimiento_formateado' => '$' . number_format($valorAlVencimiento, 0, ',', '.'),
            'roi_anual' => $proyecto->roi_anual,
            'duracion_meses' => $duracionMeses,
        ];
    }

    /**
     * Generar código único de inversión
     */
    private function generateInvestmentCode(): string
    {
        $year = date('Y');
        $lastInversion = Inversion::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInversion && preg_match('/INV-' . $year . '-(\d+)/', $lastInversion->codigo_inversion, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('INV-%s-%06d', $year, $nextNumber);
    }

    /**
     * Obtener inversiones del usuario con filtros
     */
    public function getUserInvestments(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Inversion::where('usuario_id', $user->id)
            ->with(['proyecto', 'proyecto.categoria', 'proyecto.imagenes'])
            ->orderBy('created_at', 'desc');

        // Filtro por estado
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        // Filtro por proyecto
        if (!empty($filters['proyecto_id'])) {
            $query->where('proyecto_id', $filters['proyecto_id']);
        }

        // Filtro por fecha desde
        if (!empty($filters['fecha_desde'])) {
            $query->whereDate('fecha_inversion', '>=', $filters['fecha_desde']);
        }

        // Filtro por fecha hasta
        if (!empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha_inversion', '<=', $filters['fecha_hasta']);
        }

        $limit = $filters['limit'] ?? 15;

        return $query->paginate($limit);
    }

    /**
     * Obtener resumen de inversiones del usuario
     */
    public function getInvestmentSummary(User $user): array
    {
        $billetera = $this->walletService->getOrCreateWallet($user);

        // Contar inversiones activas
        $inversionesActivas = Inversion::where('usuario_id', $user->id)
            ->where('estado', 'activa')
            ->count();

        // Suma de valor actual de inversiones activas
        $valorActualTotal = Inversion::where('usuario_id', $user->id)
            ->where('estado', 'activa')
            ->sum('valor_actual');

        // Ganancia acumulada total
        $gananciaTotal = Inversion::where('usuario_id', $user->id)
            ->sum('ganancia_acumulada');

        // Próximo dividendo (de inversiones activas)
        $proximoDividendo = Inversion::where('usuario_id', $user->id)
            ->where('estado', 'activa')
            ->with('proyecto')
            ->get()
            ->map(function ($inversion) {
                if ($inversion->proyecto && $inversion->proyecto->fecha_primer_dividendo) {
                    return $inversion->proyecto->fecha_primer_dividendo;
                }
                return null;
            })
            ->filter()
            ->min();

        return [
            'total_invertido' => [
                'valor' => $billetera->saldo_invertido,
                'formateado' => '$' . number_format($billetera->saldo_invertido, 0, ',', '.'),
                'titulo' => 'Total Invertido',
                'icono' => 'fas fa-hand-holding-usd',
                'color' => 'primary',
            ],
            'inversiones_activas' => [
                'valor' => $inversionesActivas,
                'formateado' => $inversionesActivas,
                'titulo' => 'Inversiones Activas',
                'icono' => 'fas fa-chart-pie',
                'color' => 'success',
            ],
            'ganancias_acumuladas' => [
                'valor' => $billetera->retornos_acumulados,
                'formateado' => '$' . number_format($billetera->retornos_acumulados, 0, ',', '.'),
                'titulo' => 'Ganancias Acumuladas',
                'icono' => 'fas fa-chart-line',
                'color' => 'info',
            ],
            'proximo_dividendo' => [
                'valor' => $proximoDividendo,
                'formateado' => $proximoDividendo ? Carbon::parse($proximoDividendo)->format('d/m/Y') : 'Sin programar',
                'titulo' => 'Próximo Dividendo',
                'icono' => 'fas fa-calendar-check',
                'color' => 'warning',
            ],
            'valor_actual_total' => [
                'valor' => $valorActualTotal,
                'formateado' => '$' . number_format($valorActualTotal, 0, ',', '.'),
                'titulo' => 'Valor Actual',
                'icono' => 'fas fa-coins',
                'color' => 'dark',
            ],
        ];
    }

    /**
     * Obtener una inversión por código
     */
    public function getInvestmentByCode(string $codigo): ?Inversion
    {
        return Inversion::where('codigo_inversion', $codigo)
            ->with(['proyecto', 'usuario', 'dividendos', 'aceptacionContrato'])
            ->first();
    }

    /**
     * Obtener colores por estado de inversión
     */
    public static function getInvestmentStateColor(string $estado): string
    {
        $colores = [
            'pendiente_pago' => 'warning',
            'activa' => 'success',
            'en_trading' => 'info',
            'vendida' => 'secondary',
            'vencida' => 'dark',
            'retirada_anticipada' => 'warning',
            'cancelada' => 'danger',
        ];

        return $colores[$estado] ?? 'secondary';
    }

    /**
     * Obtener etiqueta legible del estado
     */
    public static function getInvestmentStateLabel(string $estado): string
    {
        $labels = [
            'pendiente_pago' => 'Pendiente de Pago',
            'activa' => 'Activa',
            'en_trading' => 'En Venta',
            'vendida' => 'Vendida',
            'vencida' => 'Vencida',
            'retirada_anticipada' => 'Retiro Anticipado',
            'cancelada' => 'Cancelada',
        ];

        return $labels[$estado] ?? ucfirst($estado);
    }
}
