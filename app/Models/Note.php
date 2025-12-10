<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'content',
        'timestamp_seconds',
    ];

    protected $casts = [
        'timestamp_seconds' => 'integer',
    ];

    /**
     * Relación: Una nota pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una nota pertenece a un curso
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relación: Una nota pertenece a un video
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Obtener el curso a través del video
     */
    public function getCourseAttribute(): ?Course
    {
        return $this->video?->course;
    }

    /**
     * Verificar si un usuario es el autor de la nota
     */
    public function isAuthor(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Obtener las iniciales del autor
     */
    public function getAuthorInitialsAttribute(): string
    {
        $name = $this->user->name ?? 'U';
        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }

        return $initials ?: 'U';
    }

    /**
     * Obtener el timestamp formateado (MM:SS o HH:MM:SS)
     */
    public function getFormattedTimestampAttribute(): ?string
    {
        if ($this->timestamp_seconds === null) {
            return null;
        }

        $seconds = $this->timestamp_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%d:%02d', $minutes, $secs);
    }

    /**
     * Scope: Ordenar por fecha más reciente
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Notas de un usuario específico
     */
    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope: Notas de un video específico
     */
    public function scopeByVideo($query, Video $video)
    {
        return $query->where('video_id', $video->id);
    }

    /**
     * Scope: Ordenar por timestamp del video
     */
    public function scopeOrderByTimestamp($query)
    {
        return $query->orderBy('timestamp_seconds', 'asc');
    }
}
