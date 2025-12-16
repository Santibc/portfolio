<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadProjectImageRequest;
use App\Models\ImagenProyecto;
use App\Models\Proyecto;
use App\Services\Storage\FileUploadService;
use Illuminate\Http\Request;

class ProjectImageController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Subir una imagen al proyecto
     */
    public function store(UploadProjectImageRequest $request, Proyecto $proyecto)
    {
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

            return back()->with('error', 'Error al subir la imagen.');
        }
    }

    /**
     * Eliminar una imagen
     */
    public function destroy(Request $request, ImagenProyecto $imagen)
    {
        // Verificar que el usuario es el dueño del proyecto
        if ($imagen->proyecto->agricultor_id !== auth()->id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para eliminar esta imagen.',
                ], 403);
            }
            abort(403);
        }

        // Verificar que el proyecto permite edición
        if (!$imagen->proyecto->canEdit()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede modificar un proyecto en este estado.',
                ], 403);
            }
            return back()->with('error', 'No se puede modificar un proyecto en este estado.');
        }

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
     * Establecer imagen como principal
     */
    public function setPrincipal(Request $request, ImagenProyecto $imagen)
    {
        // Verificar que el usuario es el dueño del proyecto
        if ($imagen->proyecto->agricultor_id !== auth()->id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para modificar esta imagen.',
                ], 403);
            }
            abort(403);
        }

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

    /**
     * Reordenar imágenes
     */
    public function reorder(Request $request, Proyecto $proyecto)
    {
        // Verificar que el usuario es el dueño del proyecto
        if ($proyecto->agricultor_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permiso para modificar este proyecto.',
            ], 403);
        }

        $request->validate([
            'orden' => 'required|array',
            'orden.*' => 'integer|exists:imagenes_proyecto,id',
        ]);

        try {
            foreach ($request->orden as $index => $imagenId) {
                ImagenProyecto::where('id', $imagenId)
                    ->where('proyecto_id', $proyecto->id)
                    ->update(['orden' => $index + 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reordenar las imágenes.',
            ], 500);
        }
    }
}
