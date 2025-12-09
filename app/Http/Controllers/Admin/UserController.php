<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')->orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('usuarios'));
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

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
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

        $usuario->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $usuario->update(['password' => Hash::make($validated['password'])]);
        }

        // Sincronizar rol
        $usuario->syncRoles([$validated['role']]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $usuario)
    {
        // Evitar eliminar el propio usuario
        if ($usuario->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
