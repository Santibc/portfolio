<x-layouts.public title="Catálogo de Proyectos">
    <div class="marketplace-layout">
        {{-- Sidebar de Filtros --}}
        <aside class="filters-sidebar">
            <div class="filters-header">
                <h3><i class="fas fa-filter"></i> Filtros</h3>
                @if(request()->hasAny(['categoria', 'search', 'roi_min', 'plazo_max', 'riesgo']))
                    <a href="{{ route('catalog.index') }}" class="clear-filters">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('catalog.index') }}" id="filtersForm">
                {{-- Búsqueda --}}
                <div class="filter-group">
                    <label><i class="fas fa-search"></i> Buscar</label>
                    <input type="text"
                           name="search"
                           placeholder="Nombre del proyecto..."
                           value="{{ $filters['search'] ?? '' }}"
                           class="filter-input">
                </div>

                {{-- Categorías --}}
                <div class="filter-group">
                    <label><i class="fas fa-tags"></i> Categoría</label>
                    <div class="filter-options">
                        @foreach($categorias as $categoria)
                            <label class="filter-checkbox">
                                <input type="radio"
                                       name="categoria"
                                       value="{{ $categoria->codigo }}"
                                       {{ ($filters['categoria'] ?? '') === $categoria->codigo ? 'checked' : '' }}
                                       onchange="document.getElementById('filtersForm').submit()">
                                <span class="checkmark"></span>
                                {{ $categoria->nombre }}
                                <span class="count">({{ $categoria->proyectos()->where('estado', 'en_recaudacion')->count() }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ROI Mínimo --}}
                <div class="filter-group">
                    <label><i class="fas fa-percentage"></i> ROI Mínimo</label>
                    <div class="filter-options">
                        <label class="filter-checkbox">
                            <input type="radio" name="roi_min" value="" {{ !request('roi_min') ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Todos
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="roi_min" value="15" {{ request('roi_min') == '15' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            15% o más
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="roi_min" value="20" {{ request('roi_min') == '20' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            20% o más
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="roi_min" value="25" {{ request('roi_min') == '25' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            25% o más
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="roi_min" value="30" {{ request('roi_min') == '30' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            30% o más
                        </label>
                    </div>
                </div>

                {{-- Plazo Máximo --}}
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Plazo Máximo</label>
                    <div class="filter-options">
                        <label class="filter-checkbox">
                            <input type="radio" name="plazo_max" value="" {{ !request('plazo_max') ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Todos
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="plazo_max" value="12" {{ request('plazo_max') == '12' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Hasta 12 meses
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="plazo_max" value="18" {{ request('plazo_max') == '18' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Hasta 18 meses
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="plazo_max" value="24" {{ request('plazo_max') == '24' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Hasta 24 meses
                        </label>
                    </div>
                </div>

                {{-- Nivel de Riesgo --}}
                <div class="filter-group">
                    <label><i class="fas fa-shield-alt"></i> Nivel de Riesgo</label>
                    <div class="filter-options">
                        <label class="filter-checkbox">
                            <input type="radio" name="riesgo" value="" {{ !request('riesgo') ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Todos
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="riesgo" value="bajo" {{ request('riesgo') == 'bajo' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            <span class="risk-badge bajo">Bajo</span>
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="riesgo" value="medio" {{ request('riesgo') == 'medio' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            <span class="risk-badge medio">Medio</span>
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="riesgo" value="alto" {{ request('riesgo') == 'alto' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            <span class="risk-badge alto">Alto</span>
                        </label>
                    </div>
                </div>

                {{-- Inversión Mínima --}}
                <div class="filter-group">
                    <label><i class="fas fa-dollar-sign"></i> Inversión Mínima</label>
                    <div class="filter-options">
                        <label class="filter-checkbox">
                            <input type="radio" name="inversion_max" value="" {{ !request('inversion_max') ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Cualquier monto
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="inversion_max" value="500000" {{ request('inversion_max') == '500000' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Hasta $500,000
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="inversion_max" value="1000000" {{ request('inversion_max') == '1000000' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Hasta $1,000,000
                        </label>
                        <label class="filter-checkbox">
                            <input type="radio" name="inversion_max" value="2000000" {{ request('inversion_max') == '2000000' ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="checkmark"></span>
                            Hasta $2,000,000
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-apply-filters">
                    <i class="fas fa-check"></i> Aplicar Filtros
                </button>
            </form>
        </aside>

        {{-- Contenido Principal --}}
        <main class="products-content">
            {{-- Header con stats y ordenamiento --}}
            <div class="content-header">
                <div class="results-info">
                    <h1><i class="fas fa-seedling"></i> Proyectos de Inversión</h1>
                    <p class="results-count">{{ $proyectos->total() }} proyectos encontrados</p>
                </div>

                <div class="sort-controls">
                    <label>Ordenar por:</label>
                    <select name="sort" onchange="window.location.href=this.value">
                        <option value="{{ route('catalog.index', array_merge(request()->except('sort'), ['sort' => 'destacado'])) }}" {{ ($filters['sort'] ?? '') === 'destacado' ? 'selected' : '' }}>Destacados</option>
                        <option value="{{ route('catalog.index', array_merge(request()->except('sort'), ['sort' => 'roi'])) }}" {{ ($filters['sort'] ?? '') === 'roi' ? 'selected' : '' }}>Mayor ROI</option>
                        <option value="{{ route('catalog.index', array_merge(request()->except('sort'), ['sort' => 'fecha_cierre'])) }}" {{ ($filters['sort'] ?? '') === 'fecha_cierre' ? 'selected' : '' }}>Próximos a cerrar</option>
                        <option value="{{ route('catalog.index', array_merge(request()->except('sort'), ['sort' => 'inversion_minima'])) }}" {{ ($filters['sort'] ?? '') === 'inversion_minima' ? 'selected' : '' }}>Menor inversión</option>
                        <option value="{{ route('catalog.index', array_merge(request()->except('sort'), ['sort' => 'reciente'])) }}" {{ ($filters['sort'] ?? '') === 'reciente' ? 'selected' : '' }}>Más recientes</option>
                    </select>
                </div>
            </div>

            {{-- Stats rápidos --}}
            <div class="quick-stats">
                <div class="quick-stat">
                    <i class="fas fa-chart-line"></i>
                    <span><strong>{{ number_format($stats['roi_promedio'], 1) }}%</strong> ROI Promedio</span>
                </div>
                <div class="quick-stat">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span><strong>${{ number_format($stats['total_recaudado']/1000000, 1) }}M</strong> Recaudados</span>
                </div>
                <div class="quick-stat">
                    <i class="fas fa-users"></i>
                    <span><strong>{{ $stats['total_proyectos'] }}</strong> Proyectos Activos</span>
                </div>
            </div>

            {{-- Grid de Todos los Proyectos --}}
            @if($proyectos->count() > 0)
                <div class="products-grid">
                    @foreach($proyectos as $proyecto)
                        @php
                            $imagenPrincipal = $proyecto->imagenes->first();
                            $icon = app(\App\Services\Project\ProjectCatalogService::class)->getProjectIcon($proyecto);
                            $diasRestantes = app(\App\Services\Project\ProjectCatalogService::class)->calculateDaysRemaining($proyecto);
                        @endphp
                        <a href="{{ route('catalog.show', $proyecto->codigo) }}" class="product-card-link">
                            <div class="product-card {{ $proyecto->destacado ? 'featured' : '' }}">
                                {{-- Imagen/Icono --}}
                                <div class="product-image">
                                    @if($imagenPrincipal)
                                        <img src="{{ asset($imagenPrincipal->ruta_imagen) }}" alt="{{ $proyecto->nombre }}">
                                    @else
                                        <div class="product-icon">
                                            <i class="{{ $icon }}"></i>
                                        </div>
                                    @endif
                                    @if($proyecto->destacado)
                                        <span class="badge-featured"><i class="fas fa-star"></i> Destacado</span>
                                    @endif
                                    @if($diasRestantes > 0 && $diasRestantes <= 7)
                                        <span class="badge-urgent"><i class="fas fa-clock"></i> {{ $diasRestantes }} días</span>
                                    @endif
                                    <span class="badge-category {{ strtolower($proyecto->categoria->codigo ?? 'default') }}">
                                        {{ $proyecto->categoria->nombre ?? 'Proyecto' }}
                                    </span>
                                </div>

                                {{-- Info --}}
                                <div class="product-info">
                                    <h3 class="product-title">{{ $proyecto->nombre }}</h3>
                                    <p class="product-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $proyecto->ubicacion ?? 'Colombia' }}
                                    </p>

                                    {{-- Métricas --}}
                                    <div class="product-metrics">
                                        <div class="metric">
                                            <span class="metric-value highlight">{{ number_format($proyecto->roi_anual, 1) }}%</span>
                                            <span class="metric-label">ROI Anual</span>
                                        </div>
                                        <div class="metric">
                                            <span class="metric-value">{{ $proyecto->duracion_meses }}m</span>
                                            <span class="metric-label">Plazo</span>
                                        </div>
                                        <div class="metric">
                                            <span class="metric-value">${{ number_format($proyecto->inversion_minima/1000, 0) }}k</span>
                                            <span class="metric-label">Mín.</span>
                                        </div>
                                    </div>

                                    {{-- Barra de progreso --}}
                                    @php
                                        $progress = $proyecto->monto_objetivo > 0 ? ($proyecto->monto_recaudado / $proyecto->monto_objetivo) * 100 : 0;
                                    @endphp
                                    <div class="product-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ min($progress, 100) }}%"></div>
                                        </div>
                                        <div class="progress-info">
                                            <span class="progress-raised">${{ number_format($proyecto->monto_recaudado/1000000, 1) }}M</span>
                                            <span class="progress-percent">{{ round($progress) }}%</span>
                                        </div>
                                    </div>

                                    {{-- Botón --}}
                                    <div class="product-action">
                                        <span class="btn-invest-card">
                                            <i class="fas fa-hand-holding-usd"></i> Ver Proyecto
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="pagination-container">
                    {{ $proyectos->appends(request()->query())->links() }}
                </div>
            @else
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No se encontraron proyectos</h3>
                    <p>Intenta ajustar los filtros o busca algo diferente.</p>
                    <a href="{{ route('catalog.index') }}" class="btn-primary">
                        <i class="fas fa-times"></i> Limpiar Filtros
                    </a>
                </div>
            @endif

            {{-- Proyectos Destacados (al final) --}}
            @if($destacados->count() > 0 && !request()->hasAny(['categoria', 'search', 'roi_min', 'plazo_max', 'riesgo']))
                <section class="featured-section">
                    <h2><i class="fas fa-star"></i> Proyectos Destacados</h2>
                    <p class="section-subtitle">Seleccionados por nuestro equipo por su potencial y solidez</p>
                    <div class="featured-grid">
                        @foreach($destacados as $proyecto)
                            @php
                                $icon = app(\App\Services\Project\ProjectCatalogService::class)->getProjectIcon($proyecto);
                            @endphp
                            <a href="{{ route('catalog.show', $proyecto->codigo) }}" class="project-card-link">
                                <x-agromarket.project-card-featured
                                    :projectName="$proyecto->nombre"
                                    :location="$proyecto->ubicacion ?? 'Colombia'"
                                    :icon="$icon"
                                    :roi="$proyecto->roi_anual"
                                    :duration="$proyecto->duracion_meses . ' meses'"
                                    :minInvestment="$proyecto->inversion_minima"
                                    :raised="$proyecto->monto_recaudado"
                                    :goal="$proyecto->monto_objetivo"
                                    :featured="true"
                                />
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </main>
    </div>

    {{-- Botón flotante para filtros en móvil --}}
    <button class="mobile-filters-btn" onclick="toggleMobileFilters()">
        <i class="fas fa-filter"></i>
        <span>Filtros</span>
    </button>

    @push('styles')
    <style>
        /* Layout Principal Marketplace */
        .marketplace-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            min-height: calc(100vh - 150px);
        }

        /* Sidebar de Filtros */
        .filters-sidebar {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            height: fit-content;
            position: sticky;
            top: 90px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .filters-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .clear-filters {
            color: #e74c3c;
            font-size: 0.85rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .clear-filters:hover {
            text-decoration: underline;
        }

        .filter-group {
            margin-bottom: 1.5rem;
        }

        .filter-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .filter-group label i {
            color: var(--primary-green);
            margin-right: 0.5rem;
        }

        .filter-input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .filter-input:focus {
            outline: none;
            border-color: var(--primary-green);
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            color: #555;
            padding: 0.4rem 0;
        }

        .filter-checkbox input[type="radio"] {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid #ccc;
            border-radius: 50%;
            cursor: pointer;
            position: relative;
        }

        .filter-checkbox input[type="radio"]:checked {
            border-color: var(--primary-green);
        }

        .filter-checkbox input[type="radio"]:checked::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: var(--primary-green);
            border-radius: 50%;
        }

        .filter-checkbox .count {
            color: #999;
            font-size: 0.8rem;
            margin-left: auto;
        }

        .risk-badge {
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .risk-badge.bajo { background: #e8f5e9; color: #2e7d32; }
        .risk-badge.medio { background: #fff3e0; color: #ef6c00; }
        .risk-badge.alto { background: #ffebee; color: #c62828; }

        .btn-apply-filters {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-green);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.2s;
        }

        .btn-apply-filters:hover {
            background: var(--primary-green-dark);
        }

        /* Contenido Principal */
        .products-content {
            min-width: 0;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .results-info h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin: 0 0 0.25rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .results-info h1 i {
            color: var(--primary-green);
        }

        .results-count {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
        }

        .sort-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sort-controls label {
            font-size: 0.9rem;
            color: #666;
        }

        .sort-controls select {
            padding: 0.5rem 2rem 0.5rem 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            background: white;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
        }

        /* Quick Stats */
        .quick-stats {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #f8fdf8 0%, #f0f7f0 100%);
            border-radius: 10px;
            border: 1px solid #e8f5e9;
        }

        .quick-stat {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #555;
            font-size: 0.9rem;
        }

        .quick-stat i {
            color: var(--primary-green);
        }

        .quick-stat strong {
            color: var(--primary-green);
        }

        /* Grid de Productos */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .product-card-link {
            text-decoration: none;
            color: inherit;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .product-card.featured {
            border: 2px solid #f1c40f;
        }

        .product-image {
            position: relative;
            height: 160px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-icon {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-icon i {
            font-size: 3.5rem;
            color: rgba(255,255,255,0.4);
        }

        .badge-featured {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: #f1c40f;
            color: #333;
            padding: 0.35rem 0.6rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .badge-urgent {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            background: #e74c3c;
            color: white;
            padding: 0.35rem 0.6rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .badge-category {
            position: absolute;
            bottom: 0.75rem;
            left: 0.75rem;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-category.farming { background: rgba(46, 125, 50, 0.9); color: white; }
        .badge-category.ear { background: rgba(21, 101, 192, 0.9); color: white; }
        .badge-category.futuros { background: rgba(239, 108, 0, 0.9); color: white; }
        .badge-category.staking { background: rgba(123, 31, 162, 0.9); color: white; }
        .badge-category.default { background: rgba(0,0,0,0.6); color: white; }

        .product-info {
            padding: 1rem;
        }

        .product-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 0.35rem 0;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-location {
            font-size: 0.8rem;
            color: #888;
            margin: 0 0 0.75rem 0;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .product-metrics {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-metrics .metric {
            text-align: center;
            flex: 1;
        }

        .product-metrics .metric-value {
            display: block;
            font-weight: 700;
            font-size: 1rem;
            color: #333;
        }

        .product-metrics .metric-value.highlight {
            color: var(--primary-green);
            font-size: 1.1rem;
        }

        .product-metrics .metric-label {
            font-size: 0.7rem;
            color: #999;
            text-transform: uppercase;
        }

        .product-progress {
            margin-bottom: 0.75rem;
        }

        .product-progress .progress-bar {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 0.35rem;
        }

        .product-progress .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-green) 0%, #66bb6a 100%);
            border-radius: 3px;
        }

        .product-progress .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
        }

        .product-progress .progress-raised {
            color: var(--primary-green);
            font-weight: 600;
        }

        .product-progress .progress-percent {
            color: #888;
        }

        .product-action {
            margin-top: 0.75rem;
        }

        .btn-invest-card {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.65rem;
            background: #2D5A27;
            color: white !important;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: background 0.2s;
        }

        .product-card:hover .btn-invest-card {
            background: #1e3d1a;
            color: white !important;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
        }

        .no-results i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .no-results h3 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .no-results p {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary-green);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        /* Paginación */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }

        /* Featured Section */
        .featured-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #eee;
        }

        .featured-section h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #333;
            margin: 0 0 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .featured-section h2 i {
            color: #f1c40f;
        }

        .section-subtitle {
            color: #666;
            margin: 0 0 1.5rem 0;
            font-size: 0.95rem;
        }

        .featured-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.5rem;
        }

        .featured-grid .project-card-link {
            display: block;
            height: 100%;
        }

        .featured-grid .project-card {
            height: 100%;
        }

        .project-card-link {
            text-decoration: none;
            color: inherit;
        }

        /* Mobile Filters Button */
        .mobile-filters-btn {
            display: none;
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.3);
            z-index: 100;
            align-items: center;
            gap: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .marketplace-layout {
                grid-template-columns: 1fr;
            }

            .filters-sidebar {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1000;
                border-radius: 0;
                overflow-y: auto;
                padding-top: 2rem;
            }

            .filters-sidebar.active {
                display: block;
            }

            .mobile-filters-btn {
                display: flex;
            }
        }

        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
            }

            .quick-stats {
                flex-wrap: wrap;
            }

            .products-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .featured-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function toggleMobileFilters() {
            const sidebar = document.querySelector('.filters-sidebar');
            sidebar.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Close filters on click outside
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.filters-sidebar');
            const btn = document.querySelector('.mobile-filters-btn');

            if (sidebar.classList.contains('active') &&
                !sidebar.contains(e.target) &&
                !btn.contains(e.target)) {
                toggleMobileFilters();
            }
        });
    </script>
    @endpush
</x-layouts.public>
