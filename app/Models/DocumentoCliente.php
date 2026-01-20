<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentoCliente extends Model
{
    use HasFactory;

    protected $table = 'documentos_cliente';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'archivo',
        'tipo',
        'mime_type',
        'tamano',
        'subido_por',
    ];

    // =========================================
    // Constantes de tipos de documento
    // =========================================
    const TIPO_RUT = 'rut';
    const TIPO_CAMARA_COMERCIO = 'camara_comercio';
    const TIPO_CEDULA = 'cedula';
    const TIPO_OTRO = 'otro';

    public static function tiposDocumento(): array
    {
        return [
            self::TIPO_RUT => 'RUT',
            self::TIPO_CAMARA_COMERCIO => 'Cámara de Comercio',
            self::TIPO_CEDULA => 'Cédula/Identificación',
            self::TIPO_OTRO => 'Otro',
        ];
    }

    // =========================================
    // Relaciones
    // =========================================
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    // =========================================
    // Accessors
    // =========================================
    public function getUrlAttribute(): string
    {
        return asset($this->archivo);
    }

    public function getTamanoFormateadoAttribute(): string
    {
        $bytes = $this->tamano;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function getTipoNombreAttribute(): string
    {
        return self::tiposDocumento()[$this->tipo] ?? $this->tipo ?? 'Sin tipo';
    }

    public function getIconoAttribute(): string
    {
        $extension = strtolower(pathinfo($this->archivo, PATHINFO_EXTENSION));

        $iconos = [
            'pdf' => 'bi-file-earmark-pdf text-danger',
            'doc' => 'bi-file-earmark-word text-primary',
            'docx' => 'bi-file-earmark-word text-primary',
            'xls' => 'bi-file-earmark-excel text-success',
            'xlsx' => 'bi-file-earmark-excel text-success',
            'ppt' => 'bi-file-earmark-ppt text-warning',
            'pptx' => 'bi-file-earmark-ppt text-warning',
            'jpg' => 'bi-file-earmark-image text-info',
            'jpeg' => 'bi-file-earmark-image text-info',
            'png' => 'bi-file-earmark-image text-info',
            'gif' => 'bi-file-earmark-image text-info',
            'zip' => 'bi-file-earmark-zip text-secondary',
            'rar' => 'bi-file-earmark-zip text-secondary',
            'txt' => 'bi-file-earmark-text text-dark',
        ];

        return $iconos[$extension] ?? 'bi-file-earmark text-secondary';
    }

    public function getNombreArchivoAttribute(): string
    {
        return basename($this->archivo);
    }

    // =========================================
    // Métodos
    // =========================================
    public function eliminarArchivo(): bool
    {
        $rutaCompleta = public_path($this->archivo);
        if ($this->archivo && file_exists($rutaCompleta)) {
            return unlink($rutaCompleta);
        }
        return false;
    }
}
