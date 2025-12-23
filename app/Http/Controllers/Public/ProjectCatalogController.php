<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Project\ProjectCatalogService;
use Illuminate\Http\Request;

class ProjectCatalogController extends Controller
{
    public function __construct(
        private ProjectCatalogService $catalogService
    ) {}

    /**
     * Mostrar catálogo de proyectos
     */
    public function index(Request $request)
    {
        $filters = [
            'categoria' => $request->get('categoria'),
            'search' => $request->get('search'),
            'riesgo' => $request->get('riesgo'),
            'roi_min' => $request->get('roi_min'),
            'plazo_max' => $request->get('plazo_max'),
            'inversion_max' => $request->get('inversion_max'),
            'sort' => $request->get('sort', 'destacado'),
            'per_page' => 12,
        ];

        $proyectos = $this->catalogService->getProjectsForCatalog($filters);
        $categorias = $this->catalogService->getCategories();
        $stats = $this->catalogService->getCatalogStats();
        $destacados = $this->catalogService->getHighlightedProjects(4);

        return view('public.catalog.index', compact(
            'proyectos',
            'categorias',
            'stats',
            'destacados',
            'filters'
        ));
    }

    /**
     * Mostrar detalle de un proyecto
     */
    public function show(string $codigo)
    {
        $proyecto = $this->catalogService->getProjectByCode($codigo);

        if (!$proyecto) {
            abort(404, 'Proyecto no encontrado');
        }

        $progress = $this->catalogService->calculateProgress($proyecto);
        $diasRestantes = $this->catalogService->calculateDaysRemaining($proyecto);
        $relacionados = $this->catalogService->getRelatedProjects($proyecto, 3);
        $icon = $this->catalogService->getProjectIcon($proyecto);

        return view('public.catalog.show', compact(
            'proyecto',
            'progress',
            'diasRestantes',
            'relacionados',
            'icon'
        ));
    }
}
