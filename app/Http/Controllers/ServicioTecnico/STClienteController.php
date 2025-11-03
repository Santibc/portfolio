<?php

namespace App\Http\Controllers\ServicioTecnico;

use App\Http\Controllers\Controller;
use App\Models\STCliente;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class STClienteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = STCliente::query();

            return DataTables::of($query)
                ->addColumn('action', function ($cliente) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="' . route('st.clientes.show', $cliente->id) . '" class="btn btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="' . route('st.clientes.edit', $cliente->id) . '" class="btn btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-danger" onclick="eliminar(' . $cliente->id . ')" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->addColumn('tipo_cliente_badge', function ($cliente) {
                    $badge = $cliente->tipo_cliente === 'empresa' ? 'primary' : 'secondary';
                    return '<span class="badge bg-' . $badge . '">' . ucfirst($cliente->tipo_cliente) . '</span>';
                })
                ->addColumn('estado_badge', function ($cliente) {
                    $badge = $cliente->activo ? 'success' : 'danger';
                    $text = $cliente->activo ? 'Activo' : 'Inactivo';
                    return '<span class="badge bg-' . $badge . '">' . $text . '</span>';
                })
                ->addColumn('equipos_count', function ($cliente) {
                    return $cliente->equipos()->count();
                })
                ->rawColumns(['action', 'tipo_cliente_badge', 'estado_badge'])
                ->make(true);
        }

        return view('servicio-tecnico.clientes.index');
    }

    public function create()
    {
        return view('servicio-tecnico.clientes.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:50|unique:st_clientes,numero_documento',
            'nombre_completo' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'required|string|max:20',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'tipo_cliente' => 'required|in:particular,empresa',
            'observaciones' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        STCliente::create($validated);

        return redirect()->route('st.clientes.index')
            ->with('success', 'Cliente registrado exitosamente');
    }

    public function show(STCliente $cliente)
    {
        $cliente->load(['equipos', 'ordenesServicio.tecnico']);

        return view('servicio-tecnico.clientes.show', compact('cliente'));
    }

    public function edit(STCliente $cliente)
    {
        return view('servicio-tecnico.clientes.form', compact('cliente'));
    }

    public function update(Request $request, STCliente $cliente)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|string|max:20',
            'numero_documento' => 'required|string|max:50|unique:st_clientes,numero_documento,' . $cliente->id,
            'nombre_completo' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'required|string|max:20',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'tipo_cliente' => 'required|in:particular,empresa',
            'observaciones' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $cliente->update($validated);

        return redirect()->route('st.clientes.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy(STCliente $cliente)
    {
        try {
            $cliente->activo = false;
            $cliente->save();

            return response()->json(['success' => true, 'message' => 'Cliente desactivado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al desactivar el cliente'], 500);
        }
    }
}
