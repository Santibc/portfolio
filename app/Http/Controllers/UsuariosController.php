<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Services\UserCreationService;
use Spatie\Permission\Models\Role;
use App\Services\CalendlyUserImporter;
use App\Services\UserSynchronizationService;
use App\Models\Ubicacion;

class UsuariosController extends Controller
{

    private UserCreationService $userService;

    public function __construct()
    {
        $this->userService = new UserCreationService();;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::query()->where('id', '!=', 1);

            return DataTables::of($users)
                    ->addColumn('roles', function($u) {
                        // toma el primer rol o concatena varios
                        return $u->getRoleNames()
                                ->map(fn($r) => ucfirst($r))
                                ->join(', ');
                    })
                    ->addColumn('estado', function($u) {
                        if ($u->activo) {
                            return '<span class="badge bg-success">Activo</span>';
                        }
                        return '<span class="badge bg-secondary">Inactivo</span>';
                    })
                ->addColumn('action', function ($user) {
                    $editUrl = route('usuarios.form', $user->id);

                    $buttons = '<div class="d-flex justify-content-center align-items-center">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-outline-info btn-sm" title="Editar">';
                    $buttons .= '<i class="bi bi-pencil"></i>';
                    $buttons .= '</a>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action', 'estado'])
                ->make(true);
        }

        return view('usuarios.usuarios_index');
    }



    public function form(User $user = null)
    {
        $user  = $user ?? new User();
        $roles = Role::where('name', '!=', 'cliente')->pluck('name','name');
        $ubicaciones = Ubicacion::activas()->with('feria')->orderBy('nombre')->get();

        return view('usuarios.usuarios_form', compact('user','roles','ubicaciones'));
    }

    public function guardar(Request $request)
    {
        $user = $request->id ? User::findOrFail($request->id) : null;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')->ignore($user?->id)
            ],
            'password' => $user ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'],
             'role'     => ['required','exists:roles,name'],
             'ubicacion_id' => ['nullable', 'exists:ubicaciones,id'],
        ];

        $messages = [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Debe ser un correo válido.',
            'max' => 'No debe superar los :max caracteres.',
            'unique' => 'Ya existe un usuario con este correo.',
            'min' => 'Debe tener al menos :min caracteres.',
        ];

        $data = $request->validate($rules, $messages);

        // 1) Crear o actualizar usuario
        if ($user) {
            $this->userService->update($user, $data);
            // Actualizar estado activo solo en edición
            $user->activo = $request->has('activo') ? 1 : 0;
            $user->ubicacion_id = $data['ubicacion_id'] ?? null;
            $user->save();
        } else {
            $user = $this->userService->create($data);
            $user->activo = 1; // Nuevo usuario siempre activo
            $user->ubicacion_id = $data['ubicacion_id'] ?? null;
            $user->save();
        }
        $user->syncRoles($data['role']);
        return redirect()->route('usuarios')->with('success', 'Usuario guardado correctamente.');
    }
}
