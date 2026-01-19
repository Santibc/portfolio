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
        return Storage::url($this->archivo);
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

    // =========================================
    // Métodos
    // =========================================
    public function eliminarArchivo(): bool
    {
        if ($this->archivo && Storage::exists($this->archivo)) {
            return Storage::delete($this->archivo);
        }
        return false;
    }
}
