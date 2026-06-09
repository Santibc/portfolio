@extends('layouts.app')

@section('title', 'Deuda por trabajador')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Deuda por trabajador</h1>
            <p class="text-muted mb-0">Bonos y primas pendientes de pago a cada trabajador</p>
        </div>
        <a href="{{ route('trabajadores.bonos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Volver a bonos</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Total adeudado</small>
                    <h3 class="mb-0 text-warning">{{ number_format($totalDeuda, 2, ',', '.') }} €</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">Trabajadores con deuda</small>
                    <h3 class="mb-0">{{ $trabajadores->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Trabajador</th>
                        <th class="text-end">Bonos pendientes</th>
                        <th class="text-end">Primas pendientes</th>
                        <th class="text-end">Total adeudado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trabajadores as $t)
                    <tr>
                        <td>{{ $t->apellidos }}, {{ $t->nombre }}</td>
                        <td class="text-end">{{ number_format($t->deuda_bonos, 2, ',', '.') }} €</td>
                        <td class="text-end">{{ number_format($t->deuda_primas, 2, ',', '.') }} €</td>
                        <td class="text-end fw-bold text-warning">{{ number_format($t->deuda_total, 2, ',', '.') }} €</td>
                        <td class="text-end">
                            <a href="{{ route('trabajadores.bonos.index', ['trabajador_id' => $t->id, 'pagado' => 'no']) }}" class="btn btn-sm btn-outline-primary">Ver detalle</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay deuda pendiente con ningún trabajador. 🎉</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
