<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ListaMercado;
use App\Models\Mercado;
use App\Models\TipoProductoMercado;
use App\Services\MercadoSessionService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ListaMercadoController extends Controller
{
    public function __construct(private MercadoSessionService $session) {}

    public function index(): View
    {
        $mercado = $this->session->obtenerMercadoActivo();
        $lista   = ListaMercado::actual();

        $tipos = collect();
        $resumenLista = null;

        if ($mercado) {
            $tipos = $this->session->tiposPendientes($mercado);
        } else {
            $items = $lista->items()->with('producto.tipo')->get();
            $resumenLista = (object) [
                'total'        => $items->count(),
                'porTipo'      => $items->groupBy(fn ($i) => $i->producto?->tipo?->nombre ?? '—')
                                        ->map->count(),
            ];
        }

        return view('lista-mercado.index', compact('mercado', 'lista', 'tipos', 'resumenLista'));
    }

    public function iniciar(): RedirectResponse
    {
        try {
            $this->session->iniciarMercado((int) auth()->id());
        } catch (DomainException $e) {
            return redirect()->route('lista-mercado.index')->with('error', $e->getMessage());
        }

        return redirect()->route('lista-mercado.index')->with('success', 'Mercado iniciado. ¡Manos a la obra!');
    }

    public function tipo(TipoProductoMercado $tipo): View|RedirectResponse
    {
        $mercado = $this->session->obtenerMercadoActivo();

        if (! $mercado) {
            return redirect()->route('lista-mercado.index')
                ->with('error', 'No hay un mercado en progreso.');
        }

        $items = $this->session->itemsDeTipo($mercado, $tipo->id);

        if ($items->isEmpty()) {
            return redirect()->route('lista-mercado.index')
                ->with('error', 'Este tipo no tiene items en el mercado actual.');
        }

        $pendientes  = $items->where('estado.value', 'pendiente');
        $procesados  = $items->whereIn('estado.value', ['registrado', 'saltado']);
        $finalizado  = $pendientes->isEmpty();

        return view('lista-mercado.tipo', compact('mercado', 'tipo', 'items', 'pendientes', 'procesados', 'finalizado'));
    }

    public function finalizar(Mercado $mercado): RedirectResponse
    {
        try {
            $this->session->finalizarManual($mercado);
        } catch (DomainException $e) {
            return redirect()->route('lista-mercado.index')->with('error', $e->getMessage());
        }

        return redirect()->route('lista-mercado.completado', $mercado);
    }

    public function cancelar(Mercado $mercado): RedirectResponse
    {
        try {
            $this->session->cancelar($mercado);
        } catch (DomainException $e) {
            return redirect()->route('lista-mercado.index')->with('error', $e->getMessage());
        }

        return redirect()->route('lista-mercado.index')->with('success', 'Mercado cancelado.');
    }

    public function completado(Mercado $mercado): View
    {
        $mercado->load(['items.producto', 'items.tipo', 'registros']);

        return view('lista-mercado.mercado-completado', compact('mercado'));
    }
}
