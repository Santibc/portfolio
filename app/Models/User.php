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
        'fecha_nacimiento',
        'pais',
        'ciudad',
        'direccion',
        'foto_perfil',
        'kyc_status',
        'kyc_aprobado_por',
        'kyc_aprobado_at',
        'kyc_notas',
        'codigo_referido',
        'referido_por',
        // Campos v2.0
        'creado_por_admin',
        'admin_creador_id',
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
        'deleted_at' => 'datetime',
        'fecha_nacimiento' => 'date',
        // Campos v2.0
        'creado_por_admin' => 'boolean',
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

    // ==================== RELACIONES v2.0 ====================

    /**
     * Perfil extendido del agricultor (datos de Fase 2)
     */
    public function perfilAgricultor()
    {
        return $this->hasOne(PerfilAgricultor::class, 'user_id');
    }

    /**
     * Familiares del agricultor
     */
    public function familia()
    {
        return $this->hasMany(FamiliaAgricultor::class, 'agricultor_id');
    }

    /**
     * Admin que creó este usuario (si fue creado por admin)
     */
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'admin_creador_id');
    }

    /**
     * Usuarios creados por este admin
     */
    public function usuariosCreados()
    {
        return $this->hasMany(User::class, 'admin_creador_id');
    }

    /**
     * Proyectos creados por este admin (cuando admin registra proyectos)
     */
    public function proyectosRegistradosComoAdmin()
    {
        return $this->hasMany(Proyecto::class, 'admin_creador_id');
    }

    // ==================== ACCESSORS v2.0 ====================

    /**
     * Verificar si el usuario fue creado por un admin
     */
    public function getFueCreadoPorAdminAttribute(): bool
    {
        return $this->creado_por_admin === true;
    }

    /**
     * Obtener URL de la foto de perfil o placeholder
     */
    public function getFotoPerfilUrlAttribute(): string
    {
        if ($this->foto_perfil) {
            return asset($this->foto_perfil);
        }

        return asset('images/default-avatar.png');
    }

    // ==================== SCOPES v2.0 ====================

    /**
     * Filtrar usuarios creados por admin
     */
    public function scopeCreadosPorAdmin($query)
    {
        return $query->where('creado_por_admin', true);
    }

    /**
     * Filtrar agricultores
     */
    public function scopeAgricultores($query)
    {
        return $query->role('Agricultor');
    }

    /**
     * Filtrar inversionistas
     */
    public function scopeInversionistas($query)
    {
        return $query->role('Inversionista');
    }
}
