<x-layouts.public :title="$proyecto->nombre">
    {{-- Breadcrumb --}}
    <nav class="breadcrumb">
        <a href="{{ route('catalog.index') }}"><i class="fas fa-store"></i> Catálogo</a>
        <i class="fas fa-chevron-right"></i>
        <span>{{ $proyecto->nombre }}</span>
    </nav>

    <div class="project-detail">
        {{-- Columna Principal --}}
        <div class="project-main">
            {{-- Hero del Proyecto --}}
            <section class="project-hero">
                <div class="hero-image">
                    @if($proyecto->imagenes->count() > 0)
                        <img src="{{ asset($proyecto->imagenes->first()->ruta_imagen) }}"
                             alt="{{ $proyecto->nombre }}">
                    @else
                        <div class="hero-placeholder">
                            <i class="{{ $icon }}"></i>
                        </div>
                    @endif
                    @if($proyecto->destacado)
                        <div class="badge-featured"><i class="fas fa-star"></i> Destacado</div>
                    @endif
                </div>

                <div class="hero-info">
                    <div class="category-badge {{ strtolower($proyecto->categoria->codigo ?? 'default') }}">
                        {{ $proyecto->categoria->nombre ?? 'Proyecto' }}
                    </div>
                    <h1>{{ $proyecto->nombre }}</h1>
                    <p class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $proyecto->ubicacion ?? 'Colombia' }}
                    </p>
                    @if($proyecto->tipo_cultivo)
                        <p class="crop-type">
                            <i class="{{ $icon }}"></i>
                            {{ $proyecto->tipo_cultivo }}
                            @if($proyecto->area_hectareas)
                                - {{ number_format($proyecto->area_hectareas, 1) }} hectáreas
                            @endif
                        </p>
                    @endif
                </div>
            </section>

            {{-- Descripción --}}
            <section class="project-section">
                <h2><i class="fas fa-info-circle"></i> Descripción del Proyecto</h2>
                <div class="section-content">
                    {!! nl2br(e($proyecto->descripcion)) !!}
                </div>
            </section>

            {{-- Objetivo del Proyecto --}}
            @if($proyecto->objetivo_proyecto)
                <section class="project-section">
                    <h2><i class="fas fa-bullseye"></i> Objetivo</h2>
                    <div class="section-content">
                        {!! nl2br(e($proyecto->objetivo_proyecto)) !!}
                    </div>
                </section>
            @endif

            {{-- Proceso Productivo --}}
            @if($proyecto->detalle_proceso_productivo)
                <section class="project-section">
                    <h2><i class="fas fa-cogs"></i> Proceso Productivo</h2>
                    <div class="section-content">
                        {!! nl2br(e($proyecto->detalle_proceso_productivo)) !!}
                    </div>
                </section>
            @endif

            {{-- Cronograma --}}
            @if($proyecto->cronograma_estimado)
                <section class="project-section">
                    <h2><i class="fas fa-calendar-alt"></i> Cronograma Estimado</h2>
                    <div class="section-content">
                        {!! nl2br(e($proyecto->cronograma_estimado)) !!}
                    </div>
                </section>
            @endif

            {{-- Galería de Imágenes --}}
            @if($proyecto->imagenes->count() > 1)
                <section class="project-section">
                    <h2><i class="fas fa-images"></i> Galería</h2>
                    <div class="gallery-grid">
                        @foreach($proyecto->imagenes as $imagen)
                            <div class="gallery-item" onclick="openGallery({{ $loop->index }})">
                                <img src="{{ asset($imagen->ruta_imagen) }}"
                                     alt="{{ $imagen->titulo ?? 'Imagen del proyecto' }}">
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Actualizaciones --}}
            @if($proyecto->actualizaciones->count() > 0)
                <section class="project-section">
                    <h2><i class="fas fa-newspaper"></i> Actualizaciones del Proyecto</h2>
                    <div class="updates-list">
                        @foreach($proyecto->actualizaciones as $actualizacion)
                            <div class="update-item">
                                <div class="update-date">
                                    <span class="day">{{ $actualizacion->publicado_at?->format('d') ?? $actualizacion->created_at->format('d') }}</span>
                                    <span class="month">{{ $actualizacion->publicado_at?->format('M') ?? $actualizacion->created_at->format('M') }}</span>
                                </div>
                                <div class="update-content">
                                    <h4>{{ $actualizacion->titulo }}</h4>
                                    <p>{{ Str::limit($actualizacion->contenido, 200) }}</p>
                                    <span class="update-type {{ $actualizacion->tipo }}">
                                        {{ ucfirst($actualizacion->tipo) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Agricultor --}}
            <section class="project-section">
                <h2><i class="fas fa-user-tie"></i> Sobre el Agricultor</h2>
                <div class="farmer-info">
                    <div class="farmer-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="farmer-details">
                        <h4>{{ $proyecto->agricultor->name ?? 'Agricultor Verificado' }}</h4>
                        @if($proyecto->agricultor->ciudad)
                            <p><i class="fas fa-map-marker-alt"></i> {{ $proyecto->agricultor->ciudad }}, {{ $proyecto->agricultor->pais ?? 'Colombia' }}</p>
                        @endif
                        <span class="verified-badge">
                            <i class="fas fa-check-circle"></i> Verificado por AGROMARKET
                        </span>
                    </div>
                </div>
            </section>
        </div>

        {{-- Sidebar --}}
        <aside class="project-sidebar">
            {{-- Card de Inversión --}}
            <div class="investment-card">
                <div class="investment-stats">
                    <div class="stat-row highlight">
                        <span class="stat-label">Rentabilidad Anual</span>
                        <span class="stat-value success">{{ number_format($proyecto->roi_anual, 1) }}% E.A</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Duración</span>
                        <span class="stat-value">{{ $proyecto->duracion_meses }} meses</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Inversión Mínima</span>
                        <span class="stat-value">${{ number_format($proyecto->inversion_minima, 0, ',', '.') }}</span>
                    </div>
                    @if($proyecto->inversion_maxima)
                        <div class="stat-row">
                            <span class="stat-label">Inversión Máxima</span>
                            <span class="stat-value">${{ number_format($proyecto->inversion_maxima, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="stat-row">
                        <span class="stat-label">Nivel de Riesgo</span>
                        <span class="stat-value risk-{{ $proyecto->nivel_riesgo }}">
                            {{ ucfirst($proyecto->nivel_riesgo) }}
                        </span>
                    </div>
                    @if($proyecto->periodo_dividendos_dias)
                        <div class="stat-row">
                            <span class="stat-label">Pago de Dividendos</span>
                            <span class="stat-value">Cada {{ $proyecto->periodo_dividendos_dias }} días</span>
                        </div>
                    @endif
                </div>

                {{-- Progreso de Recaudación --}}
                <div class="funding-progress">
                    <div class="progress-header">
                        <span class="progress-label">Recaudado</span>
                        <span class="progress-percentage">{{ $progress }}%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" style="width: {{ min($progress, 100) }}%"></div>
                    </div>
                    <div class="progress-amounts">
                        <span class="raised">${{ number_format($proyecto->monto_recaudado, 0, ',', '.') }}</span>
                        <span class="goal">de ${{ number_format($proyecto->monto_objetivo, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Días Restantes --}}
                @if($diasRestantes > 0)
                    <div class="days-remaining">
                        <i class="fas fa-clock"></i>
                        <span><strong>{{ $diasRestantes }}</strong> días restantes para invertir</span>
                    </div>
                @elseif($proyecto->fecha_cierre_recaudacion && $proyecto->fecha_cierre_recaudacion->isPast())
                    <div class="days-remaining closed">
                        <i class="fas fa-lock"></i>
                        <span>Recaudación cerrada</span>
                    </div>
                @endif

                {{-- Botón de Inversión --}}
                <div class="investment-actions">
                    @auth
                        @if($proyecto->estado === 'en_recaudacion')
                            @if(auth()->user()->hasRole('Inversionista'))
                                @if(in_array(auth()->user()->kyc_status, ['en_revision', 'aprobado']))
                                    <a href="{{ route('inversionista.investments.create', $proyecto) }}" class="btn-invest">
                                        <i class="fas fa-hand-holding-usd"></i> Invertir Ahora
                                    </a>
                                @elseif(auth()->user()->kyc_status === 'pendiente')
                                    <a href="{{ route('inversionista.kyc.create') }}" class="btn-invest btn-kyc">
                                        <i class="fas fa-id-card"></i> Completar KYC para Invertir
                                    </a>
                                    <p class="kyc-hint">Sube tus documentos para poder invertir</p>
                                @else
                                    <a href="{{ route('inversionista.kyc.create') }}" class="btn-invest btn-kyc-rejected">
                                        <i class="fas fa-exclamation-triangle"></i> KYC Rechazado - Reintentar
                                    </a>
                                @endif
                            @else
                                <button class="btn-invest disabled" disabled onclick="Swal.fire({icon: 'info', title: 'Solo Inversionistas', text: 'Debes tener una cuenta de inversionista para invertir.', confirmButtonColor: '#2D5A27'})">
                                    <i class="fas fa-info-circle"></i> Solo para Inversionistas
                                </button>
                            @endif
                        @else
                            <button class="btn-invest disabled" disabled>
                                <i class="fas fa-lock"></i> No disponible
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="btn-invest">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión para Invertir
                        </a>
                        <p class="register-hint">
                            ¿No tienes cuenta?
                            <a href="{{ route('register') }}">Regístrate gratis</a>
                        </p>
                    @endauth
                </div>

                {{-- Fechas Importantes --}}
                @if($proyecto->fecha_inicio_proyecto || $proyecto->fecha_primer_dividendo)
                    <div class="important-dates">
                        <h4><i class="fas fa-calendar-check"></i> Fechas Clave</h4>
                        @if($proyecto->fecha_cierre_recaudacion)
                            <div class="date-item">
                                <span class="date-label">Cierre de recaudación</span>
                                <span class="date-value">{{ $proyecto->fecha_cierre_recaudacion->format('d M Y') }}</span>
                            </div>
                        @endif
                        @if($proyecto->fecha_inicio_proyecto)
                            <div class="date-item">
                                <span class="date-label">Inicio del proyecto</span>
                                <span class="date-value">{{ $proyecto->fecha_inicio_proyecto->format('d M Y') }}</span>
                            </div>
                        @endif
                        @if($proyecto->fecha_primer_dividendo)
                            <div class="date-item">
                                <span class="date-label">Primer dividendo</span>
                                <span class="date-value">{{ $proyecto->fecha_primer_dividendo->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Compartir --}}
            <div class="share-card">
                <h4>Compartir Proyecto</h4>
                <div class="share-buttons">
                    <a href="https://wa.me/?text={{ urlencode($proyecto->nombre . ' - Invierte en AGROMARKET: ' . url()->current()) }}"
                       target="_blank" class="share-btn whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                       target="_blank" class="share-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($proyecto->nombre) }}&url={{ urlencode(url()->current()) }}"
                       target="_blank" class="share-btn twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <button class="share-btn copy" onclick="copyLink()">
                        <i class="fas fa-link"></i>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    {{-- Proyectos Relacionados --}}
    @if($relacionados->count() > 0)
        <section class="related-projects">
            <h2><i class="fas fa-seedling"></i> Proyectos Similares</h2>
            <div class="projects-grid">
                @foreach($relacionados as $relacionado)
                    @php
                        $relIcon = app(\App\Services\Project\ProjectCatalogService::class)->getProjectIcon($relacionado);
                    @endphp
                    <a href="{{ route('catalog.show', $relacionado->codigo) }}" class="project-card-link">
                        <x-agromarket.project-card-featured
                            :projectName="$relacionado->nombre"
                            :location="$relacionado->ubicacion ?? 'Colombia'"
                            :icon="$relIcon"
                            :roi="$relacionado->roi_anual"
                            :duration="$relacionado->duracion_meses . ' meses'"
                            :minInvestment="$relacionado->inversion_minima"
                            :raised="$relacionado->monto_recaudado"
                            :goal="$relacionado->monto_objetivo"
                            :featured="$relacionado->destacado"
                        />
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @push('styles')
    <style>
        :root {
            --primary-green: #2D5A27;
            --primary-green-dark: #1e3d1a;
            --primary-green-light: #3a7032;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--primary-green);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb i.fa-chevron-right {
            color: #999;
            font-size: 0.7rem;
        }

        .breadcrumb span {
            color: #666;
        }

        /* Layout */
        .project-detail {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2rem;
            align-items: start;
        }

        /* Hero */
        .project-hero {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .hero-image {
            position: relative;
            height: 300px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        }

        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-placeholder i {
            font-size: 5rem;
            color: rgba(255,255,255,0.3);
        }

        .badge-featured {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #f1c40f;
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .hero-info {
            padding: 1.5rem;
        }

        .category-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
        }

        .category-badge.farming { background: #e8f5e9; color: #2e7d32; }
        .category-badge.ear { background: #e3f2fd; color: #1565c0; }
        .category-badge.futuros { background: #fff3e0; color: #ef6c00; }
        .category-badge.staking { background: #f3e5f5; color: #7b1fa2; }
        .category-badge.default { background: #f5f5f5; color: #666; }

        .hero-info h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .hero-info .location,
        .hero-info .crop-type {
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }

        /* Secciones */
        .project-section {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .project-section h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .project-section h2 i {
            color: var(--primary-green);
        }

        .section-content {
            color: #555;
            line-height: 1.7;
        }

        /* Galería */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .gallery-item {
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Updates */
        .updates-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .update-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .update-date {
            text-align: center;
            min-width: 50px;
        }

        .update-date .day {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-green);
        }

        .update-date .month {
            font-size: 0.85rem;
            color: #666;
            text-transform: uppercase;
        }

        .update-content h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .update-content p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .update-type {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .update-type.informativo { background: #e3f2fd; color: #1565c0; }
        .update-type.hito { background: #e8f5e9; color: #2e7d32; }
        .update-type.alerta { background: #fff3e0; color: #ef6c00; }
        .update-type.financiero { background: #f3e5f5; color: #7b1fa2; }

        /* Farmer */
        .farmer-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .farmer-avatar {
            width: 60px;
            height: 60px;
            background: var(--primary-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .farmer-details h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .farmer-details p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            color: var(--primary-green);
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Sidebar */
        .project-sidebar {
            position: sticky;
            top: 90px;
        }

        .investment-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }

        .investment-stats {
            margin-bottom: 1.5rem;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-row.highlight {
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
            margin: -0.75rem -1.5rem 0.75rem;
            padding: 1rem 1.5rem;
            border-radius: 8px 8px 0 0;
            border-bottom: none;
        }

        .stat-row .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .stat-row .stat-value {
            font-weight: 600;
            color: #333;
        }

        .stat-row .stat-value.success {
            color: var(--primary-green);
            font-size: 1.25rem;
        }

        .stat-row .stat-value.risk-bajo { color: #2e7d32; }
        .stat-row .stat-value.risk-medio { color: #ef6c00; }
        .stat-row .stat-value.risk-alto { color: #c62828; }

        /* Progress */
        .funding-progress {
            margin-bottom: 1.5rem;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .progress-label {
            color: #666;
            font-size: 0.9rem;
        }

        .progress-percentage {
            font-weight: 600;
            color: var(--primary-green);
        }

        .progress-bar-container {
            height: 10px;
            background: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-green) 0%, #66bb6a 100%);
            border-radius: 5px;
            transition: width 0.5s ease;
        }

        .progress-amounts {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }

        .progress-amounts .raised {
            font-weight: 600;
            color: var(--primary-green);
        }

        .progress-amounts .goal {
            color: #666;
        }

        /* Days remaining */
        .days-remaining {
            background: #fff3e0;
            color: #ef6c00;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .days-remaining.closed {
            background: #ffebee;
            color: #c62828;
        }

        /* Investment actions */
        .investment-actions {
            margin-bottom: 1.5rem;
        }

        .btn-invest {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, #2D5A27 0%, #1e3d1a 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.15rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(45, 90, 39, 0.25);
        }

        .btn-invest i {
            font-size: 1.25rem;
        }

        .btn-invest:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(45, 90, 39, 0.35);
            background: linear-gradient(135deg, #3a7032 0%, #2D5A27 100%);
        }

        .btn-invest.disabled {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-invest.disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .btn-invest.btn-kyc {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .btn-invest.btn-kyc:hover {
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-invest.btn-kyc-rejected {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .btn-invest.btn-kyc-rejected:hover {
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .kyc-hint {
            text-align: center;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #d97706;
        }

        .register-hint {
            text-align: center;
            margin-top: 0.75rem;
            font-size: 0.9rem;
            color: #666;
        }

        .register-hint a {
            color: var(--primary-green);
            font-weight: 500;
        }

        /* Important dates */
        .important-dates {
            border-top: 1px solid #f0f0f0;
            padding-top: 1rem;
        }

        .important-dates h4 {
            font-size: 0.95rem;
            color: #333;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .date-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-size: 0.85rem;
        }

        .date-label {
            color: #666;
        }

        .date-value {
            color: #333;
            font-weight: 500;
        }

        /* Share card */
        .share-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .share-card h4 {
            font-size: 0.95rem;
            color: #333;
            margin-bottom: 0.75rem;
        }

        .share-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: none;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
        }

        .share-btn:hover {
            transform: scale(1.1);
        }

        .share-btn.whatsapp { background: #25d366; }
        .share-btn.facebook { background: #1877f2; }
        .share-btn.twitter { background: #1da1f2; }
        .share-btn.copy { background: #666; }

        /* Related projects */
        .related-projects {
            margin-top: 3rem;
        }

        .related-projects h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .related-projects h2 i {
            color: var(--primary-green);
        }

        .related-projects .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.5rem;
        }

        .project-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
            transition: transform 0.2s;
        }

        .project-card-link:hover {
            transform: translateY(-4px);
        }

        .project-card-link .project-card {
            height: 100%;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .project-detail {
                grid-template-columns: 1fr;
            }

            .project-sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .hero-image {
                height: 200px;
            }

            .hero-info h1 {
                font-size: 1.5rem;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .related-projects .projects-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Enlace copiado',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        }

        function openGallery(index) {
            // TODO: Implementar lightbox para galería
            console.log('Open gallery at index:', index);
        }
    </script>
    @endpush
</x-layouts.public>
