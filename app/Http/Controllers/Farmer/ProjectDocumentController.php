<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadProjectDocumentRequest;
use App\Models\DocumentoProyecto;
use App\Models\Proyecto;
use App\Services\Storage\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProjectDocumentController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Subir un documento al proyecto
     */
    public function store(UploadProjectDocumentRequest $request, Proyecto $proyecto)
    {
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

            return back()->with('error', 'Error al subir el documento.');
        }
    }

    /**
     * Eliminar un documento
     */
    public function destroy(Request $request, DocumentoProyecto $documento)
    {
        // Verificar que el usuario es el dueño del proyecto
        if ($documento->proyecto->agricultor_id !== auth()->id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para eliminar este documento.',
                ], 403);
            }
            abort(403);
        }

        // Verificar que el proyecto permite edición
        if (!$documento->proyecto->canEdit()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede modificar un proyecto en este estado.',
                ], 403);
            }
            return back()->with('error', 'No se puede modificar un proyecto en este estado.');
        }

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
     * Descargar un documento
     */
    public function download(DocumentoProyecto $documento)
    {
        // Verificar acceso (dueño del proyecto o admin)
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador') || $user->hasRole('Supervisor');
        $esDueno = $documento->proyecto->agricultor_id === $user->id;

        if (!$esAdmin && !$esDueno) {
            abort(403, 'No tiene permiso para descargar este documento.');
        }

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
     * Formatear bytes a formato legible
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
}
