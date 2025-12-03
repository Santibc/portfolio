<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    use HasFactory;

    protected $table = 'configuraciones_sistema';

    protected $fillable = [
        'clave',
        'nombre',
        'valor',
        'tipo',
        'grupo',
        'descripcion',
        'editable',
        'modificado_por',
        'modificado_at'
    ];

    protected $casts = [
        'editable' => 'boolean',
        'modificado_at' => 'datetime'
    ];

    // Relaciones
    public function modificadoPor()
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    public function scopePorGrupo($query, $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    public function scopeEditables($query)
    {
        return $query->where('editable', true);
    }

    // Métodos auxiliares
    public static function obtenerValor($clave, $default = null)
    {
        $config = self::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    public static function establecerValor($clave, $valor, $usuarioId = null)
    {
        return self::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valor,
                'modificado_por' => $usuarioId,
                'modificado_at' => now()
            ]
        );
    }
}
