<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EstadoMercado;
use App\Enums\EstadoMercadoItem;
use App\Http\Requests\RegistrarMercadoItemRequest;
use App\Models\MercadoItem;
use App\Services\MercadoSessionService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ListaMercadoItemController extends Controller
{
    public function __construct(private MercadoSessionService $session) {}

    public function create(MercadoItem $item): View|RedirectResponse
    {
        $item->load(['producto.tipo', 'tipo', 'mercado']);

        if ($item->mercado->estado !== EstadoMercado::EnProgreso) {
            return redirect()->route('lista-mercado.index')
                ->with('error', 'Este mercado ya no está en progreso.');
        }

        if ($item->estado !== EstadoMercadoItem::Pendiente) {
            return redirect()->route('lista-mercado.tipo', $item->tipo_producto_mercado_id)
                ->with('error', 'Este item ya fue procesado.');
        }

        return view('lista-mercado.item-create', compact('item'));
    }

    public function store(RegistrarMercadoItemRequest $request, MercadoItem $item): RedirectResponse
    {
        try {
            $this->session->registrarItem(
                $item,
                (float) $request->validated('cantidad'),
                (int) $request->validated('valor'),
            );
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $mercado = $item->mercado->fresh();

        if ($mercado->estado === EstadoMercado::Completado) {
            return redirect()->route('lista-mercado.completado', $mercado)
                ->with('success', '¡Mercado completado!');
        }

        $tipoFinalizado = ! $mercado->items()
            ->where('tipo_producto_mercado_id', $item->tipo_producto_mercado_id)
            ->where('estado', EstadoMercadoItem::Pendiente->value)
            ->exists();

        if ($tipoFinalizado) {
            return redirect()->route('lista-mercado.index')
                ->with('tipo_completado', $item->tipo->nombre);
        }

        return redirect()->route('lista-mercado.tipo', $item->tipo_producto_mercado_id)
            ->with('success', 'Registrado: ' . ($item->producto?->nombre ?? '—'));
    }

    public function saltar(MercadoItem $item): RedirectResponse
    {
        try {
            $this->session->saltarItem($item);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        $mercado = $item->mercado->fresh();

        if ($mercado->estado === EstadoMercado::Completado) {
            return redirect()->route('lista-mercado.completado', $mercado)
                ->with('success', '¡Mercado completado!');
        }

        $tipoFinalizado = ! $mercado->items()
            ->where('tipo_producto_mercado_id', $item->tipo_producto_mercado_id)
            ->where('estado', EstadoMercadoItem::Pendiente->value)
            ->exists();

        if ($tipoFinalizado) {
            return redirect()->route('lista-mercado.index')
                ->with('tipo_completado', $item->tipo->nombre);
        }

        return redirect()->route('lista-mercado.tipo', $item->tipo_producto_mercado_id)
            ->with('success', 'Saltado: ' . ($item->producto?->nombre ?? '—'));
    }
}
