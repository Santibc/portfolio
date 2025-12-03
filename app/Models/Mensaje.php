<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mensajes';

    protected $fillable = [
        'remitente_id',
        'destinatario_id',
        'asunto',
        'contenido',
        'leido',
        'leido_at',
        'archivado_remitente',
        'archivado_destinatario',
        'mensaje_padre_id'
    ];

    protected $casts = [
        'leido' => 'boolean',
        'leido_at' => 'datetime',
        'archivado_remitente' => 'boolean',
        'archivado_destinatario' => 'boolean'
    ];

    // Relaciones
    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function mensajePadre()
    {
        return $this->belongsTo(Mensaje::class, 'mensaje_padre_id');
    }

    public function respuestas()
    {
        return $this->hasMany(Mensaje::class, 'mensaje_padre_id');
    }

    public function scopeNoLeidos($query)
    {
        return $query->where('leido', false);
    }

    public function scopeEnviados($query, $usuarioId)
    {
        return $query->where('remitente_id', $usuarioId)
                     ->where('archivado_remitente', false);
    }

    public function scopeRecibidos($query, $usuarioId)
    {
        return $query->where('destinatario_id', $usuarioId)
                     ->where('archivado_destinatario', false);
    }
}
