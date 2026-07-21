<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class MetaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('admin')) {
                abort(403, 'No autorizado.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Meta::with(['vendedor:id,name,email'])
                ->select('metas.*');

            if ($request->filled('anio')) {
                $query->where('anio', (int) $request->input('anio'));
            }
            if ($request->filled('mes')) {
                $query->where('mes', (int) $request->input('mes'));
            }
            if ($request->filled('user_id')) {
                $query->where('user_id', (int) $request->input('user_id'));
            }

            return DataTables::of($query)
                ->addColumn('vendedor', fn($m) => $m->vendedor?->name)
                ->addColumn('periodo', fn($m) => sprintf('%02d/%d', $m->mes, $m->anio))
                ->addColumn('monto_fmt', fn($m) => '$ ' . number_format((float) $m->monto, 0, ',', '.'))
                ->addColumn('vendido', function ($m) {
                    return (float) Venta::delVendedor($m->user_id)
                        ->delMes($m->anio, $m->mes)
                        ->sum('monto');
                })
                ->addColumn('cumplimiento', function ($m) {
                    $vendido = (float) Venta::delVendedor($m->user_id)
                        ->delMes($m->anio, $m->mes)
                        ->sum('monto');
                    $pct = $m->monto > 0 ? min(999, ($vendido / $m->monto) * 100) : 0;
                    return round($pct, 1) . ' %';
                })
                ->addColumn('action', function ($m) {
                    $url = route('metas.form', $m->id);
                    return <<<HTML
<div class="d-flex justify-content-center gap-1">
  <a href="{$url}" class="btn btn-outline-info btn-sm" title="Editar">
    <i class="bi bi-pencil"></i>
  </a>
</div>
HTML;
                })
                ->filterColumn('vendedor', function ($q, $keyword) {
                    $q->whereHas('vendedor', fn($qq) => $qq->where('name', 'like', "%{$keyword}%"));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $vendedores = User::role('vendedor')->orderBy('name')->pluck('name', 'id');
        $anioActual = (int) now()->year;
        $mesActual = (int) now()->month;

        return view('metas.metas_index', compact('vendedores', 'anioActual', 'mesActual'));
    }

    public function form(Meta $meta = null)
    {
        $meta = $meta ?? new Meta();
        $vendedores = User::role('vendedor')->orderBy('name')->pluck('name', 'id');

        return view('metas.metas_form', compact('meta', 'vendedores'));
    }

    public function guardar(Request $request)
    {
        $esMasivo = !$request->id && $request->input('user_id') === 'all';

        $reglasBase = [
            'anio' => ['required', 'integer', 'min:2020', 'max:2100'],
            'mes' => ['required', 'integer', 'min:1', 'max:12'],
            'monto' => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];

        $mensajes = [
            'required' => 'Este campo es obligatorio.',
            'exists' => 'El vendedor seleccionado no es válido.',
            'min' => 'El valor debe ser mayor o igual a :min.',
            'max' => 'El valor debe ser menor o igual a :max.',
        ];

        if ($esMasivo) {
            $data = $request->validate($reglasBase, $mensajes);

            $vendedores = User::role('vendedor')->pluck('id');
            $nuevas = 0;
            $actualizadas = 0;

            foreach ($vendedores as $userId) {
                $existente = Meta::where('user_id', $userId)
                    ->where('anio', $data['anio'])
                    ->where('mes', $data['mes'])
                    ->first();

                if ($existente) {
                    $existente->update([
                        'monto' => $data['monto'],
                        'observaciones' => $data['observaciones'] ?? $existente->observaciones,
                    ]);
                    $actualizadas++;
                } else {
                    Meta::create(array_merge($data, [
                        'user_id' => $userId,
                        'created_by' => auth()->id(),
                    ]));
                    $nuevas++;
                }
            }

            return redirect()->route('metas')
                ->with('success', "Meta aplicada a {$vendedores->count()} vendedor(es): {$nuevas} nueva(s), {$actualizadas} actualizada(s).");
        }

        $meta = $request->id
            ? Meta::findOrFail($request->id)
            : new Meta();

        $data = $request->validate(
            array_merge(['user_id' => ['required', 'integer', 'exists:users,id']], $reglasBase),
            $mensajes
        );

        $existe = Meta::where('user_id', $data['user_id'])
            ->where('anio', $data['anio'])
            ->where('mes', $data['mes'])
            ->when($meta->exists, fn($q) => $q->where('id', '!=', $meta->id))
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors(['user_id' => 'Ya existe una meta para este vendedor en este periodo.']);
        }

        if (!$meta->exists) {
            $data['created_by'] = auth()->id();
        }

        $meta->fill($data)->save();

        return redirect()->route('metas')
            ->with('success', 'Meta guardada correctamente.');
    }
}
