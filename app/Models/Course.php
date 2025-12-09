<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
            if (empty($course->order)) {
                $course->order = static::where('category_id', $course->category_id)->max('order') + 1;
            }
        });
    }

    /**
     * Relación: Un curso pertenece a una categoría
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación: Un curso tiene muchos videos
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class)->orderBy('order');
    }

    /**
     * Relación: Un curso tiene muchas notas
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class)->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Solo cursos publicados
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: Ordenar por campo order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Obtener la URL del thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return asset($this->thumbnail);
        }
        return null;
    }

    /**
     * Contar total de videos
     */
    public function getTotalVideosAttribute(): int
    {
        return $this->videos()->count();
    }

    /**
     * Obtener duración total en segundos
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->videos()->sum('duration_seconds');
    }

    /**
     * Obtener duración formateada
     */
    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->total_duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }

    /**
     * Verificar si un usuario puede acceder al curso (lógica secuencial)
     */
    public function canBeAccessedBy(User $user): bool
    {
        // Admin siempre puede acceder
        if ($user->hasRole('Administrador')) {
            return true;
        }

        // Si es el primer curso de la categoría, siempre es accesible
        if ($this->order == 1) {
            return true;
        }

        // Obtener el curso anterior en la misma categoría
        $previousCourse = Course::where('category_id', $this->category_id)
            ->where('order', '<', $this->order)
            ->where('is_published', true)
            ->orderBy('order', 'desc')
            ->first();

        // Si no hay curso anterior, es accesible
        if (!$previousCourse) {
            return true;
        }

        // Verificar que el curso anterior esté completado
        return $user->hasCourseCompleted($previousCourse);
    }

    /**
     * Obtener el progreso de un usuario en este curso
     */
    public function getProgressForUser(User $user): int
    {
        $totalVideos = $this->videos()->count();
        if ($totalVideos === 0) {
            return 0;
        }

        $completedVideos = VideoCompletion::where('user_id', $user->id)
            ->whereIn('video_id', $this->videos()->pluck('id'))
            ->count();

        return (int) round(($completedVideos / $totalVideos) * 100);
    }

    /**
     * Verificar si un usuario completó este curso
     */
    public function isCompletedBy(User $user): bool
    {
        return $this->getProgressForUser($user) === 100;
    }

    /**
     * Obtener la ruta para ver el curso
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Obtener el siguiente curso en la categoría
     */
    public function getNextCourseAttribute(): ?Course
    {
        return Course::where('category_id', $this->category_id)
            ->where('order', '>', $this->order)
            ->where('is_published', true)
            ->orderBy('order')
            ->first();
    }

    /**
     * Obtener el curso anterior en la categoría
     */
    public function getPreviousCourseAttribute(): ?Course
    {
        return Course::where('category_id', $this->category_id)
            ->where('order', '<', $this->order)
            ->where('is_published', true)
            ->orderBy('order', 'desc')
            ->first();
    }
}
