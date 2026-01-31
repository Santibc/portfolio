<?php

namespace App\Http\Controllers;

use App\Models\CaducidadGeneral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CaducidadGeneralController extends Controller
{
    /**
     * Tipos de caducidad disponibles
     */
    protected const TIPOS = [
        'seguro_rc' => 'Seguro Responsabilidad Civil',
        'iso' => 'Certificación ISO',
        'certificacion' => 'Certificación / Homologación',
        'licencia' => 'Licencia',
        'permiso' => 'Permiso',
        'poliza' => 'Póliza de Seguro',
        'otro' => 'Otro',
    ];

    /**
     * Listado con KPIs y filtros
     */
    public function index(Request $request)
    {
        $query = CaducidadGeneral::query();

        // Filtros
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            switch ($request->estado) {
                case 'vigente':
                    $query->where('fecha_caducidad', '>', now());
                    break;
                case 'proxima':
                    $query->where('fecha_caducidad', '<=', now()->addDays(30))
                        ->where('fecha_caducidad', '>', now());
                    break;
                case 'caducada':
                    $query->where('fecha_caducidad', '<=', now());
                    break;
            }
        }

        $caducidades = $query->orderBy('fecha_caducidad', 'asc')->paginate(15)->withQueryString();

        // Estadísticas
        $stats = [
            'total' => CaducidadGeneral::count(),
            'vigentes' => CaducidadGeneral::where('fecha_caducidad', '>', now())->count(),
            'proximas' => CaducidadGeneral::where('fecha_caducidad', '<=', now()->addDays(30))
                ->where('fecha_caducidad', '>', now())
                ->count(),
            'caducadas' => CaducidadGeneral::where('fecha_caducidad', '<=', now())->count(),
        ];

        $tipos = self::TIPOS;

        return view('caducidades-generales.index', compact('caducidades', 'stats', 'tipos'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $tipos = self::TIPOS;
        return view('caducidades-generales.create', compact('tipos'));
    }

    /**
     * Guardar nueva caducidad general
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:100',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_emision' => 'nullable|date',
            'fecha_caducidad' => 'required|date',
            'documento' => 'nullable|file|max:10240',
            'alerta_activa' => 'sometimes|boolean',
        ]);

        // Subir documento si existe
        $documentoPath = null;
        if ($request->hasFile('documento')) {
            $documentoPath = $this->subirDocumento($request->file('documento'));
        }

        CaducidadGeneral::create([
            'tipo' => $validated['tipo'],
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'fecha_emision' => $validated['fecha_emision'] ?? null,
            'fecha_caducidad' => $validated['fecha_caducidad'],
            'documento_path' => $documentoPath,
            'alerta_activa' => $request->boolean('alerta_activa', true),
        ]);

        return redirect()->route('caducidades-generales.index')
            ->with('success', 'Caducidad registrada correctamente.');
    }

    /**
     * Ver detalle
     */
    public function show(CaducidadGeneral $caducidadGeneral)
    {
        $tipos = self::TIPOS;
        return view('caducidades-generales.show', compact('caducidadGeneral', 'tipos'));
    }

    /**
     * Formulario de edición
     */
    public function edit(CaducidadGeneral $caducidadGeneral)
    {
        $tipos = self::TIPOS;
        return view('caducidades-generales.edit', compact('caducidadGeneral', 'tipos'));
    }

    /**
     * Actualizar
     */
    public function update(Request $request, CaducidadGeneral $caducidadGeneral)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:100',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_emision' => 'nullable|date',
            'fecha_caducidad' => 'required|date',
            'documento' => 'nullable|file|max:10240',
            'alerta_activa' => 'sometimes|boolean',
        ]);

        // Subir nuevo documento si existe
        $documentoPath = $caducidadGeneral->documento_path;
        if ($request->hasFile('documento')) {
            // Eliminar documento anterior
            if ($caducidadGeneral->documento_path) {
                $this->eliminarDocumento($caducidadGeneral->documento_path);
            }
            $documentoPath = $this->subirDocumento($request->file('documento'));
        }

        $caducidadGeneral->update([
            'tipo' => $validated['tipo'],
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'fecha_emision' => $validated['fecha_emision'] ?? null,
            'fecha_caducidad' => $validated['fecha_caducidad'],
            'documento_path' => $documentoPath,
            'alerta_activa' => $request->boolean('alerta_activa', true),
        ]);

        return redirect()->route('caducidades-generales.show', $caducidadGeneral)
            ->with('success', 'Caducidad actualizada correctamente.');
    }

    /**
     * Eliminar (con confirmación SweetAlert)
     */
    public function destroy(CaducidadGeneral $caducidadGeneral)
    {
        // Eliminar documento si existe
        if ($caducidadGeneral->documento_path) {
            $this->eliminarDocumento($caducidadGeneral->documento_path);
        }

        $caducidadGeneral->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Caducidad eliminada correctamente',
            ]);
        }

        return redirect()->route('caducidades-generales.index')
            ->with('success', 'Caducidad eliminada correctamente.');
    }

    /**
     * Subir documento a public/uploads/caducidades/
     */
    protected function subirDocumento($file): string
    {
        $directorio = public_path('uploads/caducidades/' . date('Y'));

        if (!File::exists($directorio)) {
            File::makeDirectory($directorio, 0755, true);
        }

        $nombreArchivo = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $file->move($directorio, $nombreArchivo);

        return 'uploads/caducidades/' . date('Y') . '/' . $nombreArchivo;
    }

    /**
     * Eliminar documento del sistema de archivos
     */
    protected function eliminarDocumento(string $path): void
    {
        $rutaCompleta = public_path($path);
        if (File::exists($rutaCompleta)) {
            File::delete($rutaCompleta);
        }
    }
}
