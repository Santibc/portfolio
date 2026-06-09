@extends('layouts.app')

@section('title', 'Resumen de Nóminas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Resumen de Nóminas</h1>
            <p class="text-muted mb-0">Coste de personal mensual — se refleja en gastos e impuestos</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('nominas.resumen') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Año</label>
                    <select name="anio" class="form-select" onchange="this.form.submit()">
                        @foreach($anios as $a)<option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>@endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Coste empresa (año)</small><h4 class="mb-0 text-danger">{{ number_format($totales['coste_empresa'], 2, ',', '.') }} €</h4><small class="text-muted">Bruto + SS empresa</small></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Seguridad Social total</small><h4 class="mb-0">{{ number_format($totales['ss_empresa'] + $totales['ss_trabajador'], 2, ',', '.') }} €</h4><small class="text-muted">Empresa + trabajador</small></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">IRPF total</small><h4 class="mb-0 text-primary">{{ number_format($totales['irpf'], 2, ',', '.') }} €</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Líquido pagado</small><h4 class="mb-0">{{ number_format($totales['liquido'], 2, ',', '.') }} €</h4></div></div></div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3"><h5 class="mb-0">Desglose mensual {{ $anio }}</h5></div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Mes</th><th class="text-center">Nóminas</th><th class="text-end">Bruto</th><th class="text-end">SS empresa</th><th class="text-end">SS trabajador</th><th class="text-end">IRPF</th><th class="text-end">Líquido</th><th class="text-end">Coste empresa</th></tr>
                </thead>
                <tbody>
                    @forelse($porMes as $m)
                    <tr>
                        <td class="fw-medium">{{ $m['nombre'] }}</td>
                        <td class="text-center">{{ $m['count'] }}</td>
                        <td class="text-end">{{ number_format($m['bruto'], 2, ',', '.') }} €</td>
                        <td class="text-end">{{ number_format($m['ss_empresa'], 2, ',', '.') }} €</td>
                        <td class="text-end">{{ number_format($m['ss_trabajador'], 2, ',', '.') }} €</td>
                        <td class="text-end">{{ number_format($m['irpf'], 2, ',', '.') }} €</td>
                        <td class="text-end">{{ number_format($m['liquido'], 2, ',', '.') }} €</td>
                        <td class="text-end fw-bold text-danger">{{ number_format($m['coste_empresa'], 2, ',', '.') }} €</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay nóminas registradas en {{ $anio }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info mt-3 small">
        <i class="bi bi-info-circle me-1"></i>La <strong>Seguridad Social</strong> y el <strong>IRPF</strong> de las nóminas se reflejan en el
        <a href="{{ route('impuestos.resumen') }}">Resumen de Impuestos</a>. El <strong>"coste empresa"</strong> es el gasto de personal de cada mes.
    </div>
</div>
@endsection
