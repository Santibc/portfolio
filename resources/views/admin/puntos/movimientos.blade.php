@extends('layouts.app')

@section('title', 'Movimientos de Puntos')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-list-ul"></i> Movimientos de Puntos
            </h1>
            <p class="text-muted mb-0">Historial completo de todos los movimientos</p>
        </div>
        <a href="{{ route('admin.puntos.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="movimientosTable" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th width="100">Puntos</th>
                        <th width="120">Tipo</th>
                        <th>Concepto</th>
                        <th width="140">Fecha</th>
                        <th width="140">Expiración</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#movimientosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.puntos.movimientos") }}',
        columns: [
            { data: 'usuario', name: 'users.name' },
            { data: 'puntos_formateado', name: 'puntos', orderable: true, searchable: false },
            { data: 'tipo_badge', name: 'tipo', orderable: true },
            { data: 'concepto', name: 'concepto' },
            { data: 'fecha', name: 'created_at', orderable: true },
            { data: 'expiracion', name: 'fecha_expiracion', orderable: true }
        ],
        order: [[4, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        pageLength: 25
    });
});
</script>
@endpush
@endsection
