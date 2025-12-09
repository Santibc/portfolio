<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
            if (empty($category->order)) {
                $category->order = static::max('order') + 1;
            }
        });
    }

    /**
     * Relación: Una categoría tiene muchos cursos
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class)->orderBy('order');
    }

    /**
     * Cursos publicados de la categoría
     */
    public function publishedCourses(): HasMany
    {
        return $this->hasMany(Course::class)
            ->where('is_published', true)
            ->orderBy('order');
    }

    /**
     * Scope: Solo categorías activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordenar por campo order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Obtener la URL de la imagen
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset($this->image);
        }
        return null;
    }

    /**
     * Contar cursos publicados
     */
    public function getPublishedCoursesCountAttribute(): int
    {
        return $this->courses()->where('is_published', true)->count();
    }

    /**
     * Obtener la ruta para ver la categoría
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
