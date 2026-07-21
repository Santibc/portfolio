<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;

    protected $table = 'metas';

    protected $fillable = [
        'user_id',
        'anio',
        'mes',
        'monto',
        'observaciones',
        'created_by',
    ];

    protected $casts = [
        'anio' => 'integer',
        'mes' => 'integer',
        'monto' => 'decimal:2',
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDelPeriodo($query, int $anio, int $mes)
    {
        return $query->where('anio', $anio)->where('mes', $mes);
    }
}
