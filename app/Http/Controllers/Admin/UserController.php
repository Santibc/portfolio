<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
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

        // Filtro por estado (basado en email_verified_at)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNull('email_verified_at');
            }
        }

        $usuarios = $query->orderBy('created_at', 'desc')->get();
        $roles = Role::where('name', '!=', 'Trabajador')->get();
        $allRoles = Role::all();
        return view('admin.users.index', compact('usuarios', 'roles', 'allRoles'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'Trabajador')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'exists:roles,name', Rule::notIn(['Trabajador'])],
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
            'role.not_in' => 'El rol Trabajador se asigna automáticamente al crear un trabajador.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

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

        $roles = Role::where('name', '!=', 'Trabajador')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', 'exists:roles,name', Rule::notIn(['Trabajador'])],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol seleccionado no existe.',
            'role.not_in' => 'El rol Trabajador se asigna automáticamente al crear un trabajador.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Sincronizar rol
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // Evitar eliminar el propio usuario
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
