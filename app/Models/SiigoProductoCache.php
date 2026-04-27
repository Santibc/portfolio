<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiigoProductoCache extends Model
{
    protected $table = 'siigo_productos_cache';

    protected $fillable = [
        'siigo_id',
        'code',
        'name',
        'reference',
        'account_group_name',
        'type',
        'active',
        'last_sync_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];
}
