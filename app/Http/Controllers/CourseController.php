<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Video;
use App\Services\CourseService;
use App\Services\ProgressService;
use App\Services\NoteService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected CourseService $courseService;
    protected ProgressService $progressService;
    protected NoteService $noteService;

    public function __construct(
        CourseService $courseService,
        ProgressService $progressService,
        NoteService $noteService
    ) {
        $this->courseService = $courseService;
        $this->progressService = $progressService;
        $this->noteService = $noteService;
    }

    /**
     * Mostrar todos los cursos publicados
     */
    public function index(Request $request)
    {
        $cursos = Course::with(['category', 'videos'])
            ->published()
            ->ordered()
            ->withCount('videos')
            ->get();

        $user = $request->user();

        // Agregar progreso del usuario
        $cursos = $cursos->map(function ($curso) use ($user) {
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

        // Agrupar por categoría
        $cursosPorCategoria = $cursos->groupBy('category_id');

        return view('cursos.index', compact('cursos', 'cursosPorCategoria'));
    }

    /**
     * Mostrar un curso con sus videos y documentos
     */
    public function show(Request $request, Course $course)
    {
        // Verificar que el curso esté publicado
        if (!$course->is_published) {
            abort(404);
        }

        $user = $request->user();

        // Verificar acceso secuencial
        if ($user && !$user->canAccessCourse($course)) {
            return redirect()->route('cursos.index')
                ->with('warning', 'Debes completar los cursos anteriores para acceder a este curso.');
        }

        $course->load(['category', 'videos' => function ($query) {
            $query->ordered();
        }, 'documents' => function ($query) {
            $query->ordered();
        }]);

        // Progreso del usuario en el curso
        $courseProgress = null;
        if ($user) {
            $courseProgress = $this->progressService->getCourseProgress($user, $course);
        }

        // Marcar videos completados
        $course->videos = $course->videos->map(function ($video) use ($user) {
            $video->is_completed = $user ? $video->isCompletedBy($user) : false;
            return $video;
        });

        // Crear colección mixta de contenido (videos + documentos) ordenada
        $courseContent = collect();

        foreach ($course->videos as $video) {
            $courseContent->push([
                'type' => 'video',
                'item' => $video,
                'order' => $video->order,
            ]);
        }

        foreach ($course->documents as $document) {
            $courseContent->push([
                'type' => 'document',
                'item' => $document,
                'order' => $document->order,
            ]);
        }

        // Ordenar por campo order
        $courseContent = $courseContent->sortBy('order')->values();

        return view('cursos.show', compact('course', 'courseProgress', 'courseContent'));
    }

    /**
     * Ver un video específico del curso
     */
    public function video(Request $request, Course $course, Video $video)
    {
        // Verificar que el curso esté publicado
        if (!$course->is_published) {
            abort(404);
        }

        // Verificar que el video pertenece al curso
        if ($video->course_id !== $course->id) {
            abort(404);
        }

        $user = $request->user();

        // Verificar acceso secuencial
        if ($user && !$user->canAccessCourse($course)) {
            return redirect()->route('cursos.index')
                ->with('warning', 'Debes completar los cursos anteriores para acceder a este curso.');
        }

        $course->load(['category', 'videos' => function ($query) {
            $query->ordered();
        }]);

        // Marcar videos completados y obtener siguiente video
        $previousVideo = null;
        $nextVideo = null;
        $currentIndex = null;

        foreach ($course->videos as $index => $v) {
            $v->is_completed = $user ? $v->isCompletedBy($user) : false;

            if ($v->id === $video->id) {
                $currentIndex = $index;
            }
        }

        if ($currentIndex !== null) {
            $previousVideo = $currentIndex > 0 ? $course->videos[$currentIndex - 1] : null;
            $nextVideo = $currentIndex < count($course->videos) - 1 ? $course->videos[$currentIndex + 1] : null;
        }

        // Progreso del curso
        $courseProgress = null;
        if ($user) {
            $courseProgress = $this->progressService->getCourseProgress($user, $course);
        }

        // Verificar si el video actual está completado
        $videoCompleted = $user ? $video->isCompletedBy($user) : false;

        return view('cursos.video', compact(
            'course',
            'video',
            'previousVideo',
            'nextVideo',
            'courseProgress',
            'videoCompleted'
        ));
    }
}
