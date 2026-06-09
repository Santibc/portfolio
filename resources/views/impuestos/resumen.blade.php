@extends('layouts.app')

@section('title', 'Resumen de Impuestos')

@php
    function eurImp($n) { return number_format((float) $n, 2, ',', '.') . ' €'; }
@endphp

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Resumen de Impuestos</h1>
            <p class="text-muted mb-0">IVA, IRPF y Seguridad Social del periodo seleccionado</p>
        </div>
    </div>

    {{-- Filtros de periodo --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('impuestos.resumen') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Año</label>
                    <select name="anio" class="form-select">
                        @foreach($anios as $a)
                            <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trimestre</label>
                    <select name="trimestre" class="form-select">
                        <option value="">Año completo</option>
                        <option value="1" {{ $trimestre == '1' ? 'selected' : '' }}>1T (Ene-Mar)</option>
                        <option value="2" {{ $trimestre == '2' ? 'selected' : '' }}>2T (Abr-Jun)</option>
                        <option value="3" {{ $trimestre == '3' ? 'selected' : '' }}>3T (Jul-Sep)</option>
                        <option value="4" {{ $trimestre == '4' ? 'selected' : '' }}>4T (Oct-Dic)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Ver</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Resumen rápido --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">IVA repercutido (ventas)</small>
                    <h4 class="mb-0 text-success">{{ eurImp($totalIvaRepercutido) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">IVA soportado (compras)</small>
                    <h4 class="mb-0 text-danger">{{ eurImp($totalIvaSoportado) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 {{ $ivaLiquidar >= 0 ? 'border-start border-4 border-warning' : 'border-start border-4 border-info' }}">
                <div class="card-body">
                    <small class="text-muted">IVA a liquidar</small>
                    <h4 class="mb-0 {{ $ivaLiquidar >= 0 ? 'text-warning' : 'text-info' }}">{{ eurImp($ivaLiquidar) }}</h4>
                    <small class="text-muted">{{ $ivaLiquidar >= 0 ? 'A ingresar a Hacienda' : 'A compensar / devolver' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">IRPF practicado (gastos)</small>
                    <h4 class="mb-0 text-primary">{{ eurImp($irpfGastos) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- IVA detalle --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-percent me-2"></i>IVA por tipo</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-success">IVA repercutido (ingresos / ventas)</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm">
                            <thead class="table-light"><tr><th>Tipo IVA</th><th class="text-end">Base imponible</th><th class="text-end">Cuota IVA</th></tr></thead>
                            <tbody>
                                @forelse($ivaRepercutido as $pct => $d)
                                    <tr><td>{{ $pct }}%</td><td class="text-end">{{ eurImp($d['base']) }}</td><td class="text-end">{{ eurImp($d['iva']) }}</td></tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Sin ingresos en el periodo</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot><tr class="fw-bold"><td>Total</td><td class="text-end">{{ eurImp($baseIngresos) }}</td><td class="text-end text-success">{{ eurImp($totalIvaRepercutido) }}</td></tr></tfoot>
                        </table>
                    </div>

                    <h6 class="text-danger">IVA soportado (gastos / compras)</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light"><tr><th>Tipo IVA</th><th class="text-end">Base imponible</th><th class="text-end">Cuota IVA</th></tr></thead>
                            <tbody>
                                @forelse($ivaSoportado as $pct => $d)
                                    <tr><td>{{ $pct }}%</td><td class="text-end">{{ eurImp($d['base']) }}</td><td class="text-end">{{ eurImp($d['iva']) }}</td></tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Sin gastos en el periodo</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot><tr class="fw-bold"><td>Total</td><td class="text-end">{{ eurImp($baseGastos) }}</td><td class="text-end text-danger">{{ eurImp($totalIvaSoportado) }}</td></tr></tfoot>
                        </table>
                    </div>

                    <div class="bg-light rounded p-3 mt-3 d-flex justify-content-between fw-bold fs-5">
                        <span>RESULTADO IVA (repercutido − soportado):</span>
                        <span class="{{ $ivaLiquidar >= 0 ? 'text-warning' : 'text-info' }}">{{ eurImp($ivaLiquidar) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- IRPF + SS --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>IRPF</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>IRPF nóminas (trabajadores)<br><small class="text-muted">(retenido en sueldos, a Hacienda)</small></span>
                        <span class="fw-bold text-primary">{{ eurImp($irpfNominas) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>IRPF practicado a proveedores<br><small class="text-muted">(a ingresar a Hacienda)</small></span>
                        <span class="fw-bold text-primary">{{ eurImp($irpfGastos) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Retención soportada en ventas<br><small class="text-muted">(que te practican los clientes)</small></span>
                        <span class="fw-bold">{{ eurImp($retencionIngresos) }}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Seguridad Social</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Total Seguridad Social<br><small class="text-muted">(empresa + trabajador)</small></span>
                        <span class="fw-bold">{{ eurImp($seguridadSocial) }}</span>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i>Proviene de las <a href="{{ route('nominas.resumen') }}">nóminas</a> del periodo.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
