<?php

namespace App\Models;
use App\Models\Empresa;
 use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; 
use App\Models\Lead;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'activo',
        'ultimo_login',
        'documento_identidad',
        'tipo_documento',
        'kyc_status',
        'kyc_aprobado_por',
        'kyc_aprobado_at',
        'codigo_referido',
        'referido_por'
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
        'last_synced_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
        'ultimo_login' => 'datetime',
        'kyc_aprobado_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
public function empresa()
{
    return $this->hasOne(Empresa::class, 'usuario_id');
}

public function tieneRolEmpresa()
{
    return $this->hasRole('empresa');
}
    public function tieneEmpresa()
    {
        return $this->empresa()->exists();
    }
        public function getEmpresaActivaAttribute()
    {
        return $this->empresa()->where('activo', true)->first();
    }
public function puedeCrearEmpresa()
{
    return $this->tieneRolEmpresa() && !$this->empresa;
}
        public function clientes()
    {
        return $this->hasMany(Cliente::class, 'vendedor_id');
    }

    public function enlacesCreados()
    {
        return $this->hasMany(EnlaceAcceso::class, 'creado_por');
    }

    public function solicitudesAplicadas()
    {
        return $this->hasMany(SolicitudCotizacion::class, 'aplicada_por');
    }

    public function actualizacionesPrecios()
    {
        return $this->hasMany(ActualizacionPrecio::class, 'usuario_id');
    }

    public function esAdmin()
    {
        return $this->tipo_usuario === 'admin';
    }

    public function esVendedor()
    {
        return $this->tipo_usuario === 'vendedor';
    }

    public function registrarLogin()
    {
        $this->update(['ultimo_login' => now()]);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVendedores($query)
    {
        return $query->where('tipo_usuario', 'vendedor');
    }

    public function scopeAdministradores($query)
    {
        return $query->where('tipo_usuario', 'admin');
    }

    // Relaciones AGROMARKET
    public function billetera()
    {
        return $this->hasOne(Billetera::class, 'usuario_id');
    }

    public function inversiones()
    {
        return $this->hasMany(Inversion::class, 'usuario_id');
    }

    public function proyectosCreados()
    {
        return $this->hasMany(Proyecto::class, 'agricultor_id');
    }

    public function transaccionesBilletera()
    {
        return $this->hasMany(TransaccionBilletera::class, 'usuario_id');
    }

    public function dividendos()
    {
        return $this->hasMany(Dividendo::class, 'usuario_id');
    }

    public function retiros()
    {
        return $this->hasMany(Retiro::class, 'usuario_id');
    }

    public function depositos()
    {
        return $this->hasMany(Deposito::class, 'usuario_id');
    }

    public function documentosKyc()
    {
        return $this->hasMany(DocumentoKyc::class, 'usuario_id');
    }

    public function cuentasBancarias()
    {
        return $this->hasMany(CuentaBancaria::class, 'usuario_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }

    public function mensajesEnviados()
    {
        return $this->hasMany(Mensaje::class, 'remitente_id');
    }

    public function mensajesRecibidos()
    {
        return $this->hasMany(Mensaje::class, 'destinatario_id');
    }

    public function referidoPor()
    {
        return $this->belongsTo(User::class, 'referido_por');
    }

    public function referidos()
    {
        return $this->hasMany(User::class, 'referido_por');
    }

    public function aprobadorKyc()
    {
        return $this->belongsTo(User::class, 'kyc_aprobado_por');
    }

    public function comprasCrossFund()
    {
        return $this->hasMany(CompraCrossFund::class, 'usuario_id');
    }
}
