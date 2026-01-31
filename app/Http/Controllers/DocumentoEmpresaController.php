<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\DocumentoEmpresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocumentoEmpresaController extends Controller
{
    /**
     * Listado de documentos de empresa
     */
    public function index(Request $request): View
    {
        $query = DocumentoEmpresa::with('subidoPor');

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // Filtro por estado de caducidad
        if ($request->filled('estado')) {
            switch ($request->estado) {
                case 'vigente':
                    $query->vigentes();
                    break;
                case 'proximo':
                    $query->proximosACaducar(30);
                    break;
                case 'caducado':
                    $query->caducados();
                    break;
            }
        }

        // Búsqueda por nombre
        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                    ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        // Estadísticas
        $stats = [
            'total' => DocumentoEmpresa::count(),
            'vigentes' => DocumentoEmpresa::vigentes()->count(),
            'proximos' => DocumentoEmpresa::proximosACaducar(30)->count(),
            'caducados' => DocumentoEmpresa::caducados()->count(),
        ];

        // Documentos por categoría
        $porCategoria = DocumentoEmpresa::selectRaw('categoria, COUNT(*) as total')
            ->groupBy('categoria')
            ->pluck('total', 'categoria')
            ->toArray();

        $documentos = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('documentos-empresa.index', compact('documentos', 'stats', 'porCategoria'));
    }

    /**
     * Formulario de nuevo documento
     */
    public function create(): View
    {
        $categorias = DocumentoEmpresa::CATEGORIAS;
        return view('documentos-empresa.create', compact('categorias'));
    }

    /**
     * Guardar nuevo documento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'categoria' => 'required|in:' . implode(',', array_keys(DocumentoEmpresa::CATEGORIAS)),
            'archivo' => 'required|file|max:10240',
            'fecha_documento' => 'nullable|date',
            'fecha_caducidad' => 'nullable|date|after_or_equal:fecha_documento',
            'notas' => 'nullable|string|max:2000',
        ], [
            'nombre.required' => 'El nombre del documento es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
            'categoria.required' => 'La categoría es obligatoria.',
            'categoria.in' => 'La categoría seleccionada no es válida.',
            'archivo.required' => 'Debe adjuntar un archivo.',
            'archivo.file' => 'El archivo adjunto no es válido.',
            'archivo.max' => 'El archivo no puede superar los 10MB.',
            'fecha_caducidad.after_or_equal' => 'La fecha de caducidad debe ser posterior a la fecha del documento.',
        ]);

        DB::beginTransaction();

        try {
            // Procesar archivo
            $archivo = $request->file('archivo');
            $año = date('Y');
            $mes = date('m');
            $directorio = public_path("uploads/documentos-empresa/{$año}/{$mes}");

            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }

            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $tamaño = $archivo->getSize();
            $nombreArchivo = 'doc_empresa_' . time() . '_' . uniqid() . '.' . $extension;

            $archivo->move($directorio, $nombreArchivo);

            $documento = DocumentoEmpresa::create([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'categoria' => $validated['categoria'],
                'archivo_path' => "uploads/documentos-empresa/{$año}/{$mes}/{$nombreArchivo}",
                'archivo_nombre_original' => $nombreOriginal,
                'archivo_extension' => strtolower($extension),
                'archivo_tamaño' => $tamaño,
                'fecha_documento' => $validated['fecha_documento'] ?? null,
                'fecha_caducidad' => $validated['fecha_caducidad'] ?? null,
                'notas' => $validated['notas'] ?? null,
                'subido_por' => Auth::id(),
            ]);

            Auditoria::registrar('crear', 'documentos_empresa', $documento->id, null, $documento->toArray());

            DB::commit();

            return redirect()
                ->route('documentos-empresa.index')
                ->with('success', 'Documento subido correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Eliminar archivo si se subió pero falló la BD
            if (isset($nombreArchivo) && file_exists($directorio . '/' . $nombreArchivo)) {
                unlink($directorio . '/' . $nombreArchivo);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al subir el documento: ' . $e->getMessage()]);
        }
    }

    /**
     * Ver detalle del documento
     */
    public function show(DocumentoEmpresa $documentos_empresa): View
    {
        $documentos_empresa->load('subidoPor');
        return view('documentos-empresa.show', ['documento' => $documentos_empresa]);
    }

    /**
     * Formulario de edición
     */
    public function edit(DocumentoEmpresa $documentos_empresa): View
    {
        $categorias = DocumentoEmpresa::CATEGORIAS;
        return view('documentos-empresa.edit', [
            'documento' => $documentos_empresa,
            'categorias' => $categorias,
        ]);
    }

    /**
     * Actualizar documento
     */
    public function update(Request $request, DocumentoEmpresa $documentos_empresa)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'categoria' => 'required|in:' . implode(',', array_keys(DocumentoEmpresa::CATEGORIAS)),
            'archivo' => 'nullable|file|max:10240',
            'fecha_documento' => 'nullable|date',
            'fecha_caducidad' => 'nullable|date|after_or_equal:fecha_documento',
            'notas' => 'nullable|string|max:2000',
        ], [
            'nombre.required' => 'El nombre del documento es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
            'categoria.required' => 'La categoría es obligatoria.',
            'categoria.in' => 'La categoría seleccionada no es válida.',
            'archivo.file' => 'El archivo adjunto no es válido.',
            'archivo.max' => 'El archivo no puede superar los 10MB.',
            'fecha_caducidad.after_or_equal' => 'La fecha de caducidad debe ser posterior a la fecha del documento.',
        ]);

        DB::beginTransaction();

        try {
            $datosAnteriores = $documentos_empresa->toArray();

            $datosActualizar = [
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'categoria' => $validated['categoria'],
                'fecha_documento' => $validated['fecha_documento'] ?? null,
                'fecha_caducidad' => $validated['fecha_caducidad'] ?? null,
                'notas' => $validated['notas'] ?? null,
            ];

            // Si se sube nuevo archivo
            if ($request->hasFile('archivo')) {
                // Eliminar archivo anterior
                if ($documentos_empresa->archivo_path && file_exists(public_path($documentos_empresa->archivo_path))) {
                    unlink(public_path($documentos_empresa->archivo_path));
                }

                $archivo = $request->file('archivo');
                $año = date('Y');
                $mes = date('m');
                $directorio = public_path("uploads/documentos-empresa/{$año}/{$mes}");

                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                $nombreOriginal = $archivo->getClientOriginalName();
                $extension = $archivo->getClientOriginalExtension();
                $tamaño = $archivo->getSize();
                $nombreArchivo = 'doc_empresa_' . time() . '_' . uniqid() . '.' . $extension;

                $archivo->move($directorio, $nombreArchivo);

                $datosActualizar['archivo_path'] = "uploads/documentos-empresa/{$año}/{$mes}/{$nombreArchivo}";
                $datosActualizar['archivo_nombre_original'] = $nombreOriginal;
                $datosActualizar['archivo_extension'] = strtolower($extension);
                $datosActualizar['archivo_tamaño'] = $tamaño;
            }

            $documentos_empresa->update($datosActualizar);

            Auditoria::registrar('editar', 'documentos_empresa', $documentos_empresa->id, $datosAnteriores, $documentos_empresa->fresh()->toArray());

            DB::commit();

            return redirect()
                ->route('documentos-empresa.show', $documentos_empresa)
                ->with('success', 'Documento actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el documento: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar documento (AJAX)
     */
    public function destroy(DocumentoEmpresa $documentos_empresa): JsonResponse
    {
        try {
            $datosAnteriores = $documentos_empresa->toArray();

            // Eliminar archivo físico
            if ($documentos_empresa->archivo_path && file_exists(public_path($documentos_empresa->archivo_path))) {
                unlink(public_path($documentos_empresa->archivo_path));
            }

            Auditoria::registrar('eliminar', 'documentos_empresa', $documentos_empresa->id, $datosAnteriores, null);

            $documentos_empresa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Documento eliminado correctamente.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el documento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Descargar documento
     */
    public function descargar(DocumentoEmpresa $documentos_empresa)
    {
        if (!$documentos_empresa->archivoExiste()) {
            return back()->withErrors(['error' => 'El archivo no existe o fue eliminado.']);
        }

        $rutaCompleta = public_path($documentos_empresa->archivo_path);
        $nombreDescarga = $documentos_empresa->archivo_nombre_original;

        return response()->download($rutaCompleta, $nombreDescarga);
    }
}
