<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlantillaFacturaRequest;
use App\Models\PlantillaFactura;
use App\Services\Facturacion\TemplateRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlantillaFacturaController extends Controller
{
    public function __construct(private readonly TemplateRenderer $renderer) {}

    public function index(): View
    {
        $plantillas = PlantillaFactura::orderByDesc('es_default')->orderBy('nombre')->get();

        return view('admin.plantillas.index', compact('plantillas'));
    }

    public function create(): View
    {
        $plantilla = new PlantillaFactura([
            'html_content' => PlantillaFactura::HTML_BASE,
            'css_content' => PlantillaFactura::CSS_BASE,
        ]);

        return view('admin.plantillas.form', [
            'plantilla' => $plantilla,
            'cssDefault' => PlantillaFactura::CSS_BASE,
            'htmlDefault' => PlantillaFactura::HTML_BASE,
        ]);
    }

    public function store(PlantillaFacturaRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['html_content'] = $this->renderer->sanitizar($data['html_content']);

            if (! empty($data['es_default'])) {
                PlantillaFactura::query()->lockForUpdate()->update(['es_default' => false]);
            }

            PlantillaFactura::create($data);
        });

        return redirect()->route('admin.plantillas.index')->with('success', 'Plantilla creada.');
    }

    public function edit(PlantillaFactura $plantilla): View
    {
        return view('admin.plantillas.form', [
            'plantilla' => $plantilla,
            'cssDefault' => PlantillaFactura::CSS_BASE,
            'htmlDefault' => PlantillaFactura::HTML_BASE,
        ]);
    }

    public function update(PlantillaFacturaRequest $request, PlantillaFactura $plantilla): RedirectResponse
    {
        DB::transaction(function () use ($request, $plantilla) {
            $data = $request->validated();
            $data['html_content'] = $this->renderer->sanitizar($data['html_content']);

            if (! empty($data['es_default'])) {
                PlantillaFactura::query()->where('id', '!=', $plantilla->id)->lockForUpdate()->update(['es_default' => false]);
            }

            $plantilla->update($data);
        });

        return redirect()->route('admin.plantillas.index')->with('success', 'Plantilla actualizada.');
    }

    public function destroy(PlantillaFactura $plantilla): RedirectResponse
    {
        if ($plantilla->es_default) {
            return back()->with('error', 'No puedes eliminar la plantilla predeterminada.');
        }

        $plantilla->delete();

        return redirect()->route('admin.plantillas.index')->with('success', 'Plantilla eliminada.');
    }

    public function previsualizar(Request $request): JsonResponse
    {
        $request->validate([
            'html_content' => ['required', 'string', 'max:100000'],
            'css_content' => ['nullable', 'string', 'max:20000'],
        ]);

        $plantilla = new PlantillaFactura([
            'html_content' => $this->renderer->sanitizar((string) $request->input('html_content')),
            'css_content' => (string) $request->input('css_content'),
        ]);

        $html = $this->renderer->render($plantilla, $this->renderer->datosDummy());

        return response()->json(['html' => $html]);
    }
}
