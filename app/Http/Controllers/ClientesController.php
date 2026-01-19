<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\User;
use App\Models\ListaPrecio;
use App\Models\Sucursal;
use App\Models\DocumentoCliente;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Departamento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Cliente::with(['vendedor', 'listaPrecio',
                        'pais',        // <-- relación país
            'ciudad',      // <-- relación ciudad
            ])->select('clientes.*');

            // Filtrar por vendedor si no es admin ni inventarios ni facturacion
            $user = auth()->user();
            if ($user->hasRole('vendedor')) {
                $query->where('vendedor_id', $user->id);
            }

            return DataTables::of($query)
                        ->addColumn('pais', fn($c) => $c->pais?->nombre)
            ->addColumn('ciudad', fn($c) => $c->ciudad?->nombre)
                ->addColumn('vendedor', fn($c) => $c->vendedor?->name)
                ->addColumn('lista_precio', fn($c) => $c->listaPrecio?->nombre)
                ->addColumn('activo', fn($c) => $c->activo ? 'Sí' : 'No')
                ->addColumn('action', function($c) {
                    // Solo admin e inventarios pueden editar
                    if (auth()->user()->hasRole(['admin', 'inventarios'])) {
                        $url = route('clientes.form', $c->id);
                        return <<<HTML
<div class="d-flex justify-content-center">
  <a href="{$url}" class="btn btn-outline-info btn-sm" title="Editar">
    <i class="bi bi-pencil"></i>
  </a>
</div>
HTML;
                    }
                    // Vendedor solo puede ver
                    return '<span class="text-muted">-</span>';
                })
                ->filterColumn('pais', function($query, $keyword) {
                    $query->whereHas('pais', function($q) use ($keyword) {
                        $q->where('nombre', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('ciudad', function($query, $keyword) {
                    $query->whereHas('ciudad', function($q) use ($keyword) {
                        $q->where('nombre', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('vendedor', function($query, $keyword) {
                    $query->whereHas('vendedor', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('lista_precio', function($query, $keyword) {
                    $query->whereHas('listaPrecio', function($q) use ($keyword) {
                        $q->where('nombre', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('clientes.clientes_index');
    }

    public function form(Cliente $cliente = null)
    {
        $cliente = $cliente ?? new Cliente(['tipo_cliente' => 'natural']);
        $vendedores = User::role('vendedor')->pluck('name', 'id');
        $listas = ListaPrecio::activas()->pluck('nombre', 'id');
        $departamentos = Departamento::orderBy('nombre')->pluck('nombre', 'id');
        $pais_id = 1;

        // Datos adicionales para el formulario mejorado
        $tiposCliente = Cliente::tiposCliente();
        $tiposDocumento = DocumentoCliente::tiposDocumento();

        // Cargar sucursales y documentos si es edición
        $sucursales = $cliente->exists ? $cliente->sucursales()->with('ciudad')->get() : collect();
        $documentos = $cliente->exists ? $cliente->documentos()->with('subidoPor')->get() : collect();

        return view('clientes.clientes_form', compact(
            'cliente', 'departamentos', 'vendedores', 'listas', 'pais_id',
            'tiposCliente', 'tiposDocumento', 'sucursales', 'documentos'
        ));
    }

    public function guardar(Request $request)
    {
        $cliente = $request->id
                 ? Cliente::findOrFail($request->id)
                 : new Cliente();

        $rules = [
            'numero_identificacion' => [
                'required', 'string', 'max:255',
                Rule::unique('clientes')->ignore($cliente->id)
            ],
            'tipo_cliente' => ['required', 'in:natural,juridica'],
            'nombre_contacto' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('clientes')->ignore($cliente->id)
            ],
            'telefono' => ['nullable', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'pais_id' => ['required', 'exists:paises,id'],
            'departamento_id' => ['required', 'exists:departamentos,id'],
            'ciudad_id' => ['required', 'exists:ciudades,id'],
            'vendedor_id' => ['required', 'exists:users,id'],
            'lista_precio_id' => ['required', 'exists:listas_precios,id'],
            'valor_flete' => ['nullable', 'numeric', 'min:0'],
            'aplica_flete' => ['nullable', 'boolean'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];

        // Campos adicionales para persona jurídica
        if ($request->tipo_cliente === 'juridica') {
            $rules['razon_social'] = ['required', 'string', 'max:255'];
            $rules['nit'] = ['required', 'string', 'max:50'];
            $rules['representante_legal'] = ['nullable', 'string', 'max:255'];
        }

        $messages = [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Debe ser un correo válido.',
            'max' => 'No debe superar los :max caracteres.',
            'unique' => 'Ya existe un registro con este valor.',
            'exists' => 'El valor seleccionado no es válido.',
            'numeric' => 'Debe ser un valor numérico.',
            'min' => 'El valor mínimo es :min.',
        ];

        $data = $request->validate($rules, $messages);

        // Manejar checkbox de flete
        $data['aplica_flete'] = $request->has('aplica_flete');

        // Limpiar campos de persona jurídica si es natural
        if ($data['tipo_cliente'] === 'natural') {
            $data['razon_social'] = null;
            $data['nit'] = null;
            $data['representante_legal'] = null;
        }

        // Proteger campos sensibles: solo admin puede cambiar vendedor y lista de precios
        // en clientes existentes
        if ($cliente->exists && !auth()->user()->hasRole('admin')) {
            // Restaurar valores originales que no puede modificar
            $data['vendedor_id'] = $cliente->vendedor_id;
            $data['lista_precio_id'] = $cliente->lista_precio_id;
        }

        $cliente->fill($data)->save();

        return redirect()->route('clientes')
                         ->with('success', 'Cliente guardado correctamente.');
    }

    /**
     * Guardar una sucursal del cliente
     */
    public function guardarSucursal(Request $request, Cliente $cliente)
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:500'],
            'ciudad_id' => ['nullable', 'exists:ciudades,id'],
            'telefono' => ['nullable', 'string', 'max:100'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'es_principal' => ['nullable', 'boolean'],
        ];

        $data = $request->validate($rules);
        $data['cliente_id'] = $cliente->id;
        $data['es_principal'] = $request->has('es_principal');

        if ($request->sucursal_id) {
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            $sucursal->update($data);
        } else {
            $sucursal = Sucursal::create($data);
        }

        // Si es principal, desmarcar las demás
        if ($data['es_principal']) {
            $sucursal->marcarComoPrincipal();
        }

        return response()->json([
            'success' => true,
            'message' => 'Sucursal guardada correctamente.',
            'sucursal' => $sucursal->load('ciudad')
        ]);
    }

    /**
     * Eliminar una sucursal
     */
    public function eliminarSucursal(Sucursal $sucursal)
    {
        $sucursal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sucursal eliminada correctamente.'
        ]);
    }

    /**
     * Subir documento del cliente
     */
    public function subirDocumento(Request $request, Cliente $cliente)
    {
        $request->validate([
            'documento' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // Max 10MB
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', 'string', 'max:50'],
        ]);

        $archivo = $request->file('documento');
        $ruta = $archivo->store("documentos/clientes/{$cliente->id}", 'public');

        $documento = DocumentoCliente::create([
            'cliente_id' => $cliente->id,
            'nombre' => $request->nombre,
            'archivo' => $ruta,
            'tipo' => $request->tipo,
            'mime_type' => $archivo->getMimeType(),
            'tamano' => $archivo->getSize(),
            'subido_por' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento subido correctamente.',
            'documento' => $documento->load('subidoPor')
        ]);
    }

    /**
     * Eliminar documento del cliente
     */
    public function eliminarDocumento(DocumentoCliente $documento)
    {
        // Eliminar archivo físico
        $documento->eliminarArchivo();

        // Eliminar registro
        $documento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Documento eliminado correctamente.'
        ]);
    }

    /**
     * Descargar documento del cliente
     */
    public function descargarDocumento(DocumentoCliente $documento)
    {
        if (!Storage::disk('public')->exists($documento->archivo)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('public')->download($documento->archivo, $documento->nombre);
    }
}
