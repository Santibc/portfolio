<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectPhase1Request;
use App\Http\Requests\StoreProjectPhase2Request;
use App\Http\Requests\StoreProjectPhase3Request;
use App\Http\Requests\UploadProjectDocumentRequest;
use App\Models\CategoriaProyecto;
use App\Models\DocumentoProyecto;
use App\Models\ImagenProyecto;
use App\Models\Proyecto;
use App\Models\User;
use App\Services\Farmer\FarmerCreationService;
use App\Services\Project\ProjectFormService;
use App\Services\Project\ProjectService;
use App\Services\Storage\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProjectRegistrationController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private ProjectFormService $formService,
        private FarmerCreationService $farmerService,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Display a listing of projects registered by admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proyectos = Proyecto::creadosPorAdmin()
            ->with(['categoria', 'agricultor', 'creadoPorAdmin'])
            ->orderByDesc('created_at')
            ->paginate(15);

        // Estadísticas
        $totalRegistrados = Proyecto::creadosPorAdmin()->count();
        $enBorrador = Proyecto::creadosPorAdmin()->where('estado', 'borrador')->count();
        $enRevision = Proyecto::creadosPorAdmin()->where('estado', 'en_revision')->count();
        $aprobados = Proyecto::creadosPorAdmin()->where('estado', 'en_recaudacion')->count();

        return view('admin.projects.registration.index', compact(
            'proyectos',
            'totalRegistrados',
            'enBorrador',
            'enRevision',
            'aprobados'
        ));
    }

    /**
     * Show the form for creating a new project (Phase 1).
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorias = CategoriaProyecto::activos()->porOrden()->get();
        $agricultoresExistentes = User::role('Agricultor')
            ->activos()
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'documento_identidad']);

        return view('admin.projects.registration.create', compact(
            'categorias',
            'agricultoresExistentes'
        ));
    }

    /**
     * Store Phase 1 data (farmer + basic project info).
     *
     * @param  \App\Http\Requests\StoreProjectPhase1Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePhase1(StoreProjectPhase1Request $request)
    {
        try {
            $proyecto = $this->formService->savePhase1(
                $request->validated(),
                $request->user(),
                true // isAdmin = true
            );

            return redirect()
                ->route('admin.projects.registration.phase2', $proyecto)
                ->with('success', 'Fase 1 guardada correctamente. Continúe con la evaluación técnica.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Show Phase 2 form (technical evaluation).
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function showPhase2(Proyecto $proyecto)
    {
        $this->authorizeProject($proyecto);

        $proyecto->load(['categoria', 'agricultor.perfilAgricultor', 'agricultor.familia']);
        $perfil = $proyecto->agricultor->perfilAgricultor;
        $familia = $proyecto->agricultor->familia;

        return view('admin.projects.registration.phase2', compact(
            'proyecto',
            'perfil',
            'familia'
        ));
    }

    /**
     * Store Phase 2 data (technical evaluation).
     *
     * @param  \App\Http\Requests\StoreProjectPhase2Request  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function storePhase2(StoreProjectPhase2Request $request, Proyecto $proyecto)
    {
        $this->authorizeProject($proyecto);

        try {
            $this->formService->savePhase2($proyecto, $request->validated());

            return redirect()
                ->route('admin.projects.registration.phase3', $proyecto)
                ->with('success', 'Fase 2 guardada correctamente. Continúe con la evaluación financiera.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Show Phase 3 form (financial evaluation).
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function showPhase3(Proyecto $proyecto)
    {
        $this->authorizeProject($proyecto);

        $proyecto->load(['categoria', 'agricultor']);

        return view('admin.projects.registration.phase3', compact('proyecto'));
    }

    /**
     * Store Phase 3 data (financial evaluation).
     *
     * @param  \App\Http\Requests\StoreProjectPhase3Request  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function storePhase3(StoreProjectPhase3Request $request, Proyecto $proyecto)
    {
        $this->authorizeProject($proyecto);

        try {
            $this->formService->savePhase3($proyecto, $request->validated());

            return redirect()
                ->route('admin.projects.registration.show', $proyecto)
                ->with('success', 'Proyecto registrado completamente. Revise los datos y envíe a revisión si todo está correcto.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified project.
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
            'creadoPorAdmin'
        ]);

        $currentPhase = $this->formService->getCurrentPhase($proyecto);
        $isComplete = $this->formService->isComplete($proyecto);

        return view('admin.projects.registration.show', compact(
            'proyecto',
            'currentPhase',
            'isComplete'
        ));
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function edit(Proyecto $proyecto)
    {
        $this->authorizeProject($proyecto);

        $proyecto->load([
            'categoria',
            'agricultor.perfilAgricultor',
            'agricultor.familia'
        ]);

        $categorias = CategoriaProyecto::activos()->porOrden()->get();
        $currentPhase = $this->formService->getCurrentPhase($proyecto);

        return view('admin.projects.registration.edit', compact(
            'proyecto',
            'categorias',
            'currentPhase'
        ));
    }

    /**
     * Update the specified project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proyecto $proyecto)
    {
        $this->authorizeProject($proyecto);

        try {
            $proyecto = $this->projectService->updateProject($proyecto, $request->all());

            return redirect()
                ->route('admin.projects.registration.show', $proyecto)
                ->with('success', 'Proyecto actualizado correctamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Submit project for review.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function submitForReview(Proyecto $proyecto)
    {
        $this->authorizeProject($proyecto);

        // Verificar que el proyecto está completo
        if (!$this->formService->isComplete($proyecto)) {
            return back()->with('error', 'El proyecto debe completar las 3 fases antes de enviar a revisión.');
        }

        try {
            $this->projectService->submitForReview($proyecto);

            return redirect()
                ->route('admin.projects.registration.index')
                ->with('success', "Proyecto '{$proyecto->nombre}' enviado a revisión.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar a revisión: ' . $e->getMessage());
        }
    }

    /**
     * Send welcome email to farmer.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function resendWelcomeEmail(Proyecto $proyecto)
    {
        try {
            $farmer = $proyecto->agricultor;

            // Regenerar contraseña temporal y enviar email
            $temporaryPassword = $this->farmerService->generateTemporaryPassword([
                'documento_identidad' => $farmer->documento_identidad
            ]);

            // Actualizar contraseña
            $farmer->update([
                'password' => bcrypt($temporaryPassword)
            ]);

            // Enviar email
            $this->farmerService->sendWelcomeEmail($farmer, $temporaryPassword);

            return back()->with('success', 'Email de bienvenida reenviado al agricultor.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al reenviar email: ' . $e->getMessage());
        }
    }

    // ==================== MÉTODOS DE ARCHIVOS ====================

    /**
     * Mostrar página de gestión de archivos.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function showFiles(Proyecto $proyecto)
    {
        $proyecto->load(['imagenes', 'documentos', 'categoria', 'agricultor']);

        $tiposDocumento = UploadProjectDocumentRequest::getTiposDocumentoLabels();

        return view('admin.projects.registration.files', compact(
            'proyecto',
            'tiposDocumento'
        ));
    }

    /**
     * Subir documento al proyecto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function storeDocument(Request $request, Proyecto $proyecto)
    {
        // Validar manualmente (no usar FormRequest para permitir admin)
        $request->validate([
            'documento' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'tipo_documento' => 'required|string|in:' . implode(',', UploadProjectDocumentRequest::TIPOS_DOCUMENTO),
            'descripcion' => 'nullable|string|max:500',
        ], [
            'documento.required' => 'Debe seleccionar un documento.',
            'documento.mimes' => 'El documento debe ser PDF, DOC o DOCX.',
            'documento.max' => 'El documento no puede superar los 5MB.',
            'tipo_documento.required' => 'Debe seleccionar el tipo de documento.',
            'tipo_documento.in' => 'El tipo de documento seleccionado no es válido.',
        ]);

        try {
            $documento = $this->fileUploadService->uploadDocument(
                $request->file('documento'),
                $proyecto,
                $request->tipo_documento,
                $request->descripcion
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento subido exitosamente.',
                    'documento' => [
                        'id' => $documento->id,
                        'nombre' => $documento->nombre_archivo,
                        'tipo' => $documento->tipo_documento,
                        'tipo_label' => UploadProjectDocumentRequest::getTiposDocumentoLabels()[$documento->tipo_documento] ?? $documento->tipo_documento,
                        'tamano' => $this->formatBytes($documento->tamano_bytes),
                        'url' => asset($documento->ruta_archivo),
                        'fecha' => $documento->created_at->format('d/m/Y H:i'),
                    ],
                ]);
            }

            return back()->with('success', 'Documento subido exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir el documento: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Error al subir el documento: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un documento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DocumentoProyecto  $documento
     * @return \Illuminate\Http\Response
     */
    public function destroyDocument(Request $request, DocumentoProyecto $documento)
    {
        try {
            $this->fileUploadService->deleteDocument($documento);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento eliminado exitosamente.',
                ]);
            }

            return back()->with('success', 'Documento eliminado exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el documento.',
                ], 500);
            }

            return back()->with('error', 'Error al eliminar el documento.');
        }
    }

    /**
     * Descargar un documento.
     *
     * @param  \App\Models\DocumentoProyecto  $documento
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDocument(DocumentoProyecto $documento)
    {
        $filePath = public_path($documento->ruta_archivo);

        if (!File::exists($filePath)) {
            abort(404, 'Archivo no encontrado.');
        }

        return response()->download(
            $filePath,
            $documento->nombre_archivo,
            ['Content-Type' => $documento->tipo_mime]
        );
    }

    /**
     * Subir imagen al proyecto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function storeImage(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'imagen' => 'required|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'titulo' => 'nullable|string|max:200',
            'descripcion' => 'nullable|string|max:500',
            'es_principal' => 'nullable|boolean',
        ], [
            'imagen.required' => 'Debe seleccionar una imagen.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La imagen no puede superar los 2MB.',
        ]);

        try {
            $imagen = $this->fileUploadService->uploadImage(
                $request->file('imagen'),
                $proyecto,
                $request->titulo,
                $request->descripcion,
                $request->boolean('es_principal', false)
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen subida exitosamente.',
                    'imagen' => [
                        'id' => $imagen->id,
                        'titulo' => $imagen->titulo,
                        'descripcion' => $imagen->descripcion,
                        'url' => asset($imagen->ruta_imagen),
                        'thumbnail' => asset($imagen->thumbnail),
                        'es_principal' => $imagen->es_principal,
                        'orden' => $imagen->orden,
                        'fecha' => $imagen->created_at->format('d/m/Y H:i'),
                    ],
                ]);
            }

            return back()->with('success', 'Imagen subida exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir la imagen: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Error al subir la imagen: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar una imagen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ImagenProyecto  $imagen
     * @return \Illuminate\Http\Response
     */
    public function destroyImage(Request $request, ImagenProyecto $imagen)
    {
        try {
            $eraPrincipal = $imagen->es_principal;
            $proyectoId = $imagen->proyecto_id;

            $this->fileUploadService->deleteImage($imagen);

            // Si era la principal, asignar la primera como principal
            if ($eraPrincipal) {
                $nuevaPrincipal = ImagenProyecto::where('proyecto_id', $proyectoId)
                    ->orderBy('orden')
                    ->first();

                if ($nuevaPrincipal) {
                    $nuevaPrincipal->update(['es_principal' => true]);
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen eliminada exitosamente.',
                ]);
            }

            return back()->with('success', 'Imagen eliminada exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la imagen.',
                ], 500);
            }

            return back()->with('error', 'Error al eliminar la imagen.');
        }
    }

    /**
     * Establecer imagen como principal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ImagenProyecto  $imagen
     * @return \Illuminate\Http\Response
     */
    public function setImagePrincipal(Request $request, ImagenProyecto $imagen)
    {
        try {
            $this->fileUploadService->setImageAsPrincipal($imagen);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen establecida como principal.',
                ]);
            }

            return back()->with('success', 'Imagen establecida como principal.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al establecer imagen como principal.',
                ], 500);
            }

            return back()->with('error', 'Error al establecer imagen como principal.');
        }
    }

    // ==================== MÉTODOS PRIVADOS ====================

    /**
     * Formatear bytes a formato legible.
     *
     * @param  int  $bytes
     * @return string
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Authorize access to project.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function authorizeProject(Proyecto $proyecto): void
    {
        // Solo proyectos en borrador o rechazados pueden ser editados
        if (!$this->projectService->canEdit($proyecto)) {
            abort(403, 'Este proyecto no puede ser editado en su estado actual.');
        }
    }
}
