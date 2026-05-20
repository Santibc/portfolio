<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\RegistraActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use RegistraActividad;
    public function index(Request $request)
    {
        $query = User::with(['roles']);

        // Filtro por búsqueda (nombre o email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por rol
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Ordenamiento por columna (default: nombre alfabético)
        $sortableColumns = ['name', 'email', 'created_at'];
        $sort = in_array($request->get('sort'), $sortableColumns) ? $request->get('sort') : 'name';
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';

        $usuarios = $query->orderBy($sort, $direction)->get();
        $roles = Role::all();
        return view('admin.users.index', compact('usuarios', 'roles', 'sort', 'direction'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|exists:roles,name',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol seleccionado no existe.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        $this->registrarCreacion(
            'usuario.creado',
            "Usuario creado: {$user->name} ({$validated['role']})",
            $user,
            null,
            ['rol' => $validated['role']]
        );

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(Request $request, User $user)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? 'Administrador',
            ]);
        }

        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => 'required|exists:roles,name',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol seleccionado no existe.',
        ]);

        $rolAnterior = $user->roles->first()->name ?? null;
        $valoresOriginales = $user->getOriginal();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Sincronizar rol
        $user->syncRoles([$validated['role']]);

        $extra = [];
        if ($rolAnterior !== $validated['role']) {
            $extra['rol'] = ['antes' => $rolAnterior, 'despues' => $validated['role']];
        }
        if (!empty($validated['password'])) {
            $extra['password'] = ['antes' => '***', 'despues' => '*** (cambiado)'];
        }

        $this->registrarActualizacion(
            'usuario.actualizado',
            "Usuario actualizado: {$user->name}",
            $user,
            $valoresOriginales,
            null,
            $extra
        );

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function toggleActivo(User $user)
    {
        // Evitar desactivar el propio usuario
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No puedes desactivar tu propio usuario.');
        }

        $valoresOriginales = $user->getOriginal();
        $nuevoEstado = ! $user->activo;

        $user->update(['activo' => $nuevoEstado]);

        $accion = $nuevoEstado ? 'activado' : 'desactivado';

        $this->registrarActualizacion(
            "usuario.{$accion}",
            "Usuario {$accion}: {$user->name}",
            $user,
            $valoresOriginales,
            null,
            ['activo' => ['antes' => ! $nuevoEstado, 'despues' => $nuevoEstado]]
        );

        return redirect()->route('admin.usuarios.index')
            ->with('success', "Usuario {$accion} exitosamente.");
    }
}
