<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ConfiguracionPasarela extends Model
{
    use HasFactory;

    protected $table = 'configuracion_pasarela';

    protected $fillable = [
        'pasarela',
        'public_key',
        'private_key',
        'event_key',
        'webhook_url',
        'modo_prueba',
        'configuracion_adicional',
        'activo'
    ];

    protected $casts = [
        'modo_prueba' => 'boolean',
        'activo' => 'boolean',
        'configuracion_adicional' => 'array'
    ];

    // Encriptar keys sensibles
    public function setPrivateKeyAttribute($value)
    {
        $this->attributes['private_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getPrivateKeyAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setEventKeyAttribute($value)
    {
        $this->attributes['event_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getEventKeyAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public static function obtenerConfiguracionActiva($pasarela = 'wompi')
    {
        return static::where('pasarela', $pasarela)
                    ->where('activo', true)
                    ->first();
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}