<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroActividad extends Model
{
    protected $table = 'registro_actividades';

    const UPDATED_AT = null;

    protected $fillable = ['usuario_id', 'orden_id', 'accion', 'descripcion', 'datos_extra'];

    protected $casts = [
        'datos_extra' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function update(array $attributes = [], array $options = [])
    {
        throw new \RuntimeException('Los registros de actividad no pueden ser modificados.');
    }

    public function delete()
    {
        throw new \RuntimeException('Los registros de actividad no pueden ser eliminados.');
    }
}
