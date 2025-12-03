<?php

namespace App\Services\Project;

use App\Enums\ProjectStatus;
use App\Models\Proyecto;
use App\Models\User;
use App\Notifications\ProyectoAprobadoNotification;
use App\Notifications\ProyectoEnRevisionNotification;
use App\Notifications\ProyectoRechazadoNotification;
use App\Repositories\ProyectoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ProjectService
{
    public function __construct(
        private ProyectoRepository $proyectoRepository,
        private ProjectCodeGeneratorService $codeGenerator
    ) {}

    /**
     * Crear un nuevo proyecto
     *
     * @param array $data
     * @param User $agricultor
     * @return Proyecto
     * @throws \Exception
     */
    public function createProject(array $data, User $agricultor): Proyecto
    {
        try {
            DB::beginTransaction();

            // Generar código único
            $categoria = \App\Models\CategoriaProyecto::findOrFail($data['categoria_id']);
            $data['codigo'] = $this->codeGenerator->generateUniqueCode($categoria);

            // Asignar agricultor y estado inicial
            $data['agricultor_id'] = $agricultor->id;
            $data['estado'] = ProjectStatus::BORRADOR->value;
            $data['activo'] = true;
            $data['verificado'] = false;
            $data['destacado'] = false;
            $data['orden_destacado'] = 0;

            $proyecto = Proyecto::create($data);

            DB::commit();

            return $proyecto;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar un proyecto
     *
     * @param Proyecto $proyecto
     * @param array $data
     * @return Proyecto
     * @throws \Exception
     */
    public function updateProject(Proyecto $proyecto, array $data): Proyecto
    {
        // Verificar que se puede editar
        if (!$this->canEdit($proyecto)) {
            throw new \Exception('Este proyecto no puede ser editado en su estado actual.');
        }

        try {
            DB::beginTransaction();

            // Si el proyecto estaba rechazado y se edita, volver a borrador
            if ($proyecto->estado === ProjectStatus::RECHAZADO->value) {
                $data['estado'] = ProjectStatus::BORRADOR->value;
                $data['motivo_rechazo'] = null;
            }

            $proyecto->update($data);

            DB::commit();

            return $proyecto->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Enviar proyecto a revisión
     *
     * @param Proyecto $proyecto
     * @return void
     * @throws \Exception
     */
    public function submitForReview(Proyecto $proyecto): void
    {
        if ($proyecto->estado !== ProjectStatus::BORRADOR->value) {
            throw new \Exception('Solo se pueden enviar a revisión proyectos en estado borrador.');
        }

        try {
            DB::beginTransaction();

            $proyecto->update([
                'estado' => ProjectStatus::EN_REVISION->value
            ]);

            // Notificar a todos los administradores
            $admins = User::role('Administrador')->get();
            Notification::send($admins, new ProyectoEnRevisionNotification($proyecto));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Aprobar proyecto
     *
     * @param Proyecto $proyecto
     * @param User $admin
     * @param string|null $notas
     * @return void
     * @throws \Exception
     */
    public function approveProject(Proyecto $proyecto, User $admin, ?string $notas = null): void
    {
        if ($proyecto->estado !== ProjectStatus::EN_REVISION->value) {
            throw new \Exception('Solo se pueden aprobar proyectos en estado de revisión.');
        }

        try {
            DB::beginTransaction();

            // Actualizar proyecto como aprobado
            $proyecto->update([
                'estado' => ProjectStatus::EN_RECAUDACION->value, // Pasa directamente a recaudación
                'aprobado_por' => $admin->id,
                'aprobado_at' => now(),
                'notas_aprobacion' => $notas,
                'verificado' => true,
                'motivo_rechazo' => null
            ]);

            // Notificar al agricultor
            $proyecto->agricultor->notify(new ProyectoAprobadoNotification($proyecto));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Rechazar proyecto
     *
     * @param Proyecto $proyecto
     * @param User $admin
     * @param string $motivo
     * @return void
     * @throws \Exception
     */
    public function rejectProject(Proyecto $proyecto, User $admin, string $motivo): void
    {
        if ($proyecto->estado !== ProjectStatus::EN_REVISION->value) {
            throw new \Exception('Solo se pueden rechazar proyectos en estado de revisión.');
        }

        if (empty($motivo)) {
            throw new \Exception('El motivo de rechazo es obligatorio.');
        }

        try {
            DB::beginTransaction();

            $proyecto->update([
                'estado' => ProjectStatus::RECHAZADO->value,
                'motivo_rechazo' => $motivo,
                'verificado' => false
            ]);

            // Notificar al agricultor
            $proyecto->agricultor->notify(new ProyectoRechazadoNotification($proyecto, $motivo));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Verificar si un proyecto puede ser editado
     *
     * @param Proyecto $proyecto
     * @return bool
     */
    public function canEdit(Proyecto $proyecto): bool
    {
        return in_array($proyecto->estado, [
            ProjectStatus::BORRADOR->value,
            ProjectStatus::RECHAZADO->value
        ]);
    }

    /**
     * Verificar si un proyecto puede ser enviado a revisión
     *
     * @param Proyecto $proyecto
     * @return bool
     */
    public function canSubmitForReview(Proyecto $proyecto): bool
    {
        return $proyecto->estado === ProjectStatus::BORRADOR->value;
    }

    /**
     * Obtener proyectos del agricultor
     *
     * @param User $agricultor
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProyectosByAgricultor(User $agricultor)
    {
        return $this->proyectoRepository->findByAgricultor($agricultor);
    }

    /**
     * Obtener proyectos pendientes de revisión
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingReviewProjects()
    {
        return $this->proyectoRepository->findPendingReview();
    }

    /**
     * Verificar que el agricultor sea dueño del proyecto
     *
     * @param Proyecto $proyecto
     * @param User $agricultor
     * @return bool
     */
    public function isOwner(Proyecto $proyecto, User $agricultor): bool
    {
        return $proyecto->agricultor_id === $agricultor->id;
    }
}
