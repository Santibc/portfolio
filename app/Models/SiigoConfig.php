<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SiigoConfig extends Model
{
    use HasFactory;

    protected $table = 'siigo_config';

    protected $fillable = [
        'username',
        'access_key',
        'partner_id',
        'ambiente',
        'activo',
        'nit_emisor',
        'tipo_documento_id',
        'tipo_documento_export_id',
        'seller_id',
        'payment_type_id',
        'tax_id',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'sync_catalogos_at' => 'datetime',
        'activo' => 'bool',
        'tipo_documento_id' => 'integer',
        'tipo_documento_export_id' => 'integer',
        'seller_id' => 'integer',
        'payment_type_id' => 'integer',
        'tax_id' => 'integer',
    ];

    public function setAccessKeyAttribute(?string $value): void
    {
        $this->attributes['access_key'] = $value === null || $value === ''
            ? null
            : Crypt::encryptString($value);
    }

    public function getAccessKeyAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function current(): self
    {
        $config = self::query()->first();

        if ($config === null) {
            $config = self::create(['ambiente' => 'sandbox', 'activo' => false]);
        }

        return $config;
    }
}
