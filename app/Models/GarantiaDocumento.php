<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarantiaDocumento extends Model
{
    use HasFactory;

    protected $table = 'garantia_documentos';

    protected $fillable = [
        'garantia_id',
        'nombre_original',
        'nombre_archivo',
        'ruta_relativa',
        'mime_type',
        'tamano',
    ];

    protected $casts = [
        'tamano' => 'integer',
    ];

    public function garantia()
    {
        return $this->belongsTo(Garantia::class, 'garantia_id');
    }

    public function urlPublica(): string
    {
        return asset($this->ruta_relativa);
    }
}
