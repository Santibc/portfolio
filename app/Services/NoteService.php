<?php

namespace App\Services;

use App\Models\Note;
use App\Models\User;
use App\Models\Video;
use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NoteService
{
    /**
     * Obtener notas de un video
     */
    public function getNotesForVideo(Video $video, int $perPage = 15): LengthAwarePaginator
    {
        return Note::with('user')
            ->where('video_id', $video->id)
            ->orderBy('timestamp_seconds', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtener todas las notas de un video sin paginación
     */
    public function getAllNotesForVideo(Video $video): Collection
    {
        return Note::with('user')
            ->where('video_id', $video->id)
            ->orderBy('timestamp_seconds', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener notas de un usuario en un video específico
     */
    public function getUserNotesForVideo(User $user, Video $video): Collection
    {
        return Note::where('user_id', $user->id)
            ->where('video_id', $video->id)
            ->orderBy('timestamp_seconds', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener notas de un usuario
     */
    public function getNotesForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Note::with(['video.course'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Crear una nueva nota en un video
     */
    public function create(User $user, Video $video, string $content, ?int $timestampSeconds = null): Note
    {
        return Note::create([
            'user_id' => $user->id,
            'video_id' => $video->id,
            'content' => $content,
            'timestamp_seconds' => $timestampSeconds,
        ]);
    }

    /**
     * Actualizar una nota
     */
    public function update(Note $note, string $content, ?int $timestampSeconds = null): Note
    {
        $data = ['content' => $content];

        if ($timestampSeconds !== null) {
            $data['timestamp_seconds'] = $timestampSeconds;
        }

        $note->update($data);
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
     * Obtener el conteo de notas por video
     */
    public function getNotesCountByVideo(Video $video): int
    {
        return Note::where('video_id', $video->id)->count();
    }

    /**
     * Obtener notas recientes
     */
    public function getRecentNotes(int $limit = 10): Collection
    {
        return Note::with(['user', 'video.course'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Buscar notas por contenido
     */
    public function search(string $query, ?Video $video = null): Collection
    {
        $notes = Note::with(['user', 'video.course'])
            ->where('content', 'like', "%{$query}%");

        if ($video) {
            $notes->where('video_id', $video->id);
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
            'by_video' => Note::selectRaw('video_id, COUNT(*) as count')
                ->groupBy('video_id')
                ->with('video:id,title,course_id')
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

    /**
     * Obtener todas las notas de un curso (a través de sus videos)
     */
    public function getNotesForCourse(Course $course): Collection
    {
        $videoIds = $course->videos()->pluck('id');

        return Note::with(['user', 'video'])
            ->whereIn('video_id', $videoIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
