<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trabajador extends Model
{
    use SoftDeletes;

    protected $table = 'trabajadores';

    protected $fillable = [
        'user_id',
        'tipo_relacion',
        'nombre',
        'apellidos',
        'dni',
        'email',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'fecha_alta',
        'fecha_baja',
        'categoria_convenio',
        'salario_bruto_mensual',
        'coste_empresa_dia',
        'coste_hora',
        'vacaciones_anuales',
        'vacaciones_acumuladas',
        'antiguedad',
        'subcontrata_id',
        'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_alta' => 'date',
        'fecha_baja' => 'date',
        'antiguedad' => 'date',
        'salario_bruto_mensual' => 'decimal:2',
        'coste_empresa_dia' => 'decimal:2',
        'coste_hora' => 'decimal:2',
        'vacaciones_acumuladas' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subcontrata(): BelongsTo
    {
        return $this->belongsTo(Subcontrata::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(TrabajadorDocumento::class);
    }

    public function formaciones(): HasMany
    {
        return $this->hasMany(TrabajadorFormacion::class);
    }

    public function historialDisciplinario(): HasMany
    {
        return $this->hasMany(TrabajadorHistorialDisciplinario::class);
    }

    public function fichajes(): HasMany
    {
        return $this->hasMany(Fichaje::class);
    }

    public function episEntregados(): HasMany
    {
        return $this->hasMany(EpiEntrega::class);
    }

    public function cuadrillas(): BelongsToMany
    {
        return $this->belongsToMany(Cuadrilla::class, 'cuadrilla_trabajadores')
            ->withPivot(['fecha_incorporacion', 'fecha_salida', 'activo'])
            ->withTimestamps();
    }

    public function obras(): BelongsToMany
    {
        return $this->belongsToMany(Obra::class, 'obra_trabajadores')
            ->withPivot(['fecha_inicio', 'fecha_fin', 'rol', 'activo'])
            ->withTimestamps();
    }

    public function cuadrillaActual()
    {
        return $this->cuadrillas()->wherePivot('activo', true)->first();
    }

    public function cuadrillasLideradas(): HasMany
    {
        return $this->hasMany(Cuadrilla::class, 'capataz_id');
    }

    public function primas(): HasMany
    {
        return $this->hasMany(PrimaTrabajador::class);
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function getEsPropioAttribute(): bool
    {
        return $this->tipo_relacion === 'propio';
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePropios($query)
    {
        return $query->where('tipo_relacion', 'propio');
    }

    public function scopeSubcontratas($query)
    {
        return $query->where('tipo_relacion', 'subcontrata');
    }
}
