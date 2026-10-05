<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\User;
use App\Services\UsuarioService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function __construct(private UsuarioService $usuarios)
    {
    }

    public function index(): View
    {
        $usuarios = User::with('roles')->orderBy('name')->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        return view('usuarios.create');
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $usuario = $this->usuarios->crear($request->validated());

        return redirect()
            ->route('usuarios.index')
            ->with('success', "Usuario {$usuario->name} creado.");
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(UpdateUsuarioRequest $request, User $usuario): RedirectResponse
    {
        try {
            $this->usuarios->actualizar($usuario, $request->validated(), $request->user());
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado.');
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        try {
            $this->usuarios->eliminar($usuario, $request->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }
}
