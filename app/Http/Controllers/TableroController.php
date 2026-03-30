<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\Tablero;
use App\Models\User;
use App\Services\TableroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TableroController extends Controller
{
    protected TableroService $service;

    public function __construct(TableroService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = auth()->user();
        $tableros = $this->service->getTablerosParaUsuario($user);
        return view('tableros.index', compact('tableros'));
    }

    public function show(Tablero $tablero)
    {
        $user = auth()->user();
        if (!$tablero->esAccesiblePor($user)) {
            abort(403);
        }

        $tablero->load([
            'columnas' => function ($q) {
                $q->where('archivada', false)->orderBy('posicion');
            },
            'columnas.tarjetas' => function ($q) {
                $q->where('archivada', false)->orderBy('posicion');
            },
            'columnas.tarjetas.usuarios',
            'columnas.tarjetas.etiquetas',
            'columnas.tarjetas.checklists.items',
            'columnas.tarjetas.comentarios',
            'columnas.tarjetas.adjuntos',
            'miembros',
            'etiquetas',
            'obra',
        ]);

        $usuarios = User::select('id', 'name', 'profile_photo')->orderBy('name')->get();
        $puedeEditar = $tablero->puedeEditar($user);

        $miembrosJson = $tablero->miembros->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'initials' => $m->initials,
                'profile_photo' => $m->profile_photo,
                'profile_photo_url' => $m->profile_photo_url,
            ];
        });

        return view('tableros.show', compact('tablero', 'usuarios', 'puedeEditar', 'miembrosJson'));
    }

    public function create()
    {
        $obras = Obra::select('id', 'nombre', 'codigo')
            ->whereIn('estado', ['aprobada', 'en_curso'])
            ->orderBy('nombre')
            ->get();
        $usuarios = User::select('id', 'name')->orderBy('name')->get();
        return view('tableros.create', compact('obras', 'usuarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color_fondo' => 'nullable|string|max:7',
            'imagen_fondo' => 'nullable|image|max:2048',
            'visibilidad' => 'required|in:todos,roles,miembros',
            'roles_visibles' => 'nullable|array',
            'obra_id' => 'nullable|exists:obras,id',
            'miembros' => 'nullable|array',
            'miembros.*' => 'exists:users,id',
        ]);

        if ($request->hasFile('imagen_fondo')) {
            $dir = public_path('uploads/tableros/fondos');
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $file = $request->file('imagen_fondo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($dir, $filename);
            $validated['imagen_fondo'] = 'tableros/fondos/' . $filename;
        } else {
            unset($validated['imagen_fondo']);
        }

        $miembros = $request->input('miembros', []);
        $tablero = $this->service->crearTablero($validated, auth()->user(), $miembros);

        return redirect()->route('tableros.show', $tablero)
            ->with('success', 'Tablero creado correctamente');
    }

    public function edit(Tablero $tablero)
    {
        $user = auth()->user();
        if (!$tablero->puedeEditar($user) && !$user->isAdmin()) {
            abort(403);
        }

        $obras = Obra::select('id', 'nombre', 'codigo')
            ->whereIn('estado', ['aprobada', 'en_curso'])
            ->orderBy('nombre')
            ->get();

        $tablero->load('miembros');
        $usuarios = User::select('id', 'name', 'profile_photo')->orderBy('name')->get();

        return view('tableros.edit', compact('tablero', 'obras', 'usuarios'));
    }

    public function update(Request $request, Tablero $tablero)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color_fondo' => 'nullable|string|max:7',
            'imagen_fondo' => 'nullable|image|max:2048',
            'visibilidad' => 'required|in:todos,roles,miembros',
            'roles_visibles' => 'nullable|array',
            'obra_id' => 'nullable|exists:obras,id',
        ]);

        // Remove background image
        if ($request->has('eliminar_imagen') && $tablero->imagen_fondo) {
            $oldPath = public_path('uploads/' . $tablero->imagen_fondo);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
            $validated['imagen_fondo'] = null;
        }

        // Upload new background image
        if ($request->hasFile('imagen_fondo')) {
            // Delete old image if exists
            if ($tablero->imagen_fondo) {
                $oldPath = public_path('uploads/' . $tablero->imagen_fondo);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            $dir = public_path('uploads/tableros/fondos');
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $file = $request->file('imagen_fondo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($dir, $filename);
            $validated['imagen_fondo'] = 'tableros/fondos/' . $filename;
        } else {
            unset($validated['imagen_fondo']);
        }

        $tablero->update($validated);

        return redirect()->route('tableros.show', $tablero)
            ->with('success', 'Tablero actualizado correctamente');
    }

    public function destroy(Tablero $tablero)
    {
        $tablero->delete();

        return redirect()->route('tableros.index')
            ->with('success', 'Tablero eliminado correctamente');
    }

    public function archivar(Tablero $tablero)
    {
        $tablero->update(['archivado' => !$tablero->archivado]);

        return response()->json([
            'success' => true,
            'archivado' => $tablero->archivado,
        ]);
    }

    public function miembros(Tablero $tablero)
    {
        return response()->json($tablero->miembros()->select('users.id', 'name', 'email', 'profile_photo')->get());
    }

    public function agregarMiembro(Request $request, Tablero $tablero)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rol' => 'nullable|in:propietario,editor,observador',
        ]);

        if ($tablero->miembros()->where('users.id', $request->user_id)->exists()) {
            return response()->json(['error' => 'El usuario ya es miembro'], 422);
        }

        $tablero->miembros()->attach($request->user_id, [
            'rol' => $request->rol ?? 'editor',
        ]);

        return response()->json(['success' => true]);
    }

    public function removerMiembro(Tablero $tablero, User $user)
    {
        if ($tablero->creado_por === $user->id) {
            return response()->json(['error' => 'No se puede eliminar al creador del tablero'], 422);
        }

        $tablero->miembros()->detach($user->id);

        return response()->json(['success' => true]);
    }

    public function usuariosPorRol(Request $request)
    {
        $roles = $request->input('roles', []);
        if (empty($roles)) {
            return response()->json([]);
        }
        $usuarios = User::role($roles)->select('id', 'name')->orderBy('name')->get();
        return response()->json($usuarios);
    }

    public function usuariosPorObra(Request $request, Obra $obra)
    {
        $userIds = [];
        if ($obra->encargado_id) {
            $userIds[] = $obra->encargado_id;
        }
        $trabajadorUserIds = $obra->trabajadoresActivos()
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->toArray();
        $userIds = array_unique(array_merge($userIds, $trabajadorUserIds));

        $usuarios = User::whereIn('id', $userIds)->select('id', 'name')->orderBy('name')->get();
        return response()->json($usuarios);
    }
}
