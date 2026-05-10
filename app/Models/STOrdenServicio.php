<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STOrdenServicio extends Model
{
    use HasFactory;

    protected $table = 'st_ordenes_servicio';

    protected $fillable = [
        'numero_orden',
        'cliente_id',
        'st_equipo_id',
        'st_tecnico_id',
        'tipo_servicio',
        'prioridad',
        'estado',
        'descripcion_problema',
        'accesorios_entregados',
        'fecha_recepcion',
        'fecha_promesa_entrega',
        'fecha_asignacion',
        'fecha_inicio_trabajo',
        'fecha_finalizacion',
        'fecha_entrega',
        'costo_mano_obra',
        'costo_repuestos',
        'costo_total',
        'cliente_notificado',
        'observaciones',
        'user_id'
    ];

    protected $casts = [
        'cliente_notificado' => 'boolean',
        'fecha_recepcion' => 'date',
        'fecha_promesa_entrega' => 'date',
        'fecha_asignacion' => 'date',
        'fecha_inicio_trabajo' => 'date',
        'fecha_finalizacion' => 'date',
        'fecha_entrega' => 'date',
        'costo_mano_obra' => 'decimal:2',
        'costo_repuestos' => 'decimal:2',
        'costo_total' => 'decimal:2'
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function equipo()
    {
        return $this->belongsTo(STEquipo::class, 'st_equipo_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(STTecnico::class, 'st_tecnico_id');
    }

    public function diagnosticos()
    {
        return $this->hasMany(STDiagnostico::class, 'st_orden_servicio_id');
    }

    public function repuestosUsados()
    {
        return $this->hasMany(STRepuestoUsado::class, 'st_orden_servicio_id');
    }

    public function historialEstados()
    {
        return $this->hasMany(STHistorialEstado::class, 'st_orden_servicio_id');
    }

    public function imagenes()
    {
        return $this->hasMany(STImagenOrden::class, 'st_orden_servicio_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeRecibidas($query)
    {
        return $query->where('estado', 'recibida');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'en_proceso');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'urgente');
    }

    public function scopePorTecnico($query, $tecnicoId)
    {
        return $query->where('st_tecnico_id', $tecnicoId);
    }

    // Accessors
    public function getDiasTranscurridosAttribute()
    {
        if ($this->fecha_entrega) {
            return $this->fecha_recepcion->diffInDays($this->fecha_entrega);
        }
        return $this->fecha_recepcion->diffInDays(now());
    }

    public function getEstaRetrasadaAttribute()
    {
        if ($this->fecha_promesa_entrega && !$this->fecha_entrega) {
            return now() > $this->fecha_promesa_entrega;
        }
        return false;
    }

    // Métodos auxiliares
    public function calcularCostoTotal()
    {
        $costoManoObra = $this->costo_mano_obra ?? 0;
        $costoRepuestos = $this->repuestosUsados()->sum('subtotal');

        $this->costo_repuestos = $costoRepuestos;
        $this->costo_total = $costoManoObra + $costoRepuestos;
        $this->save();
    }

    public function cambiarEstado($nuevoEstado, $observaciones = null)
    {
        $estadoAnterior = $this->estado;
        $this->estado = $nuevoEstado;

        // Actualizar fechas según el estado
        switch ($nuevoEstado) {
            case 'asignada':
                $this->fecha_asignacion = now();
                break;
            case 'en_proceso':
                $this->fecha_inicio_trabajo = $this->fecha_inicio_trabajo ?? now();
                break;
            case 'completada':
                $this->fecha_finalizacion = now();
                break;
            case 'entregada':
                $this->fecha_entrega = now();
                break;
        }

        $this->save();

        // Registrar en historial
        STHistorialEstado::create([
            'st_orden_servicio_id' => $this->id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $nuevoEstado,
            'observaciones' => $observaciones,
            'user_id' => auth()->id()
        ]);
    }
}
