@extends('layouts.app')

@section('titulo', 'Dashboard Analítico')

@section('css')
<style>
    .stat-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .stat-card.primary {
        background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
        color: white;
    }
    .stat-card.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    .stat-card.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    .stat-card.info {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: rgba(255,255,255,0.2);
    }
    .stat-card:not(.primary):not(.success):not(.warning):not(.info) .stat-icon {
        background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
        color: white;
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }
    .stat-change {
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .stat-change.positive { color: #10b981; }
    .stat-change.negative { color: #ef4444; }
    .stat-card.primary .stat-change.positive,
    .stat-card.success .stat-change.positive,
    .stat-card.warning .stat-change.positive,
    .stat-card.info .stat-change.positive { color: #d1fae5; }
    .stat-card.primary .stat-change.negative,
    .stat-card.success .stat-change.negative,
    .stat-card.warning .stat-change.negative,
    .stat-card.info .stat-change.negative { color: #fecaca; }

    .chart-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 1.5rem;
        height: 100%;
    }
    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .chart-title i {
        color: #ec4899;
    }

    .product-rank {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
    }
    .product-rank.gold { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; }
    .product-rank.silver { background: linear-gradient(135deg, #9ca3af, #6b7280); color: white; }
    .product-rank.bronze { background: linear-gradient(135deg, #d97706, #b45309); color: white; }
    .product-rank.default { background: #f3f4f6; color: #6b7280; }

    .estado-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .estado-pendiente { background: #fef3c7; color: #92400e; }
    .estado-pagada { background: #d1fae5; color: #065f46; }
    .estado-enviada { background: #dbeafe; color: #1e40af; }
    .estado-entregada { background: #e0e7ff; color: #3730a3; }
    .estado-cancelada { background: #fee2e2; color: #991b1b; }

    .alert-card {
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .alert-card.warning {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
    }
    .alert-card.danger {
        background: #fee2e2;
        border-left: 4px solid #ef4444;
    }
    .alert-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .alert-card.warning .alert-icon { background: #f59e0b; color: white; }
    .alert-card.danger .alert-icon { background: #ef4444; color: white; }

    .hora-bar {
        height: 8px;
        border-radius: 4px;
        background: #e5e7eb;
        overflow: hidden;
    }
    .hora-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #ec4899, #8b5cf6);
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    .table-productos th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Dashboard Analítico</h1>
            <p class="text-muted mb-0">Resumen de ventas y métricas de tu negocio</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Imprimir
            </button>
        </div>
    </div>

    {{-- Alertas de Entregas --}}
    @if($entregasPendientesHoy > 0 || $entregasAtrasadas > 0)
    <div class="row mb-4">
        @if($entregasAtrasadas > 0)
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="alert-card danger">
                <div class="alert-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $entregasAtrasadas }} entregas atrasadas</h6>
                    <small class="text-muted">Pedidos con fecha de entrega vencida</small>
                </div>
                <a href="{{ route('compras') }}?estado=enviada" class="btn btn-sm btn-danger ms-auto">Ver pedidos</a>
            </div>
        </div>
        @endif
        @if($entregasPendientesHoy > 0)
        <div class="col-md-6">
            <div class="alert-card warning">
                <div class="alert-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $entregasPendientesHoy }} entregas para hoy</h6>
                    <small class="text-muted">Pedidos programados para entregar hoy</small>
                </div>
                <a href="{{ route('compras') }}?fecha_entrega={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-warning ms-auto">Ver pedidos</a>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Stats Cards - Ventas --}}
    <div class="row g-3 mb-4">
        {{-- Ventas Hoy --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card primary">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                    @if($cambioVentasDia != 0)
                    <span class="stat-change {{ $cambioVentasDia >= 0 ? 'positive' : 'negative' }}">
                        <i class="bi bi-{{ $cambioVentasDia >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                        {{ number_format(abs($cambioVentasDia), 1) }}%
                    </span>
                    @endif
                </div>
                <div class="stat-value">${{ number_format($ventasHoy, 0, ',', '.') }}</div>
                <div class="stat-label">Ventas Hoy</div>
                <small class="opacity-75">{{ $pedidosHoy }} pedidos</small>
            </div>
        </div>

        {{-- Ventas Semana --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card success">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    @if($cambioVentasSemana != 0)
                    <span class="stat-change {{ $cambioVentasSemana >= 0 ? 'positive' : 'negative' }}">
                        <i class="bi bi-{{ $cambioVentasSemana >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                        {{ number_format(abs($cambioVentasSemana), 1) }}%
                    </span>
                    @endif
                </div>
                <div class="stat-value">${{ number_format($ventasSemana, 0, ',', '.') }}</div>
                <div class="stat-label">Ventas Semana</div>
                <small class="opacity-75">{{ $pedidosSemana }} pedidos</small>
            </div>
        </div>

        {{-- Ventas Mes --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card warning">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    @if($cambioVentasMes != 0)
                    <span class="stat-change {{ $cambioVentasMes >= 0 ? 'positive' : 'negative' }}">
                        <i class="bi bi-{{ $cambioVentasMes >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                        {{ number_format(abs($cambioVentasMes), 1) }}%
                    </span>
                    @endif
                </div>
                <div class="stat-value">${{ number_format($ventasMes, 0, ',', '.') }}</div>
                <div class="stat-label">Ventas Mes</div>
                <small class="opacity-75">{{ $pedidosMes }} pedidos</small>
            </div>
        </div>

        {{-- Ticket Promedio --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card info">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
                <div class="stat-value">${{ number_format($ticketPromedio, 0, ',', '.') }}</div>
                <div class="stat-label">Ticket Promedio</div>
                <small class="opacity-75">Este mes</small>
            </div>
        </div>
    </div>

    {{-- Stats Cards - Clientes y Año --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-value">${{ number_format($ventasAnio, 0, ',', '.') }}</div>
                <div class="stat-label text-muted">Ventas del Año</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($clientesUnicos) }}</div>
                <div class="stat-label text-muted">Clientes Totales</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon">
                        <i class="bi bi-person-plus"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($clientesNuevosMes) }}</div>
                <div class="stat-label text-muted">Clientes Nuevos</div>
                <small class="text-muted">Este mes</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
                <div class="stat-value">{{ array_sum($pedidosPorEstado) }}</div>
                <div class="stat-label text-muted">Pedidos Totales</div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row g-4 mb-4">
        {{-- Gráfico de Ventas Últimos 30 días --}}
        <div class="col-lg-8">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="bi bi-graph-up"></i>
                    Ventas - Últimos 30 días
                </h5>
                <canvas id="ventasDiariasChart" height="120"></canvas>
            </div>
        </div>

        {{-- Estados de Pedidos --}}
        <div class="col-lg-4">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="bi bi-pie-chart"></i>
                    Pedidos por Estado
                </h5>
                <canvas id="estadosPedidosChart" height="200"></canvas>
                <div class="mt-3">
                    @php
                        $estadosLabels = [
                            'pendiente' => ['label' => 'Pendientes', 'class' => 'estado-pendiente'],
                            'pagada' => ['label' => 'Pagadas', 'class' => 'estado-pagada'],
                            'enviada' => ['label' => 'Enviadas', 'class' => 'estado-enviada'],
                            'entregada' => ['label' => 'Entregadas', 'class' => 'estado-entregada'],
                            'cancelada' => ['label' => 'Canceladas', 'class' => 'estado-cancelada'],
                        ];
                    @endphp
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($pedidosPorEstado as $estado => $total)
                            <span class="estado-badge {{ $estadosLabels[$estado]['class'] ?? 'bg-secondary' }}">
                                {{ $estadosLabels[$estado]['label'] ?? ucfirst($estado) }}: {{ $total }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Ventas por Mes --}}
        <div class="col-lg-6">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="bi bi-bar-chart"></i>
                    Ventas por Mes - Último Año
                </h5>
                <canvas id="ventasMensualesChart" height="150"></canvas>
            </div>
        </div>

        {{-- Horarios Pico --}}
        <div class="col-lg-6">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="bi bi-clock-history"></i>
                    Horarios de Mayor Actividad
                </h5>
                <div class="row g-2 mt-2">
                    @php
                        $maxPedidos = max($horasCompletas) ?: 1;
                        $horasPico = [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];
                    @endphp
                    @foreach($horasPico as $hora)
                        @php
                            $total = $horasCompletas[$hora] ?? 0;
                            $porcentaje = ($total / $maxPedidos) * 100;
                        @endphp
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="text-muted small" style="width: 50px;">{{ sprintf('%02d:00', $hora) }}</span>
                                <div class="hora-bar flex-grow-1">
                                    <div class="hora-bar-fill" style="width: {{ $porcentaje }}%"></div>
                                </div>
                                <span class="small fw-medium" style="width: 30px;">{{ $total }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Sección de Stock --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-box-seam text-primary"></i> Resumen de Inventario
            </h5>
        </div>

        {{-- Cards de Stock --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $totalProductos }}</div>
                <div class="stat-label text-muted">Total Productos</div>
                <small class="text-muted">Con control de stock</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value text-success">{{ $productosConStock }}</div>
                <div class="stat-label text-muted">Con Stock</div>
                <small class="text-muted">Disponibles</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="stat-value text-warning">{{ $productosStockBajo }}</div>
                <div class="stat-label text-muted">Stock Bajo</div>
                <small class="text-muted">Requieren atención</small>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
                <div class="stat-value text-danger">{{ $productosSinStock }}</div>
                <div class="stat-label text-muted">Sin Stock</div>
                <small class="text-muted">Agotados</small>
            </div>
        </div>
    </div>

    {{-- Movimientos y Productos Críticos --}}
    <div class="row g-4 mb-4">
        {{-- Movimientos del Mes --}}
        <div class="col-lg-4">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="bi bi-arrow-down-up"></i>
                    Movimientos del Mes
                </h5>
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <h6 class="text-success mb-1">Entradas</h6>
                        <h3 class="text-success mb-0">+{{ number_format($entradasMes) }}</h3>
                        <small class="text-muted">unidades</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-danger mb-1">Salidas</h6>
                        <h3 class="text-danger mb-0">-{{ number_format($salidasMes) }}</h3>
                        <small class="text-muted">unidades</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <h6 class="text-muted mb-1">Balance del Mes</h6>
                    @php
                        $balance = $entradasMes - $salidasMes;
                        $colorBalance = $balance >= 0 ? 'success' : 'danger';
                        $signoBalance = $balance >= 0 ? '+' : '';
                    @endphp
                    <h3 class="text-{{ $colorBalance }} mb-0">{{ $signoBalance }}{{ number_format($balance) }}</h3>
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-seam"></i> Gestionar Stock
                    </a>
                </div>
            </div>
        </div>

        {{-- Productos Críticos --}}
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="chart-title mb-0">
                        <i class="bi bi-exclamation-octagon"></i>
                        Productos Críticos - Stock Bajo
                    </h5>
                    <a href="{{ route('stock.index') }}?estado=stock_bajo" class="btn btn-sm btn-outline-warning">Ver todos</a>
                </div>
                @if($productosCriticos->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                        <p class="text-muted mt-2 mb-0">No hay productos con stock crítico</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Mínimo</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productosCriticos as $stock)
                                    <tr>
                                        <td>
                                            <strong>{{ $stock->producto->referencia ?? '-' }}</strong>
                                            <br><small class="text-muted">
                                                {{ Str::limit($stock->producto->nombre ?? '', 30) }}
                                                @if($stock->variante)
                                                    - {{ $stock->variante->nombre_variante }}
                                                @endif
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $stock->stock_real <= 0 ? 'danger' : 'warning' }} fs-6">
                                                {{ $stock->stock_real }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $stock->stock_minimo }}</td>
                                        <td class="text-center">
                                            @if($stock->stock_real <= 0)
                                                <span class="badge bg-danger">Agotado</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Bajo</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Productos Más Vendidos --}}
    <div class="row">
        <div class="col-12">
            <div class="chart-card">
                <h5 class="chart-title">
                    <i class="bi bi-trophy"></i>
                    Productos Más Vendidos del Mes
                </h5>
                @if($productosMasVendidos->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">No hay ventas registradas este mes</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-productos align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Unidades Vendidas</th>
                                    <th class="text-end">Ingresos</th>
                                    <th class="text-end">Precio Unitario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productosMasVendidos as $index => $item)
                                    <tr>
                                        <td>
                                            <div class="product-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                                {{ $index + 1 }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-medium">{{ $item->producto->nombre ?? 'Producto eliminado' }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark fs-6">{{ number_format($item->total_vendido) }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            ${{ number_format($item->ingresos_totales, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end text-muted">
                                            ${{ number_format($item->producto->precio ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colores del tema
    const primaryGradient = (ctx) => {
        const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(236, 72, 153, 0.3)');
        gradient.addColorStop(1, 'rgba(236, 72, 153, 0.01)');
        return gradient;
    };

    // Datos de ventas diarias
    const diasLabels = @json($diasGrafico->pluck('fecha'));
    const ventasDiarias = @json($diasGrafico->pluck('ventas'));
    const pedidosDiarios = @json($diasGrafico->pluck('pedidos'));

    // Gráfico de Ventas Diarias
    new Chart(document.getElementById('ventasDiariasChart'), {
        type: 'line',
        data: {
            labels: diasLabels,
            datasets: [{
                label: 'Ventas ($)',
                data: ventasDiarias,
                borderColor: '#ec4899',
                backgroundColor: primaryGradient,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#ec4899',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            const idx = context.dataIndex;
                            return [
                                'Ventas: $' + context.raw.toLocaleString('es-CO'),
                                'Pedidos: ' + pedidosDiarios[idx]
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 10 }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        callback: function(value) {
                            return '$' + (value / 1000).toFixed(0) + 'k';
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Datos de estados
    const estadosData = @json($pedidosPorEstado);
    const estadosLabels = {
        'pendiente': 'Pendientes',
        'pagada': 'Pagadas',
        'enviada': 'Enviadas',
        'entregada': 'Entregadas',
        'cancelada': 'Canceladas'
    };
    const estadosColores = {
        'pendiente': '#fbbf24',
        'pagada': '#10b981',
        'enviada': '#3b82f6',
        'entregada': '#8b5cf6',
        'cancelada': '#ef4444'
    };

    // Gráfico de Estados
    new Chart(document.getElementById('estadosPedidosChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(estadosData).map(e => estadosLabels[e] || e),
            datasets: [{
                data: Object.values(estadosData),
                backgroundColor: Object.keys(estadosData).map(e => estadosColores[e] || '#6b7280'),
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Datos de ventas mensuales
    const mesesLabels = @json($mesesGrafico->pluck('mes'));
    const ventasMensuales = @json($mesesGrafico->pluck('ventas'));
    const pedidosMensuales = @json($mesesGrafico->pluck('pedidos'));

    // Gráfico de Ventas Mensuales
    new Chart(document.getElementById('ventasMensualesChart'), {
        type: 'bar',
        data: {
            labels: mesesLabels,
            datasets: [{
                label: 'Ventas ($)',
                data: ventasMensuales,
                backgroundColor: (ctx) => {
                    const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 200);
                    gradient.addColorStop(0, '#ec4899');
                    gradient.addColorStop(1, '#8b5cf6');
                    return gradient;
                },
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const idx = context.dataIndex;
                            return [
                                'Ventas: $' + context.raw.toLocaleString('es-CO'),
                                'Pedidos: ' + pedidosMensuales[idx]
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return '$' + (value / 1000000).toFixed(1) + 'M';
                            }
                            return '$' + (value / 1000).toFixed(0) + 'k';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
