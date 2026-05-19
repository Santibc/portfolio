<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreListaMercadoItemRequest;
use App\Http\Requests\UpdateListaMercadoItemRequest;
use App\Models\ListaMercado;
use App\Models\ListaMercadoItem;
use App\Models\ProductoMercado;
use App\Services\ListaPlantillaService;
use App\Services\MercadoSessionService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ListaMercadoPlantillaController extends Controller
{
    public function __construct(
        private ListaPlantillaService $plantilla,
        private MercadoSessionService $session,
    ) {}

    public function index(): View
    {
        $lista = ListaMercado::actual();
        $items = $lista->items()
            ->with('producto.tipo')
            ->get()
            ->sortBy(fn ($i) => [$i->producto?->tipo?->nombre ?? 'zzz', $i->producto?->nombre ?? 'zzz'])
            ->values();

        $puedeEditar = $this->session->puedeEditarPlantilla();
        $mercadoActivo = $this->session->obtenerMercadoActivo();

        $idsEnLista = $items->pluck('producto_mercado_id')->all();
        $productosDisponibles = ProductoMercado::activos()
            ->with('tipo')
            ->whereNotIn('id', $idsEnLista)
            ->orderBy('nombre')
            ->get();

        return view('lista-mercado.plantilla.index', compact(
            'lista', 'items', 'puedeEditar', 'mercadoActivo', 'productosDisponibles'
        ));
    }

    public function storeItem(StoreListaMercadoItemRequest $request): RedirectResponse
    {
        try {
            $this->plantilla->agregarProducto(
                (int) $request->validated('producto_mercado_id'),
                (int) $request->validated('cantidad_sugerida'),
            );
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('lista-mercado.plantilla.index')
            ->with('success', 'Producto agregado a la lista.');
    }

    public function updateItem(UpdateListaMercadoItemRequest $request, ListaMercadoItem $item): RedirectResponse
    {
        try {
            $this->plantilla->actualizarCantidad($item, (int) $request->validated('cantidad_sugerida'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('lista-mercado.plantilla.index')
            ->with('success', 'Cantidad actualizada.');
    }

    public function destroyItem(ListaMercadoItem $item): RedirectResponse
    {
        try {
            $this->plantilla->quitar($item);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('lista-mercado.plantilla.index')
            ->with('success', 'Producto eliminado de la lista.');
    }
}
