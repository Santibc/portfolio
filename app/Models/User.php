<?php

namespace App\Models;

use App\Enums\RolUsuario;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'theme',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /** Usuario de caja: solo puede abrir/cerrar turno y registrar ventas (ver RestringirRolVentas). */
    public function esVendedor(): bool
    {
        return $this->hasRole('ventas') && ! $this->isAdmin();
    }

    /** Rol asignado desde el módulo de usuarios (null si no tiene ninguno conocido). */
    public function rolUsuario(): ?RolUsuario
    {
        foreach (RolUsuario::cases() as $rol) {
            if ($this->hasRole($rol->value)) {
                return $rol;
            }
        }

        return null;
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('uploads/profile-photos/' . $this->profile_photo);
        }

        return '';
    }

    public function hasProfilePhoto(): bool
    {
        return !empty($this->profile_photo);
    }
}
