<?php

namespace App\Services;

use App\Models\Note;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NoteService
{
    /**
     * Obtener notas de un curso (públicas para todos)
     */
    public function getNotesForCourse(Course $course, int $perPage = 15): LengthAwarePaginator
    {
        return Note::with('user')
            ->where('course_id', $course->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtener todas las notas de un curso sin paginación
     */
    public function getAllNotesForCourse(Course $course): Collection
    {
        return Note::with('user')
            ->where('course_id', $course->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener notas de un usuario
     */
    public function getNotesForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Note::with('course')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Crear una nueva nota
     */
    public function create(User $user, Course $course, string $content): Note
    {
        return Note::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'content' => $content,
        ]);
    }

    /**
     * Actualizar una nota
     */
    public function update(Note $note, string $content): Note
    {
        $note->update(['content' => $content]);
        return $note->fresh();
    }

    /**
     * Eliminar una nota
     */
    public function delete(Note $note): bool
    {
        return $note->delete();
    }

    /**
     * Verificar si un usuario puede editar una nota
     */
    public function canEdit(User $user, Note $note): bool
    {
        return $note->user_id === $user->id || $user->hasRole('Administrador');
    }

    /**
     * Verificar si un usuario puede eliminar una nota
     */
    public function canDelete(User $user, Note $note): bool
    {
        return $note->user_id === $user->id || $user->hasRole('Administrador');
    }

    /**
     * Obtener el conteo de notas por curso
     */
    public function getNotesCountByCourse(Course $course): int
    {
        return Note::where('course_id', $course->id)->count();
    }

    /**
     * Obtener notas recientes de todos los cursos
     */
    public function getRecentNotes(int $limit = 10): Collection
    {
        return Note::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Buscar notas por contenido
     */
    public function search(string $query, ?Course $course = null): Collection
    {
        $notes = Note::with(['user', 'course'])
            ->where('content', 'like', "%{$query}%");

        if ($course) {
            $notes->where('course_id', $course->id);
        }

        return $notes->orderBy('created_at', 'desc')->get();
    }

    /**
     * Obtener estadísticas de notas
     */
    public function getStats(): array
    {
        return [
            'total' => Note::count(),
            'by_course' => Note::selectRaw('course_id, COUNT(*) as count')
                ->groupBy('course_id')
                ->with('course:id,title')
                ->get(),
            'today' => Note::whereDate('created_at', today())->count(),
            'this_week' => Note::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
    }

    /**
     * Obtener contribuidores más activos (usuarios con más notas)
     */
    public function getTopContributors(int $limit = 5): Collection
    {
        return Note::selectRaw('user_id, COUNT(*) as notes_count')
            ->groupBy('user_id')
            ->orderByDesc('notes_count')
            ->limit($limit)
            ->with('user:id,name')
            ->get();
    }
}
