<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\Ubicacion;
use App\Models\User;
use App\Models\ConfiguracionPdv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class CajasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Caja::with('ubicacion', 'cajeroAsignado');

            return DataTables::of($query)
                ->addColumn('ubicacion_nombre', fn($caja) => $caja->ubicacion->nombre ?? '-')
                ->addColumn('cajero_nombre', fn($caja) => $caja->cajeroAsignado->name ?? 'Sin asignar')
                ->addColumn('estado_badge', fn($caja) => $caja->estado_badge)
                ->addColumn('activo_badge', fn($caja) => $caja->activo
                    ? '<span class="badge bg-success">Activa</span>'
                    : '<span class="badge bg-secondary">Inactiva</span>')
                ->addColumn('action', function ($caja) {
                    $btn = '<a href="' . route('pdv.cajas.form', $caja->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="bi bi-pencil"></i></a>';
                    $btn .= '<button class="btn btn-sm btn-outline-' . ($caja->activo ? 'warning' : 'success') . ' me-1" onclick="toggleEstado(' . $caja->id . ')" title="' . ($caja->activo ? 'Desactivar' : 'Activar') . '"><i class="bi bi-' . ($caja->activo ? 'pause' : 'play') . '"></i></button>';
                    return $btn;
                })
                ->rawColumns(['estado_badge', 'activo_badge', 'action'])
                ->make(true);
        }

        return view('pdv.cajas.index');
    }

    public function form($id = null)
    {
        $caja = $id ? Caja::findOrFail($id) : null;
        $ubicaciones = Ubicacion::activas()->get();
        $usuarios = User::role(['cajero_principal', 'admin'])->get();

        return view('pdv.cajas.form', compact('caja', 'ubicaciones', 'usuarios'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:cajas,codigo,' . $request->id,
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'cajero_asignado_id' => 'nullable|exists:users,id',
        ]);

        $caja = Caja::updateOrCreate(
            ['id' => $request->id],
            $request->only(['nombre', 'codigo', 'ubicacion_id', 'cajero_asignado_id'])
        );

        return redirect()->route('pdv.cajas.index')
            ->with('success', $caja->wasRecentlyCreated ? 'Caja creada exitosamente' : 'Caja actualizada exitosamente');
    }

    public function toggleActivo($id)
    {
        $caja = Caja::findOrFail($id);

        if ($caja->estaAbierta()) {
            return response()->json(['error' => 'No se puede desactivar una caja abierta'], 422);
        }

        $caja->update(['activo' => !$caja->activo]);

        return response()->json(['success' => true, 'activo' => $caja->activo]);
    }

    public function configuracion()
    {
        $configuraciones = ConfiguracionPdv::all();
        $usuariosPin = User::role(['admin', 'cajero_principal'])
            ->get(['id', 'name', 'email', 'pin_pdv']);

        return view('pdv.cajas.configuracion', compact('configuraciones', 'usuariosPin'));
    }

    public function guardarConfiguracion(Request $request)
    {
        foreach ($request->except('_token') as $clave => $valor) {
            ConfiguracionPdv::establecer($clave, $valor);
        }

        return redirect()->route('pdv.cajas.configuracion')
            ->with('success', 'Configuración guardada exitosamente');
    }

    public function guardarPin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pin' => 'required|digits:4',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update(['pin_pdv' => Hash::make($request->pin)]);

        return response()->json(['success' => true, 'message' => 'PIN actualizado para ' . $user->name]);
    }

    public function eliminarPin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        User::where('id', $request->user_id)->update(['pin_pdv' => null]);

        return response()->json(['success' => true, 'message' => 'PIN eliminado']);
    }
}
