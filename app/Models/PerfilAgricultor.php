<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfilAgricultor extends Model
{
    use HasFactory;

    protected $table = 'perfiles_agricultor';

    protected $fillable = [
        'user_id',
        // Tipo de persona
        'tipo_persona',
        // Datos empresa
        'nombre_empresa',
        'nit',
        'representante_legal',
        'direccion_finca',
        // Seguros
        'cultivo_asegurado',
        // Experiencia
        'anos_experiencia',
        'formacion_capacitaciones',
        'cantidad_cosechas',
        'produccion_promedio',
        // Equipo de trabajo
        'num_personas_trabajando',
        'familia_trabaja_cultivo',
        'roles_principales',
        'nivel_tecnificacion',
        // Estado del predio
        'tiene_riego',
        'tiene_bodega',
        'tiene_transformacion',
        'tiene_transporte',
        'accesibilidad',
        'riesgos_naturales',
    ];

    protected $casts = [
        'cultivo_asegurado' => 'boolean',
        'familia_trabaja_cultivo' => 'boolean',
        'tiene_riego' => 'boolean',
        'tiene_bodega' => 'boolean',
        'tiene_transformacion' => 'boolean',
        'tiene_transporte' => 'boolean',
        'anos_experiencia' => 'integer',
        'cantidad_cosechas' => 'integer',
        'num_personas_trabajando' => 'integer',
    ];

    // ==================== RELACIONES ====================

    /**
     * Usuario (agricultor) al que pertenece este perfil
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ==================== SCOPES ====================

    /**
     * Filtrar por tipo de persona
     */
    public function scopeTipoPersona($query, string $tipo)
    {
        return $query->where('tipo_persona', $tipo);
    }

    /**
     * Filtrar personas naturales
     */
    public function scopeNaturales($query)
    {
        return $query->where('tipo_persona', 'natural');
    }

    /**
     * Filtrar personas jurídicas
     */
    public function scopeJuridicas($query)
    {
        return $query->where('tipo_persona', 'juridica');
    }

    /**
     * Filtrar por nivel de tecnificación
     */
    public function scopeNivelTecnificacion($query, string $nivel)
    {
        return $query->where('nivel_tecnificacion', $nivel);
    }

    // ==================== ACCESSORS ====================

    /**
     * Verificar si es persona jurídica
     */
    public function getEsPersonaJuridicaAttribute(): bool
    {
        return $this->tipo_persona === 'juridica';
    }

    /**
     * Obtener nombre a mostrar (empresa o usuario)
     */
    public function getNombreMostrarAttribute(): string
    {
        if ($this->tipo_persona === 'juridica' && $this->nombre_empresa) {
            return $this->nombre_empresa;
        }

        return $this->usuario->name ?? '';
    }
}
