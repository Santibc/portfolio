<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoKyc extends Model
{
    use HasFactory;

    protected $table = 'documentos_kyc';

    protected $fillable = [
        'usuario_id',
        'tipo_documento',
        'nombre_archivo',
        'ruta_archivo',
        'mime_type',
        'tamanio_kb',
        'fecha_subida',
        'estado',
        'revisado_por',
        'revisado_at',
        'observaciones'
    ];

    protected $casts = [
        'tamanio_kb' => 'integer',
        'fecha_subida' => 'date',
        'revisado_at' => 'datetime'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Alias para compatibilidad
    public function user()
    {
        return $this->usuario();
    }

    public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente_revision');
    }

    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeRechazados($query)
    {
        return $query->where('estado', 'rechazado');
    }
}
