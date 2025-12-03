<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoProyecto extends Model
{
    use HasFactory;

    protected $table = 'documentos_proyecto';

    protected $fillable = [
        'proyecto_id',
        'tipo_documento',
        'nombre_archivo',
        'ruta_archivo',
        'tipo_mime',
        'tamano_bytes',
        'descripcion',
        'verificado',
        'verificado_por',
        'verificado_at',
        'subido_por'
    ];

    protected $casts = [
        'tamano_bytes' => 'integer',
        'verificado' => 'boolean',
        'verificado_at' => 'datetime'
    ];

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function verificador()
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }
}
