<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relación: Un usuario tiene muchas completaciones de videos
     */
    public function videoCompletions(): HasMany
    {
        return $this->hasMany(VideoCompletion::class);
    }

    /**
     * Relación: Un usuario tiene muchas notas
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Obtener los videos completados para un curso específico
     */
    public function getCompletedVideosForCourse(Course $course): Collection
    {
        return $this->videoCompletions()
            ->whereIn('video_id', $course->videos()->pluck('id'))
            ->get();
    }

    /**
     * Obtener el porcentaje de progreso en un curso
     */
    public function getCourseProgressPercentage(Course $course): int
    {
        $totalVideos = $course->videos()->count();
        if ($totalVideos === 0) {
            return 0;
        }

        $completedVideos = $this->getCompletedVideosForCourse($course)->count();
        return (int) round(($completedVideos / $totalVideos) * 100);
    }

    /**
     * Verificar si el usuario completó un curso
     */
    public function hasCourseCompleted(Course $course): bool
    {
        return $this->getCourseProgressPercentage($course) === 100;
    }

    /**
     * Verificar si el usuario puede acceder a un curso (lógica secuencial)
     */
    public function canAccessCourse(Course $course): bool
    {
        // Admin siempre puede acceder
        if ($this->hasRole('Administrador')) {
            return true;
        }

        // Si el curso no está publicado, no puede acceder
        if (!$course->is_published) {
            return false;
        }

        // Si es el primer curso de la categoría, siempre es accesible
        if ($course->order == 1) {
            return true;
        }

        // Obtener cursos anteriores de la misma categoría
        $previousCourses = Course::where('category_id', $course->category_id)
            ->where('order', '<', $course->order)
            ->where('is_published', true)
            ->orderBy('order')
            ->get();

        // Verificar que todos los cursos anteriores estén completados
        foreach ($previousCourses as $previousCourse) {
            if (!$this->hasCourseCompleted($previousCourse)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtener todos los cursos completados por el usuario
     */
    public function getCompletedCourses(): Collection
    {
        $courses = Course::published()->get();
        return $courses->filter(function ($course) {
            return $this->hasCourseCompleted($course);
        });
    }

    /**
     * Obtener cursos en progreso (iniciados pero no completados)
     */
    public function getCoursesInProgress(): Collection
    {
        $courses = Course::published()->get();
        return $courses->filter(function ($course) {
            $progress = $this->getCourseProgressPercentage($course);
            return $progress > 0 && $progress < 100;
        });
    }

    /**
     * Obtener el total de videos completados
     */
    public function getTotalCompletedVideosAttribute(): int
    {
        return $this->videoCompletions()->count();
    }

    /**
     * Obtener el progreso general del usuario (% de todos los cursos)
     */
    public function getOverallProgressAttribute(): int
    {
        $totalCourses = Course::published()->count();
        if ($totalCourses === 0) {
            return 0;
        }

        $completedCourses = $this->getCompletedCourses()->count();
        return (int) round(($completedCourses / $totalCourses) * 100);
    }

    /**
     * Obtener las iniciales del nombre
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }

        return $initials ?: 'U';
    }

    /**
     * Verificar si es administrador
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Administrador');
    }

    /**
     * Verificar si es estudiante
     */
    public function isEstudiante(): bool
    {
        return $this->hasRole('Estudiante');
    }
}
