<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveProjectRequest;
use App\Http\Requests\RejectProjectRequest;
use App\Models\Proyecto;
use App\Services\Project\ProjectService;
use Illuminate\Http\Request;

class ProjectApprovalController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {}

    /**
     * Display a listing of projects pending review.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proyectos = $this->projectService->getPendingReviewProjects();

        // Estadísticas del día
        $aprobadosHoy = Proyecto::where('estado', 'aprobado')
            ->whereDate('aprobado_at', today())
            ->count();

        $rechazadosHoy = Proyecto::where('estado', 'rechazado')
            ->whereDate('updated_at', today())
            ->whereNotNull('motivo_rechazo')
            ->count();

        return view('admin.projects.review', compact('proyectos', 'aprobadosHoy', 'rechazadosHoy'));
    }

    /**
     * Display the specified project for review.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function show(Proyecto $proyecto)
    {
        $proyecto->load([
            'categoria',
            'agricultor.perfilAgricultor',
            'agricultor.familia',
            'imagenes',
            'documentos',
            'actualizaciones',
            'creadoPorAdmin',
        ]);

        return view('admin.projects.show', compact('proyecto'));
    }

    /**
     * Approve the specified project.
     *
     * @param  \App\Http\Requests\ApproveProjectRequest  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function approve(ApproveProjectRequest $request, Proyecto $proyecto)
    {
        try {
            $this->projectService->approveProject(
                $proyecto,
                $request->user(),
                $request->input('notas_aprobacion')
            );

            return redirect()
                ->route('admin.projects.review.index')
                ->with('success', "Proyecto '{$proyecto->nombre}' aprobado exitosamente.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al aprobar el proyecto: ' . $e->getMessage());
        }
    }

    /**
     * Reject the specified project.
     *
     * @param  \App\Http\Requests\RejectProjectRequest  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function reject(RejectProjectRequest $request, Proyecto $proyecto)
    {
        try {
            $this->projectService->rejectProject(
                $proyecto,
                $request->user(),
                $request->input('motivo_rechazo')
            );

            return redirect()
                ->route('admin.projects.review.index')
                ->with('success', "Proyecto '{$proyecto->nombre}' rechazado.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al rechazar el proyecto: ' . $e->getMessage());
        }
    }
}
