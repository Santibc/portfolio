<?php

namespace App\Services\Project;

use App\Models\CategoriaProyecto;
use App\Models\Proyecto;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProjectCatalogService
{
    /**
     * Obtener proyectos para el catálogo con filtros y paginación
     */
    public function getProjectsForCatalog(array $filters = []): LengthAwarePaginator
    {
        $query = Proyecto::query()
            ->where('estado', 'en_recaudacion')
            ->where('activo', true)
            ->with([
                'categoria',
                'agricultor:id,name',
                'imagenes' => fn($q) => $q->orderBy('es_principal', 'desc')->orderBy('orden')->limit(1)
            ]);

        // Filtro por categoría
        if (!empty($filters['categoria'])) {
            $query->whereHas('categoria', function ($q) use ($filters) {
                $q->where('codigo', $filters['categoria']);
            });
        }

        // Búsqueda por nombre
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('tipo_cultivo', 'like', "%{$search}%")
                  ->orWhere('ubicacion', 'like', "%{$search}%");
            });
        }

        // Filtro por nivel de riesgo
        if (!empty($filters['riesgo'])) {
            $query->where('nivel_riesgo', $filters['riesgo']);
        }

        // Filtro por ROI mínimo
        if (!empty($filters['roi_min'])) {
            $query->where('roi_anual', '>=', (float) $filters['roi_min']);
        }

        // Filtro por plazo máximo
        if (!empty($filters['plazo_max'])) {
            $query->where('duracion_meses', '<=', (int) $filters['plazo_max']);
        }

        // Filtro por inversión máxima (inversión mínima del proyecto)
        if (!empty($filters['inversion_max'])) {
            $query->where('inversion_minima', '<=', (float) $filters['inversion_max']);
        }

        // Ordenamiento
        $sortBy = $filters['sort'] ?? 'destacado';
        switch ($sortBy) {
            case 'roi':
                $query->orderByDesc('roi_anual');
                break;
            case 'fecha_cierre':
                $query->orderBy('fecha_cierre_recaudacion');
                break;
            case 'inversion_minima':
                $query->orderBy('inversion_minima');
                break;
            case 'reciente':
                $query->orderByDesc('created_at');
                break;
            case 'destacado':
            default:
                $query->orderByDesc('destacado')
                      ->orderBy('orden_destacado')
                      ->orderByDesc('created_at');
                break;
        }

        return $query->paginate($filters['per_page'] ?? 12);
    }

    /**
     * Obtener proyectos destacados
     */
    public function getHighlightedProjects(int $limit = 4): Collection
    {
        return Proyecto::query()
            ->where('estado', 'en_recaudacion')
            ->where('activo', true)
            ->where('destacado', true)
            ->with(['categoria', 'imagenes' => fn($q) => $q->where('es_principal', true)])
            ->orderBy('orden_destacado')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener un proyecto por su código
     */
    public function getProjectByCode(string $codigo): ?Proyecto
    {
        return Proyecto::query()
            ->where('codigo', $codigo)
            ->whereIn('estado', ['en_recaudacion', 'fondeado', 'en_ejecucion', 'en_cosecha', 'finalizado'])
            ->where('activo', true)
            ->with([
                'categoria',
                'agricultor:id,name,ciudad,pais',
                'imagenes' => fn($q) => $q->orderBy('es_principal', 'desc')->orderBy('orden'),
                'actualizaciones' => fn($q) => $q->where('visible_inversores', true)
                    ->orderByDesc('publicado_at')
                    ->limit(5),
                'documentos' => fn($q) => $q->where('verificado', true)
            ])
            ->first();
    }

    /**
     * Obtener todas las categorías activas
     */
    public function getCategories(): Collection
    {
        return CategoriaProyecto::query()
            ->activos()
            ->porOrden()
            ->get();
    }

    /**
     * Calcular porcentaje de progreso de recaudación
     */
    public function calculateProgress(Proyecto $proyecto): float
    {
        if ($proyecto->monto_objetivo <= 0) {
            return 0;
        }

        return min(100, round(($proyecto->monto_recaudado / $proyecto->monto_objetivo) * 100, 1));
    }

    /**
     * Calcular días restantes para cierre de recaudación
     */
    public function calculateDaysRemaining(Proyecto $proyecto): int
    {
        if (!$proyecto->fecha_cierre_recaudacion) {
            return 0;
        }

        $now = Carbon::now()->startOfDay();
        $cierre = Carbon::parse($proyecto->fecha_cierre_recaudacion)->startOfDay();

        if ($cierre->isPast()) {
            return 0;
        }

        return $now->diffInDays($cierre);
    }

    /**
     * Obtener estadísticas del catálogo
     */
    public function getCatalogStats(): array
    {
        $proyectos = Proyecto::query()
            ->where('estado', 'en_recaudacion')
            ->where('activo', true);

        $totalProyectos = $proyectos->count();
        $roiPromedio = $proyectos->avg('roi_anual') ?? 0;
        $totalRecaudado = $proyectos->sum('monto_recaudado');
        $totalObjetivo = $proyectos->sum('monto_objetivo');

        return [
            'total_proyectos' => $totalProyectos,
            'roi_promedio' => round($roiPromedio, 1),
            'total_recaudado' => $totalRecaudado,
            'total_objetivo' => $totalObjetivo,
            'porcentaje_global' => $totalObjetivo > 0
                ? round(($totalRecaudado / $totalObjetivo) * 100, 1)
                : 0,
        ];
    }

    /**
     * Obtener proyectos relacionados (misma categoría)
     */
    public function getRelatedProjects(Proyecto $proyecto, int $limit = 3): Collection
    {
        return Proyecto::query()
            ->where('estado', 'en_recaudacion')
            ->where('activo', true)
            ->where('id', '!=', $proyecto->id)
            ->where('categoria_id', $proyecto->categoria_id)
            ->with(['categoria', 'imagenes' => fn($q) => $q->where('es_principal', true)])
            ->orderByDesc('destacado')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener el icono según el tipo de cultivo
     */
    public function getProjectIcon(Proyecto $proyecto): string
    {
        $iconMap = [
            'limon' => 'fas fa-lemon',
            'limón' => 'fas fa-lemon',
            'citrico' => 'fas fa-lemon',
            'cítrico' => 'fas fa-lemon',
            'cafe' => 'fas fa-coffee',
            'café' => 'fas fa-coffee',
            'aguacate' => 'fas fa-leaf',
            'cacao' => 'fas fa-cookie',
            'banano' => 'fas fa-apple-alt',
            'platano' => 'fas fa-apple-alt',
            'plátano' => 'fas fa-apple-alt',
            'mango' => 'fas fa-apple-alt',
            'piña' => 'fas fa-apple-alt',
            'flores' => 'fas fa-spa',
            'palma' => 'fas fa-tree',
            'arroz' => 'fas fa-seedling',
            'maiz' => 'fas fa-seedling',
            'maíz' => 'fas fa-seedling',
        ];

        $tipoCultivo = strtolower($proyecto->tipo_cultivo ?? '');

        foreach ($iconMap as $key => $icon) {
            if (str_contains($tipoCultivo, $key)) {
                return $icon;
            }
        }

        return 'fas fa-seedling';
    }
}
