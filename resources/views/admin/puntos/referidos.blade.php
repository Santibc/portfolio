@extends('layouts.app')

@section('title', 'Reporte de Referidos')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-people"></i> Reporte de Referidos
            </h1>
            <p class="text-muted mb-0">Usuarios que han referido a otros clientes</p>
        </div>
        <a href="{{ route('admin.puntos.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table id="referidosTable" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th width="120">Código</th>
                        <th width="100">Referidos</th>
                        <th width="120">Puntos Ganados</th>
                        <th>Lista de Referidos</th>
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
    $('#referidosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.puntos.referidos") }}',
        columns: [
            { data: 'usuario', name: 'name' },
            { data: 'codigo', name: 'codigo_referido', orderable: false },
            { data: 'total_referidos', name: 'referidos_count', orderable: true },
            { data: 'puntos_ganados', name: 'puntos_ganados', orderable: false },
            { data: 'lista_referidos', name: 'lista_referidos', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        pageLength: 25
    });
});
</script>
@endpush
@endsection
