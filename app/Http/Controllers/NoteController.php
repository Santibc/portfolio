<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Course;
use App\Services\NoteService;
use App\Http\Requests\StoreNoteRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    protected NoteService $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    /**
     * Obtener notas de un curso
     */
    public function index(Request $request, Course $course): JsonResponse
    {
        if (!$course->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Curso no encontrado.',
            ], 404);
        }

        $perPage = $request->input('per_page', 15);
        $notas = $this->noteService->getNotesForCourse($course, $perPage);

        return response()->json([
            'success' => true,
            'data' => $notas,
        ]);
    }

    /**
     * Crear una nueva nota
     */
    public function store(StoreNoteRequest $request, Course $course): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para crear notas.',
            ], 401);
        }

        if (!$course->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Curso no encontrado.',
            ], 404);
        }

        // Verificar acceso al curso
        if (!$user->canAccessCourse($course)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a este curso.',
            ], 403);
        }

        $nota = $this->noteService->create(
            $user,
            $course,
            $request->validated()['content']
        );

        $nota->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Nota creada exitosamente.',
            'data' => $nota,
        ], 201);
    }

    /**
     * Actualizar una nota
     */
    public function update(StoreNoteRequest $request, Note $note): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión.',
            ], 401);
        }

        // Verificar permisos
        if (!$this->noteService->canEdit($user, $note)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar esta nota.',
            ], 403);
        }

        $nota = $this->noteService->update($note, $request->validated()['content']);
        $nota->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Nota actualizada exitosamente.',
            'data' => $nota,
        ]);
    }

    /**
     * Eliminar una nota
     */
    public function destroy(Request $request, Note $note): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión.',
            ], 401);
        }

        // Verificar permisos
        if (!$this->noteService->canDelete($user, $note)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta nota.',
            ], 403);
        }

        $this->noteService->delete($note);

        return response()->json([
            'success' => true,
            'message' => 'Nota eliminada exitosamente.',
        ]);
    }

    /**
     * Ver todas las notas del usuario
     */
    public function misNotas(Request $request)
    {
        $user = $request->user();
        $notas = $this->noteService->getNotesForUser($user, 20);

        return view('notas.index', compact('notas'));
    }

    /**
     * Buscar notas
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $course = null;
        if ($request->has('course_id')) {
            $course = Course::find($request->course_id);
        }

        $notas = $this->noteService->search($request->q, $course);

        return response()->json([
            'success' => true,
            'data' => $notas,
        ]);
    }
}
