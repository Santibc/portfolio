<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Services\ProgressService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ProgressService $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function index()
    {
        $stats = $this->progressService->getAdminStats();

        // Progreso por categoría
        $categorias = Category::with(['courses' => function ($query) {
            $query->published()->withCount('videos');
        }])->active()->get();

        return view('admin.reports.index', compact('stats', 'categorias'));
    }

    public function students(Request $request)
    {
        $estudiantes = User::role('Estudiante')
            ->withCount('videoCompletions')
            ->orderBy('video_completions_count', 'desc')
            ->get();

        $estudiantesConProgreso = $estudiantes->map(function ($estudiante) {
            $progress = $this->progressService->getOverallProgress($estudiante);
            $estudiante->progress = $progress;
            return $estudiante;
        });

        return view('admin.reports.students', compact('estudiantesConProgreso'));
    }

    public function courseProgress(Request $request)
    {
        $cursos = Course::with(['category', 'videos'])->published()->get();

        $cursosConProgreso = $cursos->map(function ($curso) {
            $estudiantes = User::role('Estudiante')->get();
            $totalEstudiantes = $estudiantes->count();
            $completados = 0;
            $enProgreso = 0;

            foreach ($estudiantes as $estudiante) {
                $progress = $this->progressService->getCourseProgress($estudiante, $curso);
                if ($progress['is_completed']) {
                    $completados++;
                } elseif ($progress['completed_videos'] > 0) {
                    $enProgreso++;
                }
            }

            $curso->stats = [
                'total_estudiantes' => $totalEstudiantes,
                'completados' => $completados,
                'en_progreso' => $enProgreso,
                'sin_iniciar' => $totalEstudiantes - $completados - $enProgreso,
                'tasa_completacion' => $totalEstudiantes > 0
                    ? round(($completados / $totalEstudiantes) * 100)
                    : 0,
            ];

            return $curso;
        });

        return view('admin.reports.courses', compact('cursosConProgreso'));
    }

    public function studentDetail(User $estudiante)
    {
        if (!$estudiante->hasRole('Estudiante')) {
            return redirect()->route('admin.reportes.estudiantes')
                ->with('error', 'El usuario no es un estudiante.');
        }

        $progress = $this->progressService->getOverallProgress($estudiante);
        $actividadReciente = $this->progressService->getRecentActivity($estudiante, 20);

        $cursos = Course::published()->get()->map(function ($curso) use ($estudiante) {
            $curso->user_progress = $this->progressService->getCourseProgress($estudiante, $curso);
            return $curso;
        });

        return view('admin.reports.student-detail', compact('estudiante', 'progress', 'actividadReciente', 'cursos'));
    }
}
