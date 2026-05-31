<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoAhorroRequest;
use App\Models\Gasto;
use App\Models\PagoAhorro;
use App\Models\TrabajadorTurno;
use App\Services\PagoAhorroService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PagoAhorroController extends Controller
{
    public function __construct(private PagoAhorroService $pagosAhorro) {}

    public function index(\Illuminate\Http\Request $request): View
    {
        $desde = $request->filled('desde')
            ? Carbon::parse((string) $request->input('desde'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $hasta = $request->filled('hasta')
            ? Carbon::parse((string) $request->input('hasta'))->endOfDay()
            : Carbon::now()->endOfDay();

        $trabajadorId = $request->filled('trabajador_turno_id')
            ? (int) $request->input('trabajador_turno_id')
            : null;

        // Filtro por tipo de movimiento: '', 'ahorro' (aportes) o 'pago' (retiros)
        $tipo = in_array($request->input('tipo'), ['ahorro', 'pago'], true)
            ? (string) $request->input('tipo')
            : '';

        // Métricas del rango
        $totalAhorrado = (int) Gasto::where('ahorro', '>', 0)
            ->whereBetween('created_at', [$desde, $hasta])
            ->when($trabajadorId, fn ($q) => $q->where('trabajador_turno_id', $trabajadorId))
            ->sum('ahorro');

        $totalPagado = (int) PagoAhorro::whereBetween('pagado_en', [$desde, $hasta])
            ->when($trabajadorId, fn ($q) => $q->where('trabajador_turno_id', $trabajadorId))
            ->sum('monto');

        // Saldo acumulado global (a la fecha, no limitado por el rango)
        $aportadoTotal = (int) Gasto::when($trabajadorId, fn ($q) => $q->where('trabajador_turno_id', $trabajadorId))->sum('ahorro');
        $pagadoTotal = (int) PagoAhorro::when($trabajadorId, fn ($q) => $q->where('trabajador_turno_id', $trabajadorId))->sum('monto');
        $saldoAcumulado = $aportadoTotal - $pagadoTotal;

        $trabajadoresConAhorro = TrabajadorTurno::withSum('gastos as total_ahorrado', 'ahorro')
            ->withSum('pagosAhorro as total_pagado_ahorro', 'monto')
            ->get()
            ->filter(fn ($t) => $t->ahorro_acumulado > 0)
            ->count();

        // Movimientos del rango: aportes (gastos.ahorro) + pagos (pagos_ahorro), unificados
        $movimientos = collect();

        if ($tipo !== 'pago') {
            $aportes = Gasto::with(['trabajadorTurno', 'user', 'turno'])
                ->where('ahorro', '>', 0)
                ->whereBetween('created_at', [$desde, $hasta])
                ->when($trabajadorId, fn ($q) => $q->where('trabajador_turno_id', $trabajadorId))
                ->get()
                ->map(fn (Gasto $g) => [
                    'tipo'       => 'ahorro',
                    'fecha'      => $g->created_at,
                    'trabajador' => $g->trabajadorTurno?->nombre,
                    'monto'      => (int) $g->ahorro,
                    'detalle'    => 'Ahorro registrado en pago de turno',
                    'usuario'    => $g->user?->name,
                    'turno_id'   => $g->turno_caja_id,
                    'pago_id'    => null,
                ]);
            $movimientos = $movimientos->concat($aportes);
        }

        if ($tipo !== 'ahorro') {
            $pagos = PagoAhorro::with(['trabajadorTurno', 'user'])
                ->whereBetween('pagado_en', [$desde, $hasta])
                ->when($trabajadorId, fn ($q) => $q->where('trabajador_turno_id', $trabajadorId))
                ->get()
                ->map(fn (PagoAhorro $p) => [
                    'tipo'       => 'pago',
                    'fecha'      => $p->pagado_en,
                    'trabajador' => $p->trabajadorTurno?->nombre,
                    'monto'      => (int) $p->monto,
                    'detalle'    => $p->observacion ?: 'Pago de ahorro',
                    'usuario'    => $p->user?->name,
                    'turno_id'   => null,
                    'pago_id'    => $p->id,
                ]);
            $movimientos = $movimientos->concat($pagos);
        }

        $movimientos = $movimientos->sortByDesc('fecha')->values();

        $trabajadoresOptions = TrabajadorTurno::orderBy('nombre')->pluck('nombre', 'id')->all();

        return view('pagos-ahorros.index', [
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
            'trabajadorId' => $trabajadorId,
            'tipo' => $tipo,
            'trabajadoresOptions' => $trabajadoresOptions,
            'totalAhorrado' => $totalAhorrado,
            'totalPagado' => $totalPagado,
            'saldoAcumulado' => $saldoAcumulado,
            'trabajadoresConAhorro' => $trabajadoresConAhorro,
            'movimientos' => $movimientos,
        ]);
    }

    public function store(StorePagoAhorroRequest $request): RedirectResponse
    {
        try {
            $this->pagosAhorro->crear($request->validated(), (int) $request->user()->id);
        } catch (DomainException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->back()
            ->with('success', 'Pago de ahorro registrado correctamente.');
    }

    public function destroy(PagoAhorro $pagoAhorro): RedirectResponse
    {
        $this->pagosAhorro->eliminar($pagoAhorro);

        return redirect()
            ->back()
            ->with('success', 'Pago de ahorro eliminado.');
    }
}
