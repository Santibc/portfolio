<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\StoreProjectPhase1Request;
use App\Http\Requests\StoreProjectPhase2Request;
use App\Http\Requests\StoreProjectPhase3Request;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\CategoriaProyecto;
use App\Models\Proyecto;
use App\Services\Project\ProjectService;
use App\Services\Project\ProjectFormService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private ProjectFormService $formService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $proyectos = $this->projectService->getProyectosByAgricultor($request->user());

        // Agregar fase actual a cada proyecto
        foreach ($proyectos as $proyecto) {
            $proyecto->current_phase = $this->formService->getCurrentPhase($proyecto);
            $proyecto->is_complete = $this->formService->isComplete($proyecto);
        }

        return view('farmer.projects.index', compact('proyectos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorias = CategoriaProyecto::activos()->porOrden()->get();

        return view('farmer.projects.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectRequest $request)
    {
        try {
            $proyecto = $this->projectService->createProject(
                $request->validated(),
                $request->user()
            );

            return redirect()
                ->route('farmer.projects.files', $proyecto->id)
                ->with('success', 'Proyecto creado exitosamente. Ahora puedes subir imágenes y documentos.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear el proyecto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proyecto  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Proyecto $project)
    {
        // Verificar que el usuario sea el dueño
        if (!$this->projectService->isOwner($project, auth()->user())) {
            abort(403, 'No tienes permiso para ver este proyecto.');
        }

        $project->load([
            'categoria',
            'aprobador',
            'imagenes',
            'documentos',
            'actualizaciones',
            'agricultor.perfilAgricultor',
            'agricultor.familia',
        ]);

        return view('farmer.projects.show', ['proyecto' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proyecto  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Proyecto $project)
    {
        // Verificar que el usuario sea el dueño
        if (!$this->projectService->isOwner($project, auth()->user())) {
            abort(403, 'No tienes permiso para editar este proyecto.');
        }

        // Verificar que se pueda editar
        if (!$this->projectService->canEdit($project)) {
            return redirect()
                ->route('farmer.projects.show', $project->id)
                ->with('error', 'Este proyecto no puede ser editado en su estado actual.');
        }

        $categorias = CategoriaProyecto::activos()->porOrden()->get();

        return view('farmer.projects.edit', ['proyecto' => $project, 'categorias' => $categorias]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectRequest  $request
     * @param  \App\Models\Proyecto  $project
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request, Proyecto $project)
    {
        // Verificar que el usuario sea el dueño
        if (!$this->projectService->isOwner($project, $request->user())) {
            abort(403, 'No tienes permiso para actualizar este proyecto.');
        }

        try {
            $this->projectService->updateProject($project, $request->validated());

            return redirect()
                ->route('farmer.projects.show', $project->id)
                ->with('success', 'Proyecto actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el proyecto: ' . $e->getMessage());
        }
    }

    /**
     * Show the files management page for a project.
     *
     * @param  \App\Models\Proyecto  $project
     * @return \Illuminate\Http\Response
     */
    public function files(Proyecto $project)
    {
        // Verificar que el usuario sea el dueño
        if (!$this->projectService->isOwner($project, auth()->user())) {
            abort(403, 'No tienes permiso para gestionar los archivos de este proyecto.');
        }

        // Verificar que se pueda editar
        if (!$this->projectService->canEdit($project)) {
            return redirect()
                ->route('farmer.projects.show', $project->id)
                ->with('error', 'Este proyecto no puede ser modificado en su estado actual.');
        }

        $project->load(['categoria', 'imagenes', 'documentos']);

        return view('farmer.projects.files', ['proyecto' => $project]);
    }

    /**
     * Submit project for review
     *
     * @param  \App\Models\Proyecto  $project
     * @return \Illuminate\Http\Response
     */
    public function submitForReview(Proyecto $project)
    {
        // Verificar que el usuario sea el dueño
        if (!$this->projectService->isOwner($project, auth()->user())) {
            abort(403, 'No tienes permiso para enviar este proyecto a revisión.');
        }

        // Verificar que el proyecto esté completo
        if (!$this->formService->isComplete($project)) {
            return back()->with('error', 'Debes completar las 3 fases del proyecto antes de enviarlo a revisión.');
        }

        try {
            $this->projectService->submitForReview($project);

            return redirect()
                ->route('farmer.projects.show', $project->id)
                ->with('success', 'Proyecto enviado a revisión exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ==================== MÉTODOS FASE 2 ====================

    /**
     * Show Phase 2 form (technical evaluation).
     *
     * @param  \App\Models\Proyecto  $project
     * @return \Illuminate\Http\Response
     */
    public function showPhase2(Proyecto $project)
    {
        // Verificar que el usuario sea el dueño
        if (!$this->projectService->isOwner($project, auth()->user())) {
            abort(403, 'No tienes permiso para editar este proyecto.');
        }

        // Verificar que se pueda editar
        if (!$this->projectService->canEdit($project)) {
            return redirect()
                ->route('farmer.projects.show', $project->id)
                ->with('error', 'Este proyecto no puede ser editado en su estado actual.');
        }

        $project->load(['categoria', 'agricultor.perfilAgricultor', 'agricultor.familia']);
        $perfil = auth()->user()->perfilAgricultor;
        $familia = auth()->user()->familia;

        return view('farmer.projects.phase2', [
            'proyecto' => $project,
            'perfil' => $perfil,
            'familia' => $familia
        ]);
    }

    /**
     * Store Phase 2 data (technical evaluation).
     *
     * @param  \App\Http\Requests\StoreProjectPhase2Request  $request
     * @param  \App\Models\Proyecto  $project
     * @return \Illuminate\Http\Response
     */
    public function storePhase2(StoreProjectPhase2Request $request, Proyecto $project)
    {
        // Verificar que el usuario sea el dueño
        if (!$this->projectService->isOwner($project, $request->user())) {
            abort(403, 'No tienes permiso para editar este proyecto.');
        }

        // Verificar que se pueda editar
        if (!$this->projectService->canEdit($project)) {
            return back()->with('error', 'Este proyecto no puede ser editado en su estado actual.');
        }

        try {
            $this->formService->savePhase2($project, $request->validated());

            return redirect()
                ->route('farmer.projects.phase3', $project)
                ->with('success', 'Fase 2 guardada correctamente. Continúe con la evaluación financiera.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    // ==================== MÉTODOS FASE 3 ====================

    /**
     * Show Phase 3 form (financial evaluation).
     *
     * @param  \App\Models\Proyecto  $project
     * @return \Illuminate\Http\Response
     */
    public function showPhase3(Proyecto $project)
    {
        // Verificar que el usuario sea el dueño
        if (!$this->projectService->isOwner($project, auth()->user())) {
            abort(403, 'No tienes permiso para editar este proyecto.');
        }

        // Verificar que se pueda editar
        if (!$this->projectService->canEdit($project)) {
            return redirect()
                ->route('farmer.projects.show', $project->id)
                ->with('error', 'Este proyecto no puede ser editado en su estado actual.');
        }

        $project->load(['categoria']);

        return view('farmer.projects.phase3', ['proyecto' => $project]);
    }

    /**
     * Store Phase 3 data (financial evaluation).
     *
     * @param  \App\Http\Requests\StoreProjectPhase3Request  $request
     * @param  \App\Models\Proyecto  $project
     * @return \Illuminate\Http\Response
     */
    public function storePhase3(StoreProjectPhase3Request $request, Proyecto $project)
    {
        // Verificar que el usuario sea el dueño
        if (!$this->projectService->isOwner($project, $request->user())) {
            abort(403, 'No tienes permiso para editar este proyecto.');
        }

        // Verificar que se pueda editar
        if (!$this->projectService->canEdit($project)) {
            return back()->with('error', 'Este proyecto no puede ser editado en su estado actual.');
        }

        try {
            $this->formService->savePhase3($project, $request->validated());

            return redirect()
                ->route('farmer.projects.files', $project)
                ->with('success', 'Fase 3 guardada correctamente. Ahora puedes subir documentos e imágenes.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
}
