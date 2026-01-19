<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'direccion',
        'ciudad_id',
        'telefono',
        'contacto',
        'email',
        'es_principal',
        'activo',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'activo' => 'boolean',
    ];

    // =========================================
    // Relaciones
    // =========================================
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    // =========================================
    // Scopes
    // =========================================
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePrincipales($query)
    {
        return $query->where('es_principal', true);
    }

    // =========================================
    // Métodos
    // =========================================

    /**
     * Establece esta sucursal como principal y desmarca las demás
     */
    public function marcarComoPrincipal(): void
    {
        // Desmarcar todas las sucursales del cliente
        self::where('cliente_id', $this->cliente_id)
            ->where('id', '!=', $this->id)
            ->update(['es_principal' => false]);

        // Marcar esta como principal
        $this->update(['es_principal' => true]);
    }
}
