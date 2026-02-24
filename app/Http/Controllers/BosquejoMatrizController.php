<?php

namespace App\Http\Controllers;

use App\Models\GrupoBosquejo;
use App\Models\PlantillaBosquejo;
use App\Traits\RegistraActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BosquejoMatrizController extends Controller
{
    use RegistraActividad;

    public function __construct()
    {
        $this->middleware('permission:gestionar_bosquejos_matriz')
            ->only(['storeGrupo', 'updateGrupo', 'destroyGrupo', 'storeBosquejo', 'updateBosquejo', 'destroyBosquejo']);
    }

    /**
     * Pagina principal con accordion de grupos y tarjetas de bosquejos.
     */
    public function index()
    {
        $grupos = GrupoBosquejo::with(['plantillas' => function ($q) {
            $q->orderBy('nombre');
        }])->orderBy('nombre')->get();

        $totalGrupos = GrupoBosquejo::count();
        $totalBosquejos = PlantillaBosquejo::count();
        $bosquejosSinGrupo = PlantillaBosquejo::whereNull('grupo_bosquejo_id')->count();

        $bosquejosSueltos = PlantillaBosquejo::whereNull('grupo_bosquejo_id')
            ->orderBy('nombre')->get();

        return view('bosquejos-matriz.index', compact(
            'grupos',
            'totalGrupos',
            'totalBosquejos',
            'bosquejosSinGrupo',
            'bosquejosSueltos'
        ));
    }

    /**
     * Crear nuevo grupo (AJAX).
     */
    public function storeGrupo(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ], [
            'nombre.required' => 'El nombre del grupo es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
        ]);

        $grupo = GrupoBosquejo::create($validated);

        $this->registrarActividad(
            'bosquejo_grupo.creado',
            "Se creo el grupo de bosquejos: {$grupo->nombre}",
            null,
            ['grupo_bosquejo_id' => $grupo->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Grupo creado exitosamente.',
            'grupo' => $grupo,
        ]);
    }

    /**
     * Renombrar grupo (AJAX).
     */
    public function updateGrupo(Request $request, GrupoBosquejo $grupo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $nombreAnterior = $grupo->nombre;
        $grupo->update($validated);

        $this->registrarActividad(
            'bosquejo_grupo.actualizado',
            "Se renombro el grupo: '{$nombreAnterior}' a '{$grupo->nombre}'",
            null,
            ['grupo_bosquejo_id' => $grupo->id, 'nombre_anterior' => $nombreAnterior]
        );

        return response()->json([
            'success' => true,
            'message' => 'Grupo actualizado exitosamente.',
        ]);
    }

    /**
     * Eliminar grupo con todos sus bosquejos y archivos (AJAX).
     */
    public function destroyGrupo(GrupoBosquejo $grupo)
    {
        $nombreGrupo = $grupo->nombre;
        $plantillas = $grupo->plantillas;

        foreach ($plantillas as $plantilla) {
            $this->eliminarArchivosBosquejo($plantilla);
        }

        $grupo->plantillas()->delete();

        $dirPath = public_path("uploads/bosquejos-matriz/{$grupo->id}");
        if (File::isDirectory($dirPath)) {
            File::deleteDirectory($dirPath);
        }

        $grupo->delete();

        $this->registrarActividad(
            'bosquejo_grupo.eliminado',
            "Se elimino el grupo: '{$nombreGrupo}' con {$plantillas->count()} bosquejos",
            null,
            ['grupo_nombre' => $nombreGrupo, 'bosquejos_eliminados' => $plantillas->count()]
        );

        return response()->json([
            'success' => true,
            'message' => "Grupo '{$nombreGrupo}' eliminado exitosamente.",
        ]);
    }

    /**
     * Subir bosquejo a un grupo o individual (AJAX).
     */
    public function storeBosquejo(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'grupo_bosquejo_id' => 'nullable|exists:grupos_bosquejos,id',
            'archivo' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
        ], [
            'nombre.required' => 'El nombre del bosquejo es obligatorio.',
            'grupo_bosquejo_id.exists' => 'El grupo seleccionado no existe.',
            'archivo.required' => 'Debe seleccionar una imagen.',
            'archivo.image' => 'El archivo debe ser una imagen.',
            'archivo.mimes' => 'Solo se permiten imagenes JPG, PNG y WebP.',
            'archivo.max' => 'La imagen no puede exceder 10MB.',
        ]);

        $grupoId = $validated['grupo_bosquejo_id'] ?? null;
        $uploadSubDir = $grupoId ?: 'individuales';
        $file = $request->file('archivo');

        $uploadPath = public_path("uploads/bosquejos-matriz/{$uploadSubDir}");
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Crear registro para obtener ID
        $bosquejo = PlantillaBosquejo::create([
            'grupo_bosquejo_id' => $grupoId,
            'nombre' => $validated['nombre'],
            'ruta_archivo' => '',
            'ruta_miniatura' => null,
        ]);

        $extension = $file->getClientOriginalExtension();
        $timestamp = time();
        $fileNameBase = "bosquejo_{$bosquejo->id}_{$timestamp}";

        // Guardar original
        $originalName = "{$fileNameBase}.{$extension}";
        $file->move($uploadPath, $originalName);
        $rutaArchivo = "uploads/bosquejos-matriz/{$uploadSubDir}/{$originalName}";

        // Generar miniatura
        $thumbName = "{$fileNameBase}_thumb.{$extension}";
        $thumbFullPath = "{$uploadPath}/{$thumbName}";
        $rutaMiniatura = "uploads/bosquejos-matriz/{$uploadSubDir}/{$thumbName}";

        try {
            $img = Image::make("{$uploadPath}/{$originalName}");
            $img->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save($thumbFullPath, 80);
        } catch (\Exception $e) {
            $rutaMiniatura = $rutaArchivo;
        }

        $bosquejo->update([
            'ruta_archivo' => $rutaArchivo,
            'ruta_miniatura' => $rutaMiniatura,
        ]);

        $desc = $grupoId
            ? "Se subio el bosquejo: '{$bosquejo->nombre}' al grupo ID {$grupoId}"
            : "Se subio el bosquejo individual: '{$bosquejo->nombre}'";

        $this->registrarActividad(
            'bosquejo.creado',
            $desc,
            null,
            ['plantilla_bosquejo_id' => $bosquejo->id, 'grupo_bosquejo_id' => $grupoId]
        );

        return response()->json([
            'success' => true,
            'message' => 'Bosquejo subido exitosamente.',
            'bosquejo' => $bosquejo->fresh(),
        ]);
    }

    /**
     * Renombrar bosquejo (AJAX).
     */
    public function updateBosquejo(Request $request, PlantillaBosquejo $bosquejo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $nombreAnterior = $bosquejo->nombre;
        $bosquejo->update($validated);

        $this->registrarActividad(
            'bosquejo.actualizado',
            "Se renombro el bosquejo: '{$nombreAnterior}' a '{$bosquejo->nombre}'",
            null,
            ['plantilla_bosquejo_id' => $bosquejo->id, 'nombre_anterior' => $nombreAnterior]
        );

        return response()->json([
            'success' => true,
            'message' => 'Bosquejo actualizado exitosamente.',
        ]);
    }

    /**
     * Eliminar bosquejo y sus archivos (AJAX).
     */
    public function destroyBosquejo(PlantillaBosquejo $bosquejo)
    {
        $nombreBosquejo = $bosquejo->nombre;

        $this->eliminarArchivosBosquejo($bosquejo);
        $bosquejo->delete();

        $this->registrarActividad(
            'bosquejo.eliminado',
            "Se elimino el bosquejo: '{$nombreBosquejo}'",
            null,
            ['bosquejo_nombre' => $nombreBosquejo]
        );

        return response()->json([
            'success' => true,
            'message' => "Bosquejo '{$nombreBosquejo}' eliminado exitosamente.",
        ]);
    }

    /**
     * Descargar archivo original del bosquejo.
     */
    public function downloadBosquejo(PlantillaBosquejo $bosquejo)
    {
        $filePath = public_path($bosquejo->ruta_archivo);

        if (!File::exists($filePath)) {
            abort(404, 'Archivo no encontrado.');
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $downloadName = Str::slug($bosquejo->nombre) . '.' . $extension;

        return response()->download($filePath, $downloadName);
    }

    /**
     * Eliminar archivos fisicos de un bosquejo.
     */
    private function eliminarArchivosBosquejo(PlantillaBosquejo $bosquejo): void
    {
        if ($bosquejo->ruta_archivo && File::exists(public_path($bosquejo->ruta_archivo))) {
            File::delete(public_path($bosquejo->ruta_archivo));
        }

        if (
            $bosquejo->ruta_miniatura &&
            $bosquejo->ruta_miniatura !== $bosquejo->ruta_archivo &&
            File::exists(public_path($bosquejo->ruta_miniatura))
        ) {
            File::delete(public_path($bosquejo->ruta_miniatura));
        }
    }
}
