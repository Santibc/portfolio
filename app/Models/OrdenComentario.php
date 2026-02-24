<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenComentario extends Model
{
    protected $table = 'orden_comentarios';

    const UPDATED_AT = null;

    protected $fillable = ['orden_id', 'usuario_id', 'contenido'];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function update(array $attributes = [], array $options = [])
    {
        throw new \RuntimeException('Los comentarios de orden no pueden ser modificados.');
    }

    public function delete()
    {
        throw new \RuntimeException('Los comentarios de orden no pueden ser eliminados.');
    }
}
