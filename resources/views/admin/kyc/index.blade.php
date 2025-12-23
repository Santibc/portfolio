<x-app-layout>
    <x-agromarket.page-header
        title="Revision KYC"
        subtitle="Verificacion de identidad de inversionistas"
    />

    <div class="dashboard-row">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-check"></i> KYC Pendientes de Revision</h3>
                    <span class="pending-count">{{ $pendientes->total() }} usuario(s) en revision</span>
                </div>
                <div class="card-body">
                    @if($pendientes->count() > 0)
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Documentos</th>
                                        <th>Fecha de Envio</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendientes as $usuario)
                                        <tr>
                                            <td><strong>#{{ $usuario->id }}</strong></td>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar">
                                                        {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                                    </div>
                                                    <div class="user-details">
                                                        <div class="user-name">{{ $usuario->name }}</div>
                                                        @if($usuario->tipo_documento)
                                                            <div class="user-document">{{ $usuario->tipo_documento }}: {{ $usuario->documento_identidad }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $usuario->email }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-file-alt"></i> {{ $usuario->documentosKyc->count() }} documento(s)
                                                </span>
                                            </td>
                                            <td>{{ $usuario->updated_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.kyc.show', $usuario) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Revisar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginacion --}}
                        @if($pendientes->hasPages())
                        <div class="pagination-container mt-4">
                            {{ $pendientes->links() }}
                        </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4>No hay KYC pendientes de revision</h4>
                            <p>Todos los KYC han sido revisados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .dashboard-row {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .dashboard-col-12 {
            flex: 1;
            width: 100%;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pending-count {
            background: #4A7C59;
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .card-body {
            padding: 1.5rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #f9fafb;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            font-size: 0.85rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .data-table tr:hover {
            background: #f9fafb;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4A7C59 0%, #6B9B7A 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .user-name {
            font-weight: 600;
            color: #1f2937;
        }

        .user-document {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 1rem;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #28a745;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
        }

        .empty-state h4 {
            color: #1f2937;
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
        }

        .empty-state p {
            color: #6b7280;
            margin: 0;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
    </style>
    @endpush
</x-app-layout>
