@props([
    'showViewToggle' => true
])

<div class="filters-row">
    <div class="filter-group">
        <label>Categoría:</label>
        <select id="categoryFilter" class="filter-select">
            <option value="">Todas</option>
            <option value="STAKING">STAKING</option>
            <option value="EAR">EAR</option>
            <option value="TRADING">TRADING</option>
            <option value="CROSS FUND">CROSS FUND</option>
            <option value="FUTUROS">FUTUROS</option>
            <option value="LENDING">LENDING</option>
            <option value="CROWDFUNDING">CROWDFUNDING</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Estado:</label>
        <select id="statusFilter" class="filter-select">
            <option value="">Todos</option>
            <option value="active">Activos</option>
            <option value="processing">En proceso</option>
            <option value="completed">Completados</option>
            <option value="funding">En financiación</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Ordenar por:</label>
        <select id="sortBy" class="filter-select">
            <option value="date">Fecha de inversión</option>
            <option value="amount">Monto invertido</option>
            <option value="roi">Rentabilidad</option>
            <option value="progress">Progreso</option>
        </select>
    </div>
    @if($showViewToggle)
    <div class="view-toggle">
        <button class="view-btn" data-view="grid" onclick="switchView('grid')">
            <i class="fas fa-th"></i>
        </button>
        <button class="view-btn active" data-view="list" onclick="switchView('list')">
            <i class="fas fa-list"></i>
        </button>
    </div>
    @endif
</div>

@push('scripts')
<script>
function switchView(view) {
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`.view-btn[data-view="${view}"]`).classList.add('active');

    const container = document.querySelector('.investments-container');
    if (container) {
        container.className = 'investments-container view-' + view;
    }
}
</script>
@endpush
