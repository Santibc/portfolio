<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Video;
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
     * Obtener notas de un video
     */
    public function index(Request $request, Video $video): JsonResponse
    {
        $user = $request->user();
        $course = $video->course;

        // Verificar que el curso esté publicado
        if (!$course->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Video no encontrado.',
            ], 404);
        }

        // Verificar acceso al curso
        if ($user && !$user->canAccessCourse($course)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a este video.',
            ], 403);
        }

        $notas = $this->noteService->getAllNotesForVideo($video);

        return response()->json([
            'success' => true,
            'data' => $notas,
        ]);
    }

    /**
     * Crear una nueva nota en un video
     */
    public function store(StoreNoteRequest $request, Video $video): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para crear notas.',
            ], 401);
        }

        $course = $video->course;

        if (!$course->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Video no encontrado.',
            ], 404);
        }

        // Verificar acceso al curso
        if (!$user->canAccessCourse($course)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a este video.',
            ], 403);
        }

        $nota = $this->noteService->create(
            $user,
            $video,
            $request->validated()['content'],
            $request->input('timestamp_seconds')
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

        $nota = $this->noteService->update(
            $note,
            $request->validated()['content'],
            $request->input('timestamp_seconds')
        );
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
}
