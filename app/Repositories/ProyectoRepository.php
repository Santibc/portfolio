<?php

namespace App\Repositories;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ProyectoRepository
{
    /**
     * Encontrar proyectos de un agricultor específico
     *
     * @param User $agricultor
     * @return Collection
     */
    public function findByAgricultor(User $agricultor): Collection
    {
        return Proyecto::where('agricultor_id', $agricultor->id)
            ->with(['categoria', 'aprobador'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Encontrar proyectos pendientes de revisión
     *
     * @return Collection
     */
    public function findPendingReview(): Collection
    {
        return Proyecto::where('estado', 'en_revision')
            ->with(['categoria', 'agricultor', 'imagenes'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Encontrar proyectos activos (en recaudación, fondeado, en ejecución)
     *
     * @return Collection
     */
    public function findActiveProjects(): Collection
    {
        return Proyecto::whereIn('estado', ['en_recaudacion', 'fondeado', 'en_ejecucion'])
            ->where('activo', true)
            ->with(['categoria', 'agricultor', 'imagenes'])
            ->orderBy('destacado', 'desc')
            ->orderBy('orden_destacado', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Encontrar proyectos por categoría
     *
     * @param int $categoriaId
     * @return Collection
     */
    public function findByCategoria(int $categoriaId): Collection
    {
        return Proyecto::where('categoria_id', $categoriaId)
            ->where('activo', true)
            ->with(['categoria', 'agricultor', 'imagenes'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Encontrar proyecto por código
     *
     * @param string $codigo
     * @return Proyecto|null
     */
    public function findByCodigo(string $codigo): ?Proyecto
    {
        return Proyecto::where('codigo', $codigo)
            ->with(['categoria', 'agricultor', 'aprobador', 'imagenes', 'documentos'])
            ->first();
    }

    /**
     * Encontrar proyecto por ID con todas sus relaciones
     *
     * @param int $id
     * @return Proyecto|null
     */
    public function findWithRelations(int $id): ?Proyecto
    {
        return Proyecto::with([
            'categoria',
            'agricultor',
            'aprobador',
            'imagenes',
            'documentos',
            'actualizaciones' => function ($query) {
                $query->orderBy('publicado_at', 'desc');
            }
        ])->find($id);
    }

    /**
     * Contar proyectos por estado
     *
     * @param string $estado
     * @return int
     */
    public function countByEstado(string $estado): int
    {
        return Proyecto::where('estado', $estado)->count();
    }

    /**
     * Obtener proyectos destacados en recaudación
     *
     * @param int $limit
     * @return Collection
     */
    public function findFeaturedProjects(int $limit = 6): Collection
    {
        return Proyecto::where('estado', 'en_recaudacion')
            ->where('destacado', true)
            ->where('activo', true)
            ->with(['categoria', 'agricultor', 'imagenes'])
            ->orderBy('orden_destacado', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Buscar proyectos por múltiples filtros
     *
     * @param array $filters
     * @return Collection
     */
    public function search(array $filters): Collection
    {
        $query = Proyecto::query()->with(['categoria', 'agricultor', 'imagenes']);

        if (isset($filters['categoria_id'])) {
            $query->where('categoria_id', $filters['categoria_id']);
        }

        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        if (isset($filters['activo'])) {
            $query->where('activo', $filters['activo']);
        }

        // Ordenamiento
        if (isset($filters['order_by'])) {
            $orderDirection = $filters['order_direction'] ?? 'desc';
            $query->orderBy($filters['order_by'], $orderDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }
}
