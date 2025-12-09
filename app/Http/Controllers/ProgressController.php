<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Course;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgressController extends Controller
{
    protected ProgressService $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Marcar un video como completado
     */
    public function markVideoComplete(Request $request, Video $video): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para marcar videos como completados.',
            ], 401);
        }

        // Verificar que el curso esté publicado
        if (!$video->course->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Este curso no está disponible.',
            ], 404);
        }

        // Verificar acceso al curso
        if (!$user->canAccessCourse($video->course)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a este curso.',
            ], 403);
        }

        // Marcar como completado
        $completion = $this->progressService->markVideoAsCompleted($user, $video);

        // Obtener progreso actualizado del curso
        $courseProgress = $this->progressService->getCourseProgress($user, $video->course);

        // Verificar si se desbloqueó un nuevo curso
        $nextCourse = null;
        if ($courseProgress['is_completed']) {
            $nextCourse = Course::where('category_id', $video->course->category_id)
                ->where('order', '>', $video->course->order)
                ->where('is_published', true)
                ->orderBy('order')
                ->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Video marcado como completado.',
            'data' => [
                'video_id' => $video->id,
                'completed_at' => $completion->completed_at,
                'course_progress' => $courseProgress,
                'course_completed' => $courseProgress['is_completed'],
                'next_course' => $nextCourse ? [
                    'id' => $nextCourse->id,
                    'title' => $nextCourse->title,
                    'slug' => $nextCourse->slug,
                ] : null,
            ],
        ]);
    }

    /**
     * Desmarcar un video como completado
     */
    public function unmarkVideoComplete(Request $request, Video $video): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión.',
            ], 401);
        }

        $this->progressService->unmarkVideoAsCompleted($user, $video);

        // Obtener progreso actualizado
        $courseProgress = $this->progressService->getCourseProgress($user, $video->course);

        return response()->json([
            'success' => true,
            'message' => 'Video desmarcado.',
            'data' => [
                'video_id' => $video->id,
                'course_progress' => $courseProgress,
            ],
        ]);
    }

    /**
     * Obtener progreso general del usuario
     */
    public function getOverallProgress(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión.',
            ], 401);
        }

        $progress = $this->progressService->getOverallProgress($user);

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    /**
     * Obtener progreso de un curso específico
     */
    public function getCourseProgress(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión.',
            ], 401);
        }

        if (!$course->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Curso no encontrado.',
            ], 404);
        }

        $progress = $this->progressService->getCourseProgress($user, $course);

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    /**
     * Ver página de progreso del estudiante
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $overallProgress = $this->progressService->getOverallProgress($user);
        $recentActivity = $this->progressService->getRecentActivity($user, 20);

        // Progreso por categoría
        $progressByCategory = $this->progressService->getCoursesInProgress($user);

        // Cursos completados
        $cursosCompletados = $user->getCompletedCourses();

        // Cursos en progreso
        $cursosEnProgreso = $user->getCoursesInProgress();

        return view('progreso.index', compact(
            'overallProgress',
            'recentActivity',
            'progressByCategory',
            'cursosCompletados',
            'cursosEnProgreso'
        ));
    }
}
