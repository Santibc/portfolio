<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tarjeta extends Model
{
    use SoftDeletes;

    protected $table = 'tarjetas';

    protected $fillable = [
        'columna_id', 'titulo', 'descripcion', 'posicion',
        'fecha_vencimiento', 'fecha_completada', 'prioridad',
        'color_portada', 'archivada', 'creado_por',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
        'fecha_completada' => 'datetime',
        'archivada' => 'boolean',
    ];

    public function columna(): BelongsTo
    {
        return $this->belongsTo(TableroColumna::class, 'columna_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tarjeta_usuarios')->withTimestamps();
    }

    public function etiquetas(): BelongsToMany
    {
        return $this->belongsToMany(TableroEtiqueta::class, 'tarjeta_etiquetas', 'tarjeta_id', 'etiqueta_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(TarjetaChecklist::class)->orderBy('posicion');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(TarjetaComentario::class)->orderByDesc('created_at');
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(TarjetaAdjunto::class);
    }

    public function getEstadoVencimientoAttribute(): ?string
    {
        if (!$this->fecha_vencimiento) return null;
        if ($this->fecha_completada) return 'completada';

        $ahora = Carbon::now();
        $vence = $this->fecha_vencimiento;

        if ($vence->isPast()) return 'vencida';
        if ($vence->diffInHours($ahora) <= 24) return 'urgente';
        if ($vence->diffInDays($ahora) <= 3) return 'pronto';

        return 'ok';
    }

    public function getProgresoChecklistAttribute(): array
    {
        $total = 0;
        $completados = 0;
        foreach ($this->checklists as $checklist) {
            $total += $checklist->items->count();
            $completados += $checklist->items->where('completado', true)->count();
        }
        return ['total' => $total, 'completados' => $completados];
    }

    public function scopeActivas($query)
    {
        return $query->where('archivada', false);
    }
}
