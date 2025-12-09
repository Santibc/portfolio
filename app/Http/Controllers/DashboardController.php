<?php

namespace App\Http\Controllers;

use App\Services\ProgressService;
use App\Services\CategoryService;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ProgressService $progressService;
    protected CategoryService $categoryService;

    public function __construct(ProgressService $progressService, CategoryService $categoryService)
    {
        $this->progressService = $progressService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Redirigir admin al dashboard de admin
        if ($user->hasRole('Administrador')) {
            return redirect()->route('admin.dashboard');
        }

        // Dashboard de estudiante
        $overallProgress = $this->progressService->getOverallProgress($user);
        $recentActivity = $this->progressService->getRecentActivity($user, 5);
        $nextVideo = $this->progressService->getNextVideoToWatch($user);
        $nextCourse = $this->progressService->getNextAvailableCourse($user);

        // Cursos en progreso
        $cursosEnProgreso = $user->getCoursesInProgress();

        // Cursos completados
        $cursosCompletados = $user->getCompletedCourses();

        // Categorías activas con sus cursos
        $categorias = Category::with(['courses' => function ($query) {
            $query->published()->ordered()->withCount('videos');
        }])->active()->ordered()->get();

        return view('dashboard', compact(
            'overallProgress',
            'recentActivity',
            'nextVideo',
            'nextCourse',
            'cursosEnProgreso',
            'cursosCompletados',
            'categorias'
        ));
    }
}
