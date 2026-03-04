<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tablero extends Model
{
    use SoftDeletes;

    protected $table = 'tableros';

    protected $fillable = [
        'nombre', 'descripcion', 'color_fondo', 'imagen_fondo',
        'visibilidad', 'roles_visibles', 'archivado', 'creado_por', 'obra_id',
    ];

    protected $casts = [
        'roles_visibles' => 'array',
        'archivado' => 'boolean',
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function miembros(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tablero_miembros')
            ->withPivot('rol')
            ->withTimestamps();
    }

    public function columnas(): HasMany
    {
        return $this->hasMany(TableroColumna::class)->orderBy('posicion');
    }

    public function etiquetas(): HasMany
    {
        return $this->hasMany(TableroEtiqueta::class);
    }

    public function tarjetas(): HasManyThrough
    {
        return $this->hasManyThrough(Tarjeta::class, TableroColumna::class, 'tablero_id', 'columna_id');
    }

    public function esAccesiblePor(User $user): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->hasRole('Auditor')) return true;
        if ($this->visibilidad === 'todos') return true;

        if ($this->visibilidad === 'roles') {
            $rolesUsuario = $user->getRoleNames()->toArray();
            return !empty(array_intersect($rolesUsuario, $this->roles_visibles ?? []));
        }

        return $this->miembros()->where('users.id', $user->id)->exists();
    }

    public function esPropietario(User $user): bool
    {
        return $this->creado_por === $user->id || $this->miembros()
            ->where('users.id', $user->id)
            ->wherePivot('rol', 'propietario')
            ->exists();
    }

    public function puedeEditar(User $user): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->hasRole('Auditor')) return false;

        return $this->miembros()
            ->where('users.id', $user->id)
            ->whereIn('tablero_miembros.rol', ['propietario', 'editor'])
            ->exists();
    }

    public function scopeActivos($query)
    {
        return $query->where('archivado', false);
    }

    public function scopeAccesiblesPor($query, User $user)
    {
        if ($user->isAdmin() || $user->hasRole('Auditor')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('visibilidad', 'todos')
              ->orWhere(function ($q2) use ($user) {
                  $q2->where('visibilidad', 'roles');
                  foreach ($user->getRoleNames() as $rol) {
                      $q2->orWhereJsonContains('roles_visibles', $rol);
                  }
              })
              ->orWhereHas('miembros', function ($q3) use ($user) {
                  $q3->where('users.id', $user->id);
              });
        });
    }
}
