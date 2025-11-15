<?php

namespace App\Http\Controllers\ServicioTecnico;

use App\Http\Controllers\Controller;
use App\Models\STTecnico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class STTecnicoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = STTecnico::query();

            // Aplicar filtros
            if ($request->filled('especialidad')) {
                $query->where('especialidad', 'like', '%' . $request->especialidad . '%');
            }

            if ($request->filled('activo')) {
                $query->where('activo', $request->activo);
            }

            return DataTables::of($query)
                ->editColumn('codigo', function ($tecnico) {
                    return '<span class="badge bg-primary">' . $tecnico->codigo . '</span>';
                })
                ->addColumn('contacto', function ($tecnico) {
                    $info = [];
                    if ($tecnico->telefono) $info[] = '<i class="bi bi-telephone"></i> ' . $tecnico->telefono;
                    if ($tecnico->celular) $info[] = '<i class="bi bi-phone"></i> ' . $tecnico->celular;
                    if ($tecnico->email) $info[] = '<i class="bi bi-envelope"></i> ' . $tecnico->email;
                    return implode('<br>', $info) ?: 'No especificado';
                })
                ->editColumn('fecha_ingreso', function ($tecnico) {
                    return $tecnico->fecha_ingreso ? $tecnico->fecha_ingreso->format('d/m/Y') : 'N/A';
                })
                ->addColumn('estado', function ($tecnico) {
                    if ($tecnico->activo) {
                        return '<span class="badge bg-success">Activo</span>';
                    }
                    return '<span class="badge bg-secondary">Inactivo</span>';
                })
                ->addColumn('action', function ($tecnico) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="' . route('st.tecnicos.show', $tecnico->id) . '" class="btn btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="' . route('st.tecnicos.edit', $tecnico->id) . '" class="btn btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="' . route('st.tecnicos.destroy', $tecnico->id) . '" method="POST" class="d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger btn-eliminar" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->filterColumn('contacto', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('telefono', 'like', "%{$keyword}%")
                          ->orWhere('celular', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['codigo', 'contacto', 'estado', 'action'])
                ->make(true);
        }

        return view('servicio-tecnico.tecnicos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('servicio-tecnico.tecnicos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:st_tecnicos,codigo',
            'nombre_completo' => 'required|string|max:255',
            'documento' => 'required|string|max:50|unique:st_tecnicos,documento',
            'email' => 'required|email|max:255|unique:st_tecnicos,email|unique:users,email',
            'telefono' => 'required|string|max:20',
            'celular' => 'required|string|max:20',
            'especialidad' => 'nullable|string|max:255',
            'certificaciones' => 'nullable|string',
            'fecha_ingreso' => 'nullable|date',
        ]);

        $validated['activo'] = true;

        DB::beginTransaction();

        try {
            // Crear el usuario primero
            $user = User::create([
                'name' => $validated['nombre_completo'],
                'email' => $validated['email'],
                'password' => Hash::make('12345678'), // Contraseña por defecto
            ]);

            // Asignar el rol de técnico
            $user->assignRole('tecnico');

            // Crear el técnico con el user_id
            $validated['user_id'] = $user->id;
            STTecnico::create($validated);

            DB::commit();

            return redirect()->route('st.tecnicos.index')
                ->with('success', 'Técnico registrado exitosamente. Usuario creado con contraseña: 12345678');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el técnico: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tecnico = STTecnico::with('ordenesServicio')->findOrFail($id);
        return view('servicio-tecnico.tecnicos.show', compact('tecnico'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tecnico = STTecnico::findOrFail($id);
        return view('servicio-tecnico.tecnicos.create', compact('tecnico'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tecnico = STTecnico::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:st_tecnicos,codigo,' . $id,
            'nombre_completo' => 'required|string|max:255',
            'documento' => 'required|string|max:50|unique:st_tecnicos,documento,' . $id,
            'email' => 'required|email|max:255|unique:st_tecnicos,email,' . $id,
            'telefono' => 'required|string|max:20',
            'celular' => 'required|string|max:20',
            'especialidad' => 'nullable|string|max:255',
            'certificaciones' => 'nullable|string',
            'fecha_ingreso' => 'nullable|date',
            'activo' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Actualizar el técnico
            $tecnico->update($validated);

            // Si el técnico tiene un usuario asociado, actualizar sus datos
            if ($tecnico->user) {
                $tecnico->user->update([
                    'name' => $validated['nombre_completo'],
                    'email' => $validated['email'],
                ]);

                // Si se desactiva el técnico, también desactivar su usuario (si quieres)
                // Nota: Laravel no tiene campo 'active' por defecto, esto depende de tu implementación
            }

            DB::commit();

            return redirect()->route('st.tecnicos.index')
                ->with('success', 'Técnico actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el técnico: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tecnico = STTecnico::findOrFail($id);

        // Verificar si tiene órdenes de servicio asignadas
        if ($tecnico->ordenesServicio()->count() > 0) {
            return redirect()->route('st.tecnicos.index')
                ->with('error', 'No se puede eliminar el técnico porque tiene órdenes de servicio asignadas');
        }

        DB::beginTransaction();

        try {
            // Eliminar el usuario asociado si existe
            if ($tecnico->user) {
                $tecnico->user->delete();
            }

            // Eliminar el técnico
            $tecnico->delete();

            DB::commit();

            return redirect()->route('st.tecnicos.index')
                ->with('success', 'Técnico eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('st.tecnicos.index')
                ->with('error', 'Error al eliminar el técnico: ' . $e->getMessage());
        }
    }
}
