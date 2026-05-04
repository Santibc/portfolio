<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'profile_photo',
        'ultimo_login',
        'theme',
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
        'ultimo_login' => 'datetime',
    ];

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
     * Obtener la URL de la foto de perfil
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('uploads/profile-photos/' . $this->profile_photo);
        }

        return '';
    }

    /**
     * Verificar si tiene foto de perfil
     */
    public function hasProfilePhoto(): bool
    {
        return !empty($this->profile_photo);
    }

    // === SINDEN Relaciones ===

    public function ordenesCreadas()
    {
        return $this->hasMany(Orden::class, 'creado_por');
    }

    public function piezasAsignadas()
    {
        return $this->hasMany(OrdenPieza::class, 'operario_actual_id');
    }

    public function asignacionesRecibidas()
    {
        return $this->hasMany(AsignacionPieza::class, 'asignado_a_id');
    }

    public function asignacionesRealizadas()
    {
        return $this->hasMany(AsignacionPieza::class, 'asignado_por_id');
    }

    public function historialAvances()
    {
        return $this->hasMany(HistorialAvance::class, 'operario_id');
    }

    public function pagosRegistrados()
    {
        return $this->hasMany(Pago::class, 'registrado_por');
    }

    public function pagosAprobados()
    {
        return $this->hasMany(Pago::class, 'aprobado_por');
    }

    public function fotosSubidas()
    {
        return $this->hasMany(OrdenFoto::class, 'subido_por');
    }

    public function comentariosOrden()
    {
        return $this->hasMany(OrdenComentario::class, 'usuario_id');
    }

    public function actividades()
    {
        return $this->hasMany(RegistroActividad::class, 'usuario_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }

    public function notificacionesNoLeidas()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id')->where('leida', false);
    }

    public function garantiasAsignadas()
    {
        return $this->hasMany(DevolucionGarantia::class, 'operario_asignado_id');
    }

    public function isOperario(): bool
    {
        return $this->hasRole('Operario');
    }

    public function isRecepcion(): bool
    {
        return $this->hasRole('Recepcion');
    }

    public function isContabilidad(): bool
    {
        return $this->hasRole('Contabilidad');
    }
}
