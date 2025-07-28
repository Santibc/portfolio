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
                ->addColumn('action', function ($user) {
                    $editUrl = route('usuarios.form', $user->id);

                    $buttons = '<div class="d-flex justify-content-center align-items-center">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-outline-info btn-sm" title="Editar">';
                    $buttons .= '<i class="bi bi-pencil"></i>';
                    $buttons .= '</a>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('usuarios.usuarios_index');
    }



    public function form(User $user = null)
    {
        $user  = $user ?? new User();
        $roles = Role::pluck('name','name');      // ← lista de roles (clave and valor = nombre)

        return view('usuarios.usuarios_form', compact('user','roles'));
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
        } else {
            $user = $this->userService->create($data);
        }
   $user->syncRoles($data['role']);
        return redirect()->route('usuarios')->with('success', 'Usuario guardado correctamente.');
    }
}
