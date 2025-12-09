<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use App\Services\ProgressService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;
    protected ProgressService $progressService;

    public function __construct(CategoryService $categoryService, ProgressService $progressService)
    {
        $this->categoryService = $categoryService;
        $this->progressService = $progressService;
    }

    /**
     * Mostrar todas las categorías activas
     */
    public function index(Request $request)
    {
        $categorias = Category::with(['courses' => function ($query) {
            $query->published()->ordered()->withCount('videos');
        }])
            ->active()
            ->ordered()
            ->withCount(['courses' => function ($query) {
                $query->published();
            }])
            ->get();

        $user = $request->user();

        // Agregar progreso del usuario a cada categoría
        $categorias = $categorias->map(function ($categoria) use ($user) {
            $totalVideos = 0;
            $completedVideos = 0;

            foreach ($categoria->courses as $curso) {
                $totalVideos += $curso->videos_count;
                if ($user) {
                    $completedVideos += $user->videoCompletions()
                        ->whereIn('video_id', $curso->videos()->pluck('id'))
                        ->count();
                }
            }

            $categoria->user_progress = [
                'total_videos' => $totalVideos,
                'completed_videos' => $completedVideos,
                'percentage' => $totalVideos > 0 ? round(($completedVideos / $totalVideos) * 100) : 0,
            ];

            return $categoria;
        });

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Mostrar una categoría con sus cursos
     */
    public function show(Request $request, Category $categoria)
    {
        // Verificar que la categoría esté activa
        if (!$categoria->is_active) {
            abort(404);
        }

        $categoria->load(['courses' => function ($query) {
            $query->published()->ordered()->withCount('videos');
        }]);

        $user = $request->user();

        // Agregar progreso del usuario a cada curso
        $categoria->courses = $categoria->courses->map(function ($curso) use ($user) {
            if ($user) {
                $curso->user_progress = [
                    'percentage' => $user->getCourseProgressPercentage($curso),
                    'is_completed' => $user->hasCourseCompleted($curso),
                    'can_access' => $user->canAccessCourse($curso),
                ];
            } else {
                $curso->user_progress = [
                    'percentage' => 0,
                    'is_completed' => false,
                    'can_access' => false,
                ];
            }
            return $curso;
        });

        // Calcular progreso general de la categoría
        $totalVideos = $categoria->courses->sum('videos_count');
        $completedVideos = 0;

        if ($user) {
            foreach ($categoria->courses as $curso) {
                $completedVideos += $user->videoCompletions()
                    ->whereIn('video_id', $curso->videos()->pluck('id'))
                    ->count();
            }
        }

        $categoriaProgress = [
            'total_videos' => $totalVideos,
            'completed_videos' => $completedVideos,
            'percentage' => $totalVideos > 0 ? round(($completedVideos / $totalVideos) * 100) : 0,
        ];

        return view('categorias.show', compact('categoria', 'categoriaProgress'));
    }
}
