<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoEmpresa extends Model
{
    use HasFactory;

    protected $table = 'documentos_empresa';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'archivo_path',
        'archivo_nombre_original',
        'archivo_extension',
        'archivo_tamaño',
        'fecha_documento',
        'fecha_caducidad',
        'visible_solo_admin',
        'notas',
        'subido_por',
    ];

    protected $casts = [
        'fecha_documento' => 'date',
        'fecha_caducidad' => 'date',
        'visible_solo_admin' => 'boolean',
        'archivo_tamaño' => 'integer',
    ];

    /**
     * Categorías de documentos disponibles
     */
    const CATEGORIAS = [
        'legal' => 'Legal',
        'fiscal' => 'Fiscal',
        'laboral' => 'Laboral',
        'certificaciones' => 'Certificaciones',
        'seguros' => 'Seguros',
        'contratos' => 'Contratos',
        'procedimientos' => 'Procedimientos',
        'otro' => 'Otro',
    ];

    /**
     * Iconos para cada categoría
     */
    const CATEGORIA_ICONOS = [
        'legal' => 'bi bi-scale',
        'fiscal' => 'bi bi-calculator',
        'laboral' => 'bi bi-people',
        'certificaciones' => 'bi bi-award',
        'seguros' => 'bi bi-shield-check',
        'contratos' => 'bi bi-file-earmark-text',
        'procedimientos' => 'bi bi-list-check',
        'otro' => 'bi bi-folder',
    ];

    /**
     * Colores para cada categoría
     */
    const CATEGORIA_COLORES = [
        'legal' => 'primary',
        'fiscal' => 'info',
        'laboral' => 'success',
        'certificaciones' => 'warning',
        'seguros' => 'danger',
        'contratos' => 'secondary',
        'procedimientos' => 'dark',
        'otro' => 'light',
    ];

    /**
     * Usuario que subió el documento
     */
    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    /**
     * Scope: Documentos por categoría
     */
    public function scopeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope: Documentos próximos a caducar
     */
    public function scopeProximosACaducar($query, int $dias = 30)
    {
        return $query->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<=', now()->addDays($dias))
            ->where('fecha_caducidad', '>=', now());
    }

    /**
     * Scope: Documentos caducados
     */
    public function scopeCaducados($query)
    {
        return $query->whereNotNull('fecha_caducidad')
            ->where('fecha_caducidad', '<', now());
    }

    /**
     * Scope: Documentos vigentes
     */
    public function scopeVigentes($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('fecha_caducidad')
                ->orWhere('fecha_caducidad', '>=', now());
        });
    }

    /**
     * Accessor: Nombre de la categoría
     */
    public function getCategoriaNombreAttribute(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? 'Otro';
    }

    /**
     * Accessor: Icono de la categoría
     */
    public function getCategoriaIconoAttribute(): string
    {
        return self::CATEGORIA_ICONOS[$this->categoria] ?? 'bi bi-folder';
    }

    /**
     * Accessor: Color de la categoría
     */
    public function getCategoriaColorAttribute(): string
    {
        return self::CATEGORIA_COLORES[$this->categoria] ?? 'secondary';
    }

    /**
     * Accessor: Tamaño del archivo formateado
     */
    public function getArchivoTamañoFormateadoAttribute(): string
    {
        $bytes = $this->archivo_tamaño;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2, ',', '.') . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Accessor: Estado de caducidad
     */
    public function getEstadoCaducidadAttribute(): string
    {
        if (!$this->fecha_caducidad) {
            return 'sin_caducidad';
        }

        if ($this->fecha_caducidad->isPast()) {
            return 'caducado';
        }

        if ($this->fecha_caducidad->diffInDays(now()) <= 30) {
            return 'proximo';
        }

        return 'vigente';
    }

    /**
     * Accessor: Badge HTML para estado de caducidad
     */
    public function getBadgeCaducidadAttribute(): string
    {
        switch ($this->estado_caducidad) {
            case 'caducado':
                return '<span class="badge bg-danger">Caducado</span>';
            case 'proximo':
                return '<span class="badge bg-warning text-dark">Próximo a caducar</span>';
            case 'vigente':
                return '<span class="badge bg-success">Vigente</span>';
            default:
                return '<span class="badge bg-secondary">Sin caducidad</span>';
        }
    }

    /**
     * Verificar si el archivo existe
     */
    public function archivoExiste(): bool
    {
        return $this->archivo_path && file_exists(public_path($this->archivo_path));
    }

    /**
     * Obtener URL pública del archivo
     */
    public function getArchivoUrlAttribute(): ?string
    {
        if ($this->archivo_path) {
            return asset($this->archivo_path);
        }
        return null;
    }
}
