<?php

namespace App\Http\Controllers\ServicioTecnico;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class STClienteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Cliente::query();

            // Filtros
            if ($request->filled('busqueda')) {
                $kw = $request->busqueda;
                $query->where(function ($q) use ($kw) {
                    $q->where('nombre_contacto', 'like', "%{$kw}%")
                      ->orWhere('numero_identificacion', 'like', "%{$kw}%")
                      ->orWhere('email', 'like', "%{$kw}%")
                      ->orWhere('razon_social', 'like', "%{$kw}%")
                      ->orWhere('celular', 'like', "%{$kw}%");
                });
            }
            if ($request->filled('tipo_cliente')) {
                $query->where('tipo_cliente', $request->tipo_cliente);
            }
            if ($request->filled('tipo_documento')) {
                $query->where('tipo_documento', $request->tipo_documento);
            }
            if ($request->filled('activo')) {
                $query->where('activo', $request->activo);
            }

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
                    $tipo = $cliente->tipo_cliente ?: 'particular';
                    $badge = $tipo === 'empresa' ? 'primary' : 'secondary';
                    return '<span class="badge bg-' . $badge . '">' . ucfirst($tipo) . '</span>';
                })
                ->addColumn('estado_badge', function ($cliente) {
                    $badge = $cliente->activo ? 'success' : 'danger';
                    $text = $cliente->activo ? 'Activo' : 'Inactivo';
                    return '<span class="badge bg-' . $badge . '">' . $text . '</span>';
                })
                ->addColumn('equipos_count', function ($cliente) {
                    return $cliente->equipos()->count();
                })
                // Aliases para mantener compatibilidad con la tabla anterior
                ->addColumn('numero_documento', function ($c) { return $c->numero_identificacion; })
                ->addColumn('nombre_completo', function ($c) { return $c->nombre_contacto; })
                ->filterColumn('numero_documento', function ($q, $kw) {
                    $q->where('numero_identificacion', 'like', "%{$kw}%");
                })
                ->filterColumn('nombre_completo', function ($q, $kw) {
                    $q->where('nombre_contacto', 'like', "%{$kw}%");
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
        $validated = $this->validateForm($request);
        $data = $this->mapFormToColumns($validated);
        $data['activo'] = $request->boolean('activo', true);

        Cliente::create($data);

        return redirect()->route('st.clientes.index')
            ->with('success', 'Cliente registrado exitosamente');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['equipos', 'ordenesServicio.tecnico']);
        return view('servicio-tecnico.clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('servicio-tecnico.clientes.form', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $this->validateForm($request, $cliente->id);
        $data = $this->mapFormToColumns($validated);
        $data['activo'] = $request->boolean('activo', $cliente->activo);

        $cliente->update($data);

        return redirect()->route('st.clientes.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy(Cliente $cliente)
    {
        try {
            $cliente->activo = false;
            $cliente->save();

            return response()->json(['success' => true, 'message' => 'Cliente desactivado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al desactivar el cliente'], 500);
        }
    }

    private function validateForm(Request $request, ?int $id = null): array
    {
        $uniqueRule = 'unique:clientes,numero_identificacion' . ($id ? ',' . $id : '');

        return $request->validate([
            'tipo_documento'   => 'nullable|string|max:20',
            'numero_documento' => 'required|string|max:50|' . $uniqueRule,
            'nombre_completo'  => 'required|string|max:255',
            'razon_social'     => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'telefono'         => 'nullable|string|max:50',
            'celular'          => 'nullable|string|max:50',
            'direccion'        => 'nullable|string',
            'ciudad'           => 'nullable|string|max:255',
            'departamento'     => 'nullable|string|max:255',
            'tipo_cliente'     => 'required|in:particular,empresa',
            'observaciones'    => 'nullable|string',
            'activo'           => 'nullable',
        ]);
    }

    /**
     * Mapea los nombres del formulario (heredados de st_clientes) a las columnas
     * unificadas en clientes.
     */
    private function mapFormToColumns(array $v): array
    {
        return [
            'numero_identificacion' => $v['numero_documento'],
            'tipo_documento'        => $v['tipo_documento'] ?? null,
            'nombre_contacto'       => $v['nombre_completo'],
            'razon_social'          => $v['razon_social'] ?? null,
            'email'                 => $v['email'] ?? null,
            'telefono'              => $v['telefono'] ?? null,
            'celular'               => $v['celular'] ?? null,
            'direccion'             => $v['direccion'] ?? null,
            'ciudad_texto'          => $v['ciudad'] ?? null,
            'departamento_texto'    => $v['departamento'] ?? null,
            'tipo_cliente'          => $v['tipo_cliente'],
            'observaciones'         => $v['observaciones'] ?? null,
        ];
    }
}
