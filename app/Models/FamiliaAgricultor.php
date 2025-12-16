<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamiliaAgricultor extends Model
{
    use HasFactory;

    protected $table = 'familia_agricultor';

    protected $fillable = [
        'agricultor_id',
        'parentesco',
        'nombre',
        'edad',
        'nivel_educativo',
        'estudia_actualmente',
        'trabaja_en_cultivo',
    ];

    protected $casts = [
        'edad' => 'integer',
        'trabaja_en_cultivo' => 'boolean',
    ];

    // ==================== CONSTANTES ====================

    const PARENTESCOS = [
        'esposa' => 'Esposa',
        'esposo' => 'Esposo',
        'hijo' => 'Hijo',
        'hija' => 'Hija',
        'padre' => 'Padre',
        'madre' => 'Madre',
        'hermano' => 'Hermano',
        'hermana' => 'Hermana',
        'otro' => 'Otro',
    ];

    const NIVELES_EDUCATIVOS = [
        'ninguno' => 'Ninguno',
        'primaria' => 'Primaria',
        'secundaria' => 'Secundaria',
        'tecnico' => 'Técnico',
        'profesional' => 'Profesional',
        'posgrado' => 'Posgrado',
    ];

    const ESTUDIA_OPCIONES = [
        'si' => 'Sí',
        'no' => 'No',
        'estudio_aplazado' => 'Estudio aplazado',
    ];

    // ==================== RELACIONES ====================

    /**
     * Agricultor al que pertenece este familiar
     */
    public function agricultor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agricultor_id');
    }

    // ==================== SCOPES ====================

    /**
     * Filtrar por parentesco
     */
    public function scopeParentesco($query, string $parentesco)
    {
        return $query->where('parentesco', $parentesco);
    }

    /**
     * Filtrar familiares que trabajan en el cultivo
     */
    public function scopeTrabajanEnCultivo($query)
    {
        return $query->where('trabaja_en_cultivo', true);
    }

    /**
     * Filtrar familiares que estudian actualmente
     */
    public function scopeEstudian($query)
    {
        return $query->where('estudia_actualmente', 'si');
    }

    // ==================== ACCESSORS ====================

    /**
     * Obtener etiqueta del parentesco
     */
    public function getParentescoLabelAttribute(): string
    {
        return self::PARENTESCOS[$this->parentesco] ?? $this->parentesco;
    }

    /**
     * Obtener etiqueta del nivel educativo
     */
    public function getNivelEducativoLabelAttribute(): string
    {
        return self::NIVELES_EDUCATIVOS[$this->nivel_educativo] ?? $this->nivel_educativo;
    }

    /**
     * Obtener etiqueta de si estudia
     */
    public function getEstudiaLabelAttribute(): string
    {
        return self::ESTUDIA_OPCIONES[$this->estudia_actualmente] ?? $this->estudia_actualmente;
    }
}
