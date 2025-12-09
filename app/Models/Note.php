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
        'course_id',
        'content',
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
     * Scope: Notas de un curso específico
     */
    public function scopeByCourse($query, Course $course)
    {
        return $query->where('course_id', $course->id);
    }
}
