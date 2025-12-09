<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Video;
use App\Models\Note;
use App\Models\VideoCompletion;
use App\Services\ProgressService;

class DashboardController extends Controller
{
    protected ProgressService $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_usuarios' => User::count(),
            'total_estudiantes' => User::role('Estudiante')->count(),
            'total_administradores' => User::role('Administrador')->count(),
            'total_categorias' => Category::count(),
            'categorias_activas' => Category::active()->count(),
            'total_cursos' => Course::count(),
            'cursos_publicados' => Course::published()->count(),
            'total_videos' => Video::count(),
            'total_notas' => Note::count(),
            'total_completaciones' => VideoCompletion::count(),
        ];

        // Duración total de contenido
        $stats['duracion_total'] = Video::sum('duration_seconds');
        $stats['duracion_formateada'] = $this->formatDuration($stats['duracion_total']);

        // Progreso general de estudiantes
        $progressStats = $this->progressService->getAdminStats();

        // Cursos más populares (con más completaciones)
        $cursosPopulares = Course::withCount(['videos as completaciones' => function ($query) {
            $query->join('video_completions', 'videos.id', '=', 'video_completions.video_id');
        }])->published()
            ->orderByDesc('completaciones')
            ->limit(5)
            ->get();

        // Actividad reciente
        $actividadReciente = VideoCompletion::with(['user', 'video.course'])
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        // Notas recientes
        $notasRecientes = Note::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'progressStats',
            'cursosPopulares',
            'actividadReciente',
            'notasRecientes'
        ));
    }

    protected function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }
}
