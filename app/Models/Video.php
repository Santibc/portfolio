<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'video_path',
        'duration_seconds',
        'order',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            if (empty($video->order)) {
                $video->order = static::where('course_id', $video->course_id)->max('order') + 1;
            }
        });

        // Eliminar archivo de video cuando se elimina el registro
        static::deleting(function ($video) {
            if ($video->video_path && file_exists(public_path($video->video_path))) {
                unlink(public_path($video->video_path));
            }
        });
    }

    /**
     * Relación: Un video pertenece a un curso
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relación: Un video tiene muchas completaciones
     */
    public function completions(): HasMany
    {
        return $this->hasMany(VideoCompletion::class);
    }

    /**
     * Relación: Un video tiene muchas notas
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class)->orderBy('timestamp_seconds')->orderBy('created_at', 'desc');
    }

    /**
     * Obtener la URL del video
     */
    public function getVideoUrlAttribute(): string
    {
        return asset($this->video_path);
    }

    /**
     * Obtener duración formateada (MM:SS o HH:MM:SS)
     */
    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->duration_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%d:%02d', $minutes, $secs);
    }

    /**
     * Verificar si un usuario completó este video
     */
    public function isCompletedBy(User $user): bool
    {
        return $this->completions()->where('user_id', $user->id)->exists();
    }

    /**
     * Marcar video como completado por un usuario
     */
    public function markAsCompletedBy(User $user): VideoCompletion
    {
        return VideoCompletion::firstOrCreate([
            'user_id' => $user->id,
            'video_id' => $this->id,
        ], [
            'completed_at' => now(),
        ]);
    }

    /**
     * Obtener el siguiente video en el curso
     */
    public function getNextVideoAttribute(): ?Video
    {
        return Video::where('course_id', $this->course_id)
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    /**
     * Obtener el video anterior en el curso
     */
    public function getPreviousVideoAttribute(): ?Video
    {
        return Video::where('course_id', $this->course_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }

    /**
     * Scope: Ordenar por campo order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
