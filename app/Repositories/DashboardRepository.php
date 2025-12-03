<?php

namespace App\Repositories;

use App\Models\Billetera;
use App\Models\Deposito;
use App\Models\Dividendo;
use App\Models\Inversion;
use App\Models\Proyecto;
use App\Models\Prospecto;
use App\Models\Retiro;
use App\Models\User;
use App\Models\DocumentoKyc;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    /**
     * Obtener el total recaudado en toda la plataforma
     */
    public function getTotalRecaudado(): float
    {
        return Inversion::where('estado', 'activa')
            ->sum('monto_invertido') ?? 0.0;
    }

    /**
     * Obtener el total en billeteras de todos los usuarios
     */
    public function getTotalBilleteras(): float
    {
        return Billetera::sum('saldo_disponible') ?? 0.0;
    }

    /**
     * Obtener fondos agrupados por categoría de proyecto
     */
    public function getFondosPorCategoria(): Collection
    {
        return Inversion::join('proyectos', 'inversiones.proyecto_id', '=', 'proyectos.id')
            ->join('categorias_proyecto', 'proyectos.categoria_id', '=', 'categorias_proyecto.id')
            ->select('categorias_proyecto.nombre', DB::raw('SUM(inversiones.monto_invertido) as total'))
            ->where('inversiones.estado', 'activa')
            ->groupBy('categorias_proyecto.id', 'categorias_proyecto.nombre')
            ->get();
    }

    /**
     * Obtener inversiones por mes (últimos N meses)
     */
    public function getInversionesPorMes(int $meses = 12): Collection
    {
        return Inversion::select(
            DB::raw('DATE_FORMAT(fecha_inversion, "%Y-%m") as mes'),
            DB::raw('SUM(monto_invertido) as total'),
            DB::raw('COUNT(*) as cantidad')
        )
            ->where('fecha_inversion', '>=', now()->subMonths($meses))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();
    }

    /**
     * Obtener solicitudes de retiro pendientes
     */
    public function getRetirosPendientes(): Collection
    {
        return Retiro::with('user')
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener documentos KYC pendientes de revisión
     */
    public function getKycPendientes(): Collection
    {
        return DocumentoKyc::with('user')
            ->where('estado', 'pendiente_revision')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener proyectos pendientes de aprobación
     */
    public function getProyectosPendientes(): Collection
    {
        return Proyecto::with('agricultor', 'categoria')
            ->where('estado', 'en_revision')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener saldo de billetera de un usuario
     */
    public function getSaldoBilletera(User $user): float
    {
        $billetera = Billetera::where('usuario_id', $user->id)->first();
        return $billetera ? $billetera->saldo_disponible : 0.0;
    }

    /**
     * Obtener total invertido por un usuario
     */
    public function getTotalInvertido(User $user): float
    {
        return Inversion::where('usuario_id', $user->id)
            ->whereIn('estado', ['activa', 'completada'])
            ->sum('monto_invertido') ?? 0.0;
    }

    /**
     * Obtener retornos acumulados de un inversionista
     */
    public function getRetornosAcumulados(User $user): float
    {
        return Dividendo::where('usuario_id', $user->id)
            ->where('estado', 'pagado')
            ->sum('monto') ?? 0.0;
    }

    /**
     * Obtener dividendos pendientes de un inversionista
     */
    public function getDividendosPendientes(User $user): float
    {
        return Dividendo::where('usuario_id', $user->id)
            ->where('estado', 'programado')
            ->sum('monto') ?? 0.0;
    }

    /**
     * Obtener inversiones activas de un usuario
     */
    public function getInversionesActivas(User $user): Collection
    {
        return Inversion::with('proyecto.categoria')
            ->where('usuario_id', $user->id)
            ->where('estado', 'activa')
            ->orderBy('fecha_inversion', 'desc')
            ->get();
    }

    /**
     * Obtener próximos dividendos a recibir
     */
    public function getProximosDividendos(User $user): Collection
    {
        return Dividendo::with('proyecto')
            ->where('usuario_id', $user->id)
            ->where('estado', 'programado')
            ->orderBy('fecha_programada', 'asc')
            ->limit(5)
            ->get();
    }

    /**
     * Obtener rendimiento del portafolio por mes (inversionista)
     */
    public function getRendimientoPortafolio(User $user, int $meses = 12): Collection
    {
        return Dividendo::select(
            DB::raw('DATE_FORMAT(fecha_pagada, "%Y-%m") as mes'),
            DB::raw('SUM(monto) as total')
        )
            ->where('usuario_id', $user->id)
            ->where('estado', 'pagado')
            ->where('fecha_pagada', '>=', now()->subMonths($meses))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();
    }

    /**
     * Obtener proyectos de un agricultor
     */
    public function getProyectosAgricultor(User $agricultor): Collection
    {
        return Proyecto::with('categoria')
            ->where('agricultor_id', $agricultor->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener total recaudado en proyectos de un agricultor
     */
    public function getTotalRecaudadoAgricultor(User $agricultor): float
    {
        return Proyecto::where('agricultor_id', $agricultor->id)
            ->sum('monto_recaudado') ?? 0.0;
    }

    /**
     * Obtener proyectos en recaudación de un agricultor
     */
    public function getProyectosEnRecaudacion(User $agricultor): Collection
    {
        return Proyecto::with('categoria')
            ->where('agricultor_id', $agricultor->id)
            ->where('estado', 'en_recaudacion')
            ->get();
    }

    /**
     * Obtener prospectos de un vendedor
     */
    public function getProspectosVendedor(User $vendedor): Collection
    {
        return Prospecto::where('asignado_a', $vendedor->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener prospectos por estado (vendedor)
     */
    public function getProspectosPorEstado(User $vendedor): Collection
    {
        return Prospecto::select('estado', DB::raw('COUNT(*) as total'))
            ->where('asignado_a', $vendedor->id)
            ->groupBy('estado')
            ->get();
    }

    /**
     * Obtener tasa de conversión de un vendedor
     */
    public function getTasaConversion(User $vendedor): float
    {
        $totalProspectos = Prospecto::where('asignado_a', $vendedor->id)->count();

        if ($totalProspectos === 0) {
            return 0.0;
        }

        $convertidos = Prospecto::where('asignado_a', $vendedor->id)
            ->where('estado', 'convertido')
            ->count();

        return ($convertidos / $totalProspectos) * 100;
    }

    /**
     * Obtener inversión total generada por prospectos de un vendedor
     */
    public function getInversionGeneradaVendedor(User $vendedor): float
    {
        return Prospecto::where('asignado_a', $vendedor->id)
            ->where('estado', 'convertido')
            ->whereNotNull('usuario_convertido_id')
            ->with('usuarioConvertido.inversiones')
            ->get()
            ->sum(function ($prospecto) {
                return $prospecto->usuarioConvertido ? $prospecto->usuarioConvertido->inversiones->sum('monto_invertido') : 0;
            });
    }

    /**
     * Obtener conteo de proyectos por estado
     */
    public function getProyectosPorEstado(): Collection
    {
        return Proyecto::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get();
    }

    /**
     * Obtener últimos depósitos
     */
    public function getUltimosDepositos(int $limit = 10): Collection
    {
        return Deposito::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas generales del sistema
     */
    public function getEstadisticasGenerales(): array
    {
        return [
            'total_usuarios' => User::count(),
            'total_proyectos' => Proyecto::count(),
            'total_inversiones' => Inversion::count(),
            'total_recaudado' => $this->getTotalRecaudado(),
            'total_billeteras' => $this->getTotalBilleteras(),
            'proyectos_activos' => Proyecto::whereIn('estado', ['en_recaudacion', 'en_ejecucion'])->count(),
            'inversiones_activas' => Inversion::where('estado', 'activa')->count(),
        ];
    }
}
