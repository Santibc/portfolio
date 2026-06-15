@extends('layouts.app')

@section('title', 'Resumen de Nóminas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Resumen de Nóminas</h1>
            <p class="text-muted mb-0">Coste de personal mensual — se refleja en gastos e impuestos</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNomina">
            <i class="bi bi-plus-lg me-2"></i>Subir nómina
        </button>
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

    {{-- Modal: alta centralizada de nómina --}}
    <div class="modal fade" id="modalNomina" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('nominas.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Subir nómina</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Trabajador <span class="text-danger">*</span></label>
                                <select name="trabajador_id" class="form-select" required>
                                    <option value="">Selecciona un trabajador...</option>
                                    @foreach($trabajadores as $t)
                                        <option value="{{ $t->id }}" {{ old('trabajador_id') == $t->id ? 'selected' : '' }}>{{ $t->apellidos }}, {{ $t->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mes <span class="text-danger">*</span></label>
                                <select name="mes" class="form-select" required>
                                    <option value="">Mes...</option>
                                    @foreach(\App\Models\Nomina::MESES as $num => $nombre)
                                        <option value="{{ $num }}" {{ old('mes') == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Año <span class="text-danger">*</span></label>
                                <select name="anio" class="form-select" required>
                                    @foreach($anios as $a)
                                        <option value="{{ $a }}" {{ old('anio', $anio) == $a ? 'selected' : '' }}>{{ $a }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Salario bruto <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="salario_bruto" id="n_bruto" class="form-control" value="{{ old('salario_bruto') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">SS empresa</label>
                                <input type="number" step="0.01" min="0" name="ss_empresa" class="form-control" value="{{ old('ss_empresa') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">SS trabajador</label>
                                <input type="number" step="0.01" min="0" name="ss_trabajador" id="n_sstrab" class="form-control" value="{{ old('ss_trabajador') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">IRPF</label>
                                <input type="number" step="0.01" min="0" name="irpf" id="n_irpf" class="form-control" value="{{ old('irpf') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Líquido a percibir (automático)</label>
                                <input type="text" id="n_liquido" class="form-control bg-light fw-semibold" readonly value="0,00 €">
                                <small class="text-muted">Bruto − SS trabajador − IRPF</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Documento (PDF)</label>
                                <input type="file" name="documento" class="form-control" accept="application/pdf">
                                <small class="text-muted">Opcional. Máximo 5MB.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea name="notas" class="form-control" rows="2">{{ old('notas') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-2"></i>Guardar nómina</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const bruto = document.getElementById('n_bruto');
        const ssTrab = document.getElementById('n_sstrab');
        const irpf = document.getElementById('n_irpf');
        const liquido = document.getElementById('n_liquido');
        function calc() {
            const v = (parseFloat(bruto.value) || 0) - (parseFloat(ssTrab.value) || 0) - (parseFloat(irpf.value) || 0);
            liquido.value = new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(v);
        }
        [bruto, ssTrab, irpf].forEach(el => el && el.addEventListener('input', calc));
        @if($errors->any() || session('error'))
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('modalNomina')).show();
            calc();
        });
        @endif
    })();
</script>
@endpush
@endsection
