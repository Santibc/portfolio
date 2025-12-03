<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\CategoriaProyecto;
use App\Models\Proyecto;
use App\Services\Project\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $proyectos = $this->projectService->getProyectosByAgricultor($request->user());

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
                ->route('farmer.projects.show', $proyecto->id)
                ->with('success', 'Proyecto creado exitosamente.');

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

        $project->load(['categoria', 'aprobador', 'imagenes', 'documentos', 'actualizaciones']);

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

        try {
            $this->projectService->submitForReview($project);

            return redirect()
                ->route('farmer.projects.show', $project->id)
                ->with('success', 'Proyecto enviado a revisión exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
