<x-app-layout>
    <div style="padding: 2rem; width: 100%;">
        <h1 style="font-size: 2.5rem; font-weight: 700; color: #2D5A27; margin-bottom: 0.5rem;">Test de Componentes AGROMARKET</h1>
        <p style="color: #6C757D; margin-bottom: 3rem;">Bienvenido, {{ Auth::user()->name }} - Verificación completa del template</p>

        {{-- TEST 1: Stat Cards (RESPONSIVE) --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 1: Stat Cards (Responsive)</h2>
            <div class="summary-cards">
                <x-agromarket.stat-card
                    icon="fas fa-dollar-sign"
                    value="$12,450"
                    label="Capital Total"
                    change="+12.5%"
                    changeType="positive"
                    variant="primary"
                />
                <x-agromarket.stat-card
                    icon="fas fa-chart-line"
                    value="$2,340"
                    label="Ganancias del Mes"
                    change="+8.3%"
                    changeType="positive"
                    variant="success"
                />
                <x-agromarket.stat-card
                    icon="fas fa-leaf"
                    value="8"
                    label="Proyectos Activos"
                    change="+2"
                    changeType="positive"
                    variant="info"
                />
                <x-agromarket.stat-card
                    icon="fas fa-percentage"
                    value="18.5%"
                    label="ROI Promedio"
                    change="-2.1%"
                    changeType="negative"
                    variant="warning"
                />
            </div>
        </section>

        {{-- TEST 2: Distribution Bars --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 2: Distribution Bars (Distribución por Categoría)</h2>
            <div class="distribution-chart">
                <x-agromarket.distribution-bar category="STAKING" :percentage="45" variant="staking" />
                <x-agromarket.distribution-bar category="LENDING" :percentage="25" variant="ear" />
                <x-agromarket.distribution-bar category="TRADING" :percentage="20" variant="trading" />
                <x-agromarket.distribution-bar category="CROSS FUND" :percentage="10" variant="crossfund" />
            </div>
        </section>

        {{-- TEST 3: Charts con Chart.js --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 3: Charts Interactivos</h2>
            <div class="charts-grid">
                <div class="chart-container">
                    <x-agromarket.chart-container
                        title="Evolución del Portafolio"
                        chartId="evolutionChart"
                        height="300px"
                    />
                </div>
                <div class="chart-container">
                    <x-agromarket.chart-container
                        title="Rendimiento por Categoría"
                        chartId="performanceChart"
                        height="300px"
                    />
                </div>
                <div class="chart-container">
                    <x-agromarket.chart-container
                        title="Distribución del Portafolio"
                        chartId="distributionChart"
                        height="300px"
                    />
                </div>
            </div>
        </section>

        {{-- TEST 4: Proyectos por Región (Region Cards) --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 4: Proyectos por Región (Region Cards)</h2>
            <div class="map-summary-grid">
                <x-agromarket.region-card
                    region="Valle del Cauca"
                    projectCount="1"
                    :projects="[['name' => 'Limones Orgánicos Premium', 'icon' => '🍋', 'roi' => '32.5']]"
                />
                <x-agromarket.region-card
                    region="Antioquia"
                    projectCount="1"
                    :projects="[['name' => 'Aguacate Hass', 'icon' => '🥑', 'roi' => '35.0']]"
                />
                <x-agromarket.region-card
                    region="Magdalena"
                    projectCount="1"
                    :projects="[['name' => 'Banano Orgánico', 'icon' => '🍌', 'roi' => '28.7']]"
                />
                <x-agromarket.region-card
                    region="Otras Regiones"
                    projectCount="4"
                    :showMultiple="true"
                    avgRoi="21.8"
                    :projects="[
                        ['icon' => '☕'],
                        ['icon' => '🌺'],
                        ['icon' => '🍫'],
                        ['icon' => '🍍']
                    ]"
                />
            </div>
        </section>

        {{-- TEST 5: Mapa Interactivo Simple + Botón para Mapa Completo --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; margin: 0; color: #333;">✅ Test 5: Mapa de Proyectos</h2>
                <button class="btn-outline btn-sm" onclick="openProjectMap()" style="padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-expand"></i> Ver Mapa Completo
                </button>
            </div>
            <x-agromarket.map-container mapId="testMap" height="400px" />
        </section>

        {{-- TEST 6: Filter Row --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 6: Filtros y Toggle de Vista</h2>
            <x-agromarket.filter-row :showViewToggle="true" />
        </section>

        {{-- TEST 7: Investment Cards (Estilo Portafolio) --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 7: Investment Cards (Portafolio)</h2>
            <div class="investments-grid">
                <x-agromarket.investment-card
                    projectId="inv_001"
                    projectName="Limones Orgánicos Valle"
                    location="Valle del Cauca"
                    icon="fas fa-lemon"
                    :invested="3500"
                    :roi="28.5"
                    :profit="998"
                    :progress="65"
                    category="staking"
                    status="active"
                />
                <x-agromarket.investment-card
                    projectId="inv_002"
                    projectName="Café Premium Huila"
                    location="Huila"
                    icon="fas fa-coffee"
                    :invested="2000"
                    :roi="12.3"
                    :profit="246"
                    :progress="95"
                    category="lending"
                    status="processing"
                />
            </div>
        </section>

        {{-- TEST 8: Project Cards Featured (Estilo Categorías) --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 8: Project Cards Featured</h2>
            <div class="projects-grid">
                <x-agromarket.project-card-featured
                    projectName="Limones Orgánicos Valle"
                    location="Valle del Cauca, Colombia"
                    icon="fas fa-lemon"
                    roi="32.5"
                    duration="18 meses"
                    :minInvestment="1000"
                    :raised="45000"
                    :goal="60000"
                    :featured="true"
                />
                <x-agromarket.project-card-featured
                    projectName="Aguacate Hass Premium"
                    location="Antioquia, Colombia"
                    icon="fas fa-leaf"
                    roi="35.0"
                    duration="24 meses"
                    :minInvestment="1500"
                    :raised="38000"
                    :goal="50000"
                />
            </div>
        </section>

        {{-- TEST 9: Timeline Items (Historial) --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 9: Timeline Items (Historial)</h2>
            <div class="timeline-container">
                <x-agromarket.timeline-item
                    date="23"
                    month="Sep"
                    title="Dividendo Recibido"
                    description="Limones Orgánicos Valle - Pago trimestral"
                    amount="+$245.00"
                    time="14:32"
                    icon="fas fa-coins"
                    type="dividend"
                    status="completed"
                />
                <x-agromarket.timeline-item
                    date="22"
                    month="Sep"
                    title="Nueva Inversión"
                    description="Café Premium Huila - Inversión inicial"
                    amount="-$2,000"
                    time="09:15"
                    icon="fas fa-plus"
                    type="investment"
                    status="completed"
                />
                <x-agromarket.timeline-item
                    date="20"
                    month="Sep"
                    title="Trading Completado"
                    description="Transferencia de posición STAKING"
                    amount="+$150"
                    time="16:45"
                    icon="fas fa-exchange-alt"
                    type="trading"
                    status="completed"
                />
            </div>
        </section>

        {{-- TEST 10: Data Table con iconos correctos --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 10: Data Table</h2>
            <x-agromarket.data-table :headers="['Proyecto', 'Tipo', 'Inversión', 'ROI', 'Progreso', 'Estado', 'Acciones']">
                <x-agromarket.table-row>
                    <x-agromarket.table-cell>
                        <div class="project-info">
                            <div class="project-image">
                                <i class="fas fa-lemon"></i>
                            </div>
                            <div>
                                <div class="project-name">Aguacate Hass Premium</div>
                                <div class="project-location">Valle del Cauca</div>
                            </div>
                        </div>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <x-agromarket.badge variant="staking" type="category">STAKING</x-agromarket.badge>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>$5,000</x-agromarket.table-cell>
                    <x-agromarket.table-cell class="roi positive">+22%</x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 75%"></div>
                        </div>
                        <span class="progress-text">75%</span>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <x-agromarket.badge variant="active" type="status">Activo</x-agromarket.badge>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <div class="action-buttons">
                            <button class="btn-icon" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-icon" title="Trading">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                        </div>
                    </x-agromarket.table-cell>
                </x-agromarket.table-row>
                <x-agromarket.table-row>
                    <x-agromarket.table-cell>
                        <div class="project-info">
                            <div class="project-image">
                                <i class="fas fa-coffee"></i>
                            </div>
                            <div>
                                <div class="project-name">Café Premium Huila</div>
                                <div class="project-location">Huila</div>
                            </div>
                        </div>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <x-agromarket.badge variant="ear" type="category">EAR</x-agromarket.badge>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>$2,000</x-agromarket.table-cell>
                    <x-agromarket.table-cell class="roi positive">+12%</x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 95%"></div>
                        </div>
                        <span class="progress-text">95%</span>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <x-agromarket.badge variant="processing" type="status">En Proceso</x-agromarket.badge>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <div class="action-buttons">
                            <button class="btn-icon" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-icon" title="Trading">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                        </div>
                    </x-agromarket.table-cell>
                </x-agromarket.table-row>
                <x-agromarket.table-row>
                    <x-agromarket.table-cell>
                        <div class="project-info">
                            <div class="project-image">
                                <i class="fas fa-apple-alt"></i>
                            </div>
                            <div>
                                <div class="project-name">Banano Orgánico Magdalena</div>
                                <div class="project-location">Santa Marta</div>
                            </div>
                        </div>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <x-agromarket.badge variant="crossfund" type="category">CROSS FUND</x-agromarket.badge>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>$5,800</x-agromarket.table-cell>
                    <x-agromarket.table-cell class="roi positive">+28%</x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 60%"></div>
                        </div>
                        <span class="progress-text">60%</span>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <x-agromarket.badge variant="active" type="status">Activo</x-agromarket.badge>
                    </x-agromarket.table-cell>
                    <x-agromarket.table-cell>
                        <div class="action-buttons">
                            <button class="btn-icon" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-icon" title="Trading">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                        </div>
                    </x-agromarket.table-cell>
                </x-agromarket.table-row>
            </x-agromarket.data-table>
        </section>

        {{-- TEST 11: Form Groups --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 11: Form Groups</h2>
            <x-agromarket.form-group
                label="Monto de Inversión"
                name="amount_test"
                type="number"
                icon="fas fa-dollar-sign"
                placeholder="Ingrese el monto"
            />
            <x-agromarket.form-group
                label="Contraseña"
                name="password_test"
                type="password"
                icon="fas fa-lock"
                placeholder="Ingrese su contraseña"
            />
            <x-agromarket.form-group
                label="Seleccione Proyecto"
                name="project_test"
                type="select"
                icon="fas fa-seedling"
            >
                <option value="">Seleccione un proyecto</option>
                <option value="1">Aguacate Hass Premium</option>
                <option value="2">Cacao Orgánico</option>
            </x-agromarket.form-group>
        </section>

        {{-- TEST 12: Modal --}}
        <section class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: #333;">✅ Test 12: Modal</h2>
            <x-agromarket.button variant="primary" icon="fas fa-plus" onclick="openModal('testModal')">
                Abrir Modal de Prueba
            </x-agromarket.button>
        </section>

        <x-agromarket.modal id="testModal" title="Modal de Prueba">
            <div style="padding: 1rem;">
                <p>Este es el contenido del modal.</p>
                <x-agromarket.form-group
                    label="Monto"
                    name="modal_amount"
                    type="number"
                    icon="fas fa-dollar-sign"
                    placeholder="Ingrese el monto"
                />
            </div>
            <div class="modal-footer">
                <x-agromarket.button variant="outline" type="button" onclick="closeModal('testModal')">
                    Cancelar
                </x-agromarket.button>
                <x-agromarket.button variant="primary" type="button">
                    Confirmar
                </x-agromarket.button>
            </div>
        </x-agromarket.modal>

        <div class="card" style="background: #2D5A27; color: white; padding: 1.5rem; text-align: center;">
            <h3 style="margin: 0; color: white;">✅ Todos los componentes creados</h3>
            <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Verifica que cada uno se vea como en el template original</p>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart 1: Evolución
            const ctx1 = document.getElementById('evolutionChart');
            if (ctx1 && typeof Chart !== 'undefined') {
                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Portafolio',
                            data: [1200, 1900, 3000, 5000, 6200, 7800],
                            borderColor: 'var(--primary-green)',
                            backgroundColor: 'rgba(74, 124, 89, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Chart 2: Rendimiento por Categoría
            const ctx2 = document.getElementById('performanceChart');
            if (ctx2 && typeof Chart !== 'undefined') {
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: ['STAKING', 'LENDING', 'TRADING', 'CROSS FUND'],
                        datasets: [{
                            label: 'ROI %',
                            data: [28.5, 18.2, 15.3, 22.1],
                            backgroundColor: [
                                'rgba(74, 124, 89, 0.8)',
                                'rgba(52, 168, 83, 0.8)',
                                'rgba(251, 188, 5, 0.8)',
                                'rgba(234, 67, 53, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Chart 3: Distribución
            const ctx3 = document.getElementById('distributionChart');
            if (ctx3 && typeof Chart !== 'undefined') {
                new Chart(ctx3, {
                    type: 'doughnut',
                    data: {
                        labels: ['STAKING', 'LENDING', 'TRADING', 'CROSS FUND'],
                        datasets: [{
                            data: [45, 25, 20, 10],
                            backgroundColor: [
                                'rgba(74, 124, 89, 0.8)',
                                'rgba(52, 168, 83, 0.8)',
                                'rgba(251, 188, 5, 0.8)',
                                'rgba(234, 67, 53, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        });
    </script>
    @endpush

    {{-- Mapa Interactivo Modal (se abre con botón "Ver Mapa Completo") --}}
    <x-agromarket.interactive-map mapId="fullProjectMap" />
</x-app-layout>
