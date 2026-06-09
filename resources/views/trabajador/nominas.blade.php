@extends('layouts.app')

@section('title', 'Mis Nóminas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Mis Nóminas</h1>
            <p class="text-muted mb-0">Consulta y descarga tus nóminas</p>
        </div>
        <a href="{{ route('trabajador.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Mi Portal</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Periodo</th><th class="text-end">Líquido percibido</th><th class="text-center">Nómina</th></tr>
                </thead>
                <tbody>
                    @forelse($nominas as $nom)
                    <tr>
                        <td class="fw-medium">{{ $nom->mes_nombre }} {{ $nom->anio }}</td>
                        <td class="text-end fw-bold">{{ number_format($nom->liquido, 2, ',', '.') }} €</td>
                        <td class="text-center">
                            @if($nom->documento_path)
                                <a href="{{ route('nominas.download', $nom) }}" class="btn btn-sm btn-primary"><i class="bi bi-download me-1"></i>Descargar PDF</a>
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Aún no tienes nóminas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
