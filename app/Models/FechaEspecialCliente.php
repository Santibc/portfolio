<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FechaEspecialCliente extends Model
{
    use HasFactory;

    protected $table = 'fechas_especiales_cliente';

    protected $fillable = [
        'user_id',
        'nombre',
        'tipo',
        'fecha',
        'notas',
        'recordar_dias_antes',
        'dias_anticipacion',
        'activo',
        'ultimo_recordatorio',
    ];

    protected $casts = [
        'fecha' => 'date',
        'recordar_dias_antes' => 'boolean',
        'activo' => 'boolean',
        'ultimo_recordatorio' => 'datetime',
    ];

    const TIPOS = [
        'cumpleanos' => 'Cumpleaños',
        'aniversario' => 'Aniversario',
        'dia_madre' => 'Día de la Madre',
        'dia_padre' => 'Día del Padre',
        'navidad' => 'Navidad',
        'otro' => 'Otro',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTipoNombreAttribute()
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    // Obtener próxima ocurrencia de la fecha (para cumpleaños anuales)
    public function getProximaFechaAttribute()
    {
        $hoy = now()->startOfDay();
        $fecha = $this->fecha->copy()->year($hoy->year);

        if ($fecha->lt($hoy)) {
            $fecha->addYear();
        }

        return $fecha;
    }

    // Días restantes hasta la fecha
    public function getDiasRestantesAttribute()
    {
        return now()->startOfDay()->diffInDays($this->proxima_fecha, false);
    }

    // Verificar si debe enviar recordatorio
    public function debeEnviarRecordatorio()
    {
        if (!$this->recordar_dias_antes || !$this->activo) {
            return false;
        }

        return $this->dias_restantes == $this->dias_anticipacion;
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeProximas($query, $dias = 30)
    {
        $hoy = now()->startOfDay();
        return $query->activas()
            ->whereRaw("DATE_FORMAT(fecha, '%m-%d') BETWEEN DATE_FORMAT(?, '%m-%d') AND DATE_FORMAT(?, '%m-%d')",
                [$hoy, $hoy->copy()->addDays($dias)]);
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
