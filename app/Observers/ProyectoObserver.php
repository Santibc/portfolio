<?php

namespace App\Observers;

use App\Models\LogAuditoria;
use App\Models\Proyecto;
use Illuminate\Support\Facades\Auth;

class ProyectoObserver
{
    /**
     * Handle the Proyecto "created" event.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return void
     */
    public function created(Proyecto $proyecto)
    {
        $this->logAction($proyecto, 'created', 'Proyecto creado', [
            'codigo' => $proyecto->codigo,
            'nombre' => $proyecto->nombre,
            'categoria_id' => $proyecto->categoria_id,
            'estado' => $proyecto->estado,
        ]);
    }

    /**
     * Handle the Proyecto "updated" event.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return void
     */
    public function updated(Proyecto $proyecto)
    {
        // Detectar cambios importantes
        $changes = $proyecto->getChanges();
        $original = $proyecto->getOriginal();

        $importantChanges = [];

        // Detectar cambio de estado
        if (isset($changes['estado']) && $original['estado'] !== $changes['estado']) {
            $importantChanges['estado'] = [
                'anterior' => $original['estado'],
                'nuevo' => $changes['estado']
            ];
        }

        // Detectar aprobación
        if (isset($changes['aprobado_por']) && !$original['aprobado_por']) {
            $importantChanges['aprobado'] = true;
            $importantChanges['aprobado_por'] = $changes['aprobado_por'];
        }

        // Detectar rechazo
        if (isset($changes['motivo_rechazo']) && $changes['motivo_rechazo']) {
            $importantChanges['rechazado'] = true;
        }

        $this->logAction($proyecto, 'updated', 'Proyecto actualizado', $importantChanges);
    }

    /**
     * Handle the Proyecto "deleted" event.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return void
     */
    public function deleted(Proyecto $proyecto)
    {
        $this->logAction($proyecto, 'deleted', 'Proyecto eliminado (soft delete)', [
            'codigo' => $proyecto->codigo,
            'nombre' => $proyecto->nombre,
        ]);
    }

    /**
     * Handle the Proyecto "restored" event.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return void
     */
    public function restored(Proyecto $proyecto)
    {
        $this->logAction($proyecto, 'restored', 'Proyecto restaurado', [
            'codigo' => $proyecto->codigo,
            'nombre' => $proyecto->nombre,
        ]);
    }

    /**
     * Registrar acción en log de auditoría
     *
     * @param Proyecto $proyecto
     * @param string $action
     * @param string $description
     * @param array $data
     * @return void
     */
    private function logAction(Proyecto $proyecto, string $action, string $description, array $data = [])
    {
        try {
            LogAuditoria::create([
                'usuario_id' => Auth::id() ?? $proyecto->agricultor_id,
                'modelo' => Proyecto::class,
                'modelo_id' => $proyecto->id,
                'accion' => $action,
                'descripcion' => $description,
                'datos_anteriores' => json_encode($proyecto->getOriginal()),
                'datos_nuevos' => json_encode($data),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silenciar errores de logging para no interrumpir el flujo principal
            \Log::error('Error al registrar log de auditoría: ' . $e->getMessage());
        }
    }
}
