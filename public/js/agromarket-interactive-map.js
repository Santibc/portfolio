// AGROMARKET - Mapa Interactivo de Proyectos por Región

class InteractiveProjectMap {
    constructor() {
        this.map = null;
        this.markers = [];
        this.projectData = [];
        this.currentFilter = 'all';
        this.isInitialized = false;
        this.init();
    }

    init() {
        this.loadProjectData();
        this.createMapModal();
        console.log('Mapa interactivo de proyectos inicializado');
    }

    loadProjectData() {
        // Datos de proyectos por región de Colombia
        this.projectData = [
            {
                id: 'proj_001',
                name: 'Limones Orgánicos Valle Premium',
                category: 'STAKING',
                region: 'Valle del Cauca',
                city: 'Cali',
                coordinates: [3.4516, -76.5320],
                investment: 3500000,
                roi: 32.5,
                duration: '18 meses',
                progress: 75,
                status: 'active',
                description: 'Cultivo premium de limones orgánicos con certificación internacional para exportación.',
                climate: 'Tropical',
                hectares: 45,
                farmers: 12,
                certification: 'Orgánico Internacional',
                riskLevel: 'Medio',
                nextHarvest: '2024-12-15',
                images: ['🍋', '🌱', '🚜']
            },
            {
                id: 'proj_002',
                name: 'Café Premium Huila',
                category: 'EAR',
                region: 'Huila',
                city: 'Neiva',
                coordinates: [2.9273, -75.2819],
                investment: 2000000,
                roi: 12.3,
                duration: '4 meses',
                progress: 95,
                status: 'processing',
                description: 'Procesamiento y empaque de café premium colombiano para mercado internacional.',
                climate: 'Montañoso',
                hectares: 28,
                farmers: 8,
                certification: 'Fair Trade',
                riskLevel: 'Bajo',
                nextHarvest: '2024-11-30',
                images: ['☕', '🏔️', '📦']
            },
            {
                id: 'proj_003',
                name: 'Aguacate Hass Antioquia',
                category: 'STAKING',
                region: 'Antioquia',
                city: 'Medellín',
                coordinates: [6.2442, -75.5812],
                investment: 4200000,
                roi: 35.0,
                duration: '24 meses',
                progress: 45,
                status: 'active',
                description: 'Cultivo de aguacate Hass para exportación a Estados Unidos y Europa.',
                climate: 'Templado',
                hectares: 62,
                farmers: 18,
                certification: 'GlobalGAP',
                riskLevel: 'Medio',
                nextHarvest: '2025-03-20',
                images: ['🥑', '🌿', '✈️']
            },
            {
                id: 'proj_004',
                name: 'Banano Orgánico Magdalena',
                category: 'CROSS FUND',
                region: 'Magdalena',
                city: 'Santa Marta',
                coordinates: [11.2408, -74.2099],
                investment: 5800000,
                roi: 28.7,
                duration: '20 meses',
                progress: 60,
                status: 'active',
                description: 'Producción de banano orgánico para mercados europeos premium.',
                climate: 'Tropical Costero',
                hectares: 85,
                farmers: 25,
                certification: 'Rainforest Alliance',
                riskLevel: 'Bajo',
                nextHarvest: '2024-12-01',
                images: ['🍌', '🌊', '🌴']
            },
            {
                id: 'proj_005',
                name: 'Flores Exóticas Cundinamarca',
                category: 'TRADING',
                region: 'Cundinamarca',
                city: 'Bogotá',
                coordinates: [4.7110, -74.0721],
                investment: 1800000,
                roi: 22.1,
                duration: '8 meses',
                progress: 30,
                status: 'active',
                description: 'Cultivo de flores exóticas para exportación a mercados internacionales.',
                climate: 'Frío',
                hectares: 15,
                farmers: 6,
                certification: 'Florverde',
                riskLevel: 'Alto',
                nextHarvest: '2025-02-14',
                images: ['🌺', '❄️', '💐']
            },
            {
                id: 'proj_006',
                name: 'Cacao Fino Santander',
                category: 'EAR',
                region: 'Santander',
                city: 'Bucaramanga',
                coordinates: [7.1193, -73.1227],
                investment: 3200000,
                roi: 18.5,
                duration: '12 meses',
                progress: 80,
                status: 'active',
                description: 'Procesamiento de cacao fino de aroma para chocolatería premium.',
                climate: 'Tropical Seco',
                hectares: 38,
                farmers: 14,
                certification: 'UTZ Certified',
                riskLevel: 'Medio',
                nextHarvest: '2024-11-15',
                images: ['🍫', '🌞', '🏭']
            },
            {
                id: 'proj_007',
                name: 'Piña Golden Córdoba',
                category: 'FUTUROS',
                region: 'Córdoba',
                city: 'Montería',
                coordinates: [8.7479, -75.8814],
                investment: 2700000,
                roi: 25.3,
                duration: '15 meses',
                progress: 25,
                status: 'planning',
                description: 'Cultivo de piña golden para jugos y exportación fresca.',
                climate: 'Tropical Húmedo',
                hectares: 52,
                farmers: 16,
                certification: 'En proceso',
                riskLevel: 'Medio',
                nextHarvest: '2025-06-30',
                images: ['🍍', '🌧️', '🥤']
            }
        ];
    }

    createMapModal() {
        const modal = document.createElement('div');
        modal.className = 'map-modal';
        modal.innerHTML = `
            <div class="map-overlay" onclick="closeProjectMap()">
                <div class="map-container" onclick="event.stopPropagation()">
                    <div class="map-header">
                        <div class="map-title">
                            <h2><i class="fas fa-map-marked-alt"></i> Mapa de Proyectos Agrícolas</h2>
                            <p>Explora proyectos de inversión por toda Colombia</p>
                        </div>
                        <button class="map-close-btn" onclick="closeProjectMap()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="map-controls">
                        <div class="filter-controls">
                            <div class="filter-group">
                                <label>Filtrar por categoría:</label>
                                <select id="categoryMapFilter" onchange="filterMapProjects(this.value)">
                                    <option value="all">Todas las categorías</option>
                                    <option value="STAKING">STAKING</option>
                                    <option value="EAR">EAR</option>
                                    <option value="CROSS FUND">CROSS FUND</option>
                                    <option value="TRADING">TRADING</option>
                                    <option value="FUTUROS">FUTUROS</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label>Estado:</label>
                                <select id="statusMapFilter" onchange="filterMapProjects()">
                                    <option value="all">Todos los estados</option>
                                    <option value="active">Activos</option>
                                    <option value="processing">En proceso</option>
                                    <option value="planning">En planificación</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label>ROI mínimo:</label>
                                <input type="range" id="roiMapFilter" min="0" max="40" value="0" 
                                       onchange="updateROIFilter(this.value); filterMapProjects()">
                                <span id="roiFilterValue">0%</span>
                            </div>
                        </div>
                        
                        <div class="map-stats">
                            <div class="stat-item">
                                <span class="stat-value" id="totalProjects">7</span>
                                <span class="stat-label">Proyectos</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value" id="totalInvestment">$23.2M</span>
                                <span class="stat-label">Inversión Total</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value" id="avgROI">24.9%</span>
                                <span class="stat-label">ROI Promedio</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="map-content">
                        <div id="projectMap" class="project-map"></div>
                        
                        <div class="map-sidebar">
                            <div class="legend">
                                <h4>Leyenda</h4>
                                <div class="legend-items">
                                    <div class="legend-item">
                                        <div class="legend-marker staking"></div>
                                        <span>STAKING</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-marker ear"></div>
                                        <span>EAR</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-marker crossfund"></div>
                                        <span>CROSS FUND</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-marker trading"></div>
                                        <span>TRADING</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-marker futuros"></div>
                                        <span>FUTUROS</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="project-details" id="projectDetails">
                                <div class="no-selection">
                                    <i class="fas fa-mouse-pointer"></i>
                                    <p>Haz clic en un marcador para ver detalles del proyecto</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="map-footer">
                        <button class="btn-outline" onclick="resetMapFilters()">
                            <i class="fas fa-undo"></i> Limpiar Filtros
                        </button>
                        <button class="btn-secondary" onclick="exportMapData()">
                            <i class="fas fa-download"></i> Exportar Datos
                        </button>
                        <button class="btn-primary" onclick="viewAllProjects()">
                            <i class="fas fa-list"></i> Ver Lista Completa
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        this.addMapStyles();
    }

    initializeMap() {
        if (this.isInitialized) return;
        
        // Inicializar mapa centrado en Colombia
        this.map = L.map('projectMap').setView([4.5709, -74.2973], 6);
        
        // Agregar tiles del mapa
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(this.map);
        
        // Agregar marcadores de proyectos
        this.addProjectMarkers();
        
        this.isInitialized = true;
        console.log('Mapa inicializado con', this.projectData.length, 'proyectos');
    }

    addProjectMarkers() {
        this.clearMarkers();
        
        this.projectData.forEach(project => {
            if (this.shouldShowProject(project)) {
                const marker = this.createProjectMarker(project);
                this.markers.push(marker);
                marker.addTo(this.map);
            }
        });
        
        this.updateMapStats();
    }

    createProjectMarker(project) {
        // Colores por categoría
        const categoryColors = {
            'STAKING': '#2d5a27',
            'EAR': '#28a745',
            'CROSS FUND': '#17a2b8',
            'TRADING': '#d4af37',
            'FUTUROS': '#ffc107'
        };
        
        const color = categoryColors[project.category] || '#6c757d';
        
        // Crear icono personalizado
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: `
                <div class="marker-container" style="background-color: ${color}">
                    <div class="marker-icon">${project.images[0]}</div>
                    <div class="marker-pulse" style="background-color: ${color}"></div>
                </div>
            `,
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });
        
        const marker = L.marker(project.coordinates, { icon: customIcon });
        
        // Popup con información básica
        const popupContent = `
            <div class="marker-popup">
                <h4>${project.name}</h4>
                <div class="popup-info">
                    <span class="category-badge ${project.category.toLowerCase().replace(' ', '')}">${project.category}</span>
                    <span class="roi-badge">ROI: ${project.roi}%</span>
                </div>
                <p>${project.description}</p>
                <div class="popup-stats">
                    <div class="stat">
                        <i class="fas fa-map-marker-alt"></i>
                        ${project.region}
                    </div>
                    <div class="stat">
                        <i class="fas fa-calendar"></i>
                        ${project.duration}
                    </div>
                </div>
                <button class="popup-btn" onclick="selectProject('${project.id}')">
                    Ver Detalles
                </button>
            </div>
        `;
        
        marker.bindPopup(popupContent, {
            maxWidth: 300,
            className: 'custom-popup'
        });
        
        // Event listener para mostrar detalles
        marker.on('click', () => {
            this.showProjectDetails(project);
        });
        
        return marker;
    }

    showProjectDetails(project) {
        const detailsContainer = document.getElementById('projectDetails');
        
        const statusColors = {
            'active': 'success',
            'processing': 'warning',
            'planning': 'info'
        };
        
        const statusTexts = {
            'active': 'Activo',
            'processing': 'En Proceso',
            'planning': 'En Planificación'
        };
        
        const riskColors = {
            'Bajo': 'success',
            'Medio': 'warning',
            'Alto': 'danger'
        };
        
        detailsContainer.innerHTML = `
            <div class="project-detail-card">
                <div class="project-header">
                    <h4>${project.name}</h4>
                    <span class="status-badge ${statusColors[project.status]}">${statusTexts[project.status]}</span>
                </div>
                
                <div class="project-images">
                    ${project.images.map(img => `<span class="project-emoji">${img}</span>`).join('')}
                </div>
                
                <div class="project-metrics">
                    <div class="metric">
                        <label>Inversión:</label>
                        <span>$${(project.investment / 1000000).toFixed(1)}M</span>
                    </div>
                    <div class="metric">
                        <label>ROI:</label>
                        <span class="roi-value">${project.roi}%</span>
                    </div>
                    <div class="metric">
                        <label>Progreso:</label>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${project.progress}%"></div>
                        </div>
                        <span>${project.progress}%</span>
                    </div>
                </div>
                
                <div class="project-info">
                    <div class="info-row">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${project.region}, ${project.city}</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-thermometer-half"></i>
                        <span>Clima: ${project.climate}</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-expand-arrows-alt"></i>
                        <span>${project.hectares} hectáreas</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-users"></i>
                        <span>${project.farmers} agricultores</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-certificate"></i>
                        <span>${project.certification}</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-shield-alt"></i>
                        <span>Riesgo: <span class="risk-badge ${riskColors[project.riskLevel]}">${project.riskLevel}</span></span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-calendar-check"></i>
                        <span>Próxima cosecha: ${new Date(project.nextHarvest).toLocaleDateString('es-ES')}</span>
                    </div>
                </div>
                
                <div class="project-description">
                    <p>${project.description}</p>
                </div>
                
                <div class="project-actions">
                    <button class="btn-outline btn-sm" onclick="viewProjectHistory('${project.id}')">
                        <i class="fas fa-history"></i> Historial
                    </button>
                    <button class="btn-primary btn-sm" onclick="investInProject('${project.id}')">
                        <i class="fas fa-plus"></i> Invertir
                    </button>
                </div>
            </div>
        `;
    }

    shouldShowProject(project) {
        const categoryFilter = document.getElementById('categoryMapFilter')?.value || 'all';
        const statusFilter = document.getElementById('statusMapFilter')?.value || 'all';
        const roiFilter = parseFloat(document.getElementById('roiMapFilter')?.value || 0);
        
        if (categoryFilter !== 'all' && project.category !== categoryFilter) return false;
        if (statusFilter !== 'all' && project.status !== statusFilter) return false;
        if (project.roi < roiFilter) return false;
        
        return true;
    }

    updateMapStats() {
        const visibleProjects = this.projectData.filter(p => this.shouldShowProject(p));
        const totalInvestment = visibleProjects.reduce((sum, p) => sum + p.investment, 0);
        const avgROI = visibleProjects.reduce((sum, p) => sum + p.roi, 0) / visibleProjects.length;
        
        document.getElementById('totalProjects').textContent = visibleProjects.length;
        document.getElementById('totalInvestment').textContent = `$${(totalInvestment / 1000000).toFixed(1)}M`;
        document.getElementById('avgROI').textContent = `${avgROI.toFixed(1)}%`;
    }

    clearMarkers() {
        this.markers.forEach(marker => {
            this.map.removeLayer(marker);
        });
        this.markers = [];
    }

    filterProjects() {
        this.addProjectMarkers();
    }

    resetFilters() {
        document.getElementById('categoryMapFilter').value = 'all';
        document.getElementById('statusMapFilter').value = 'all';
        document.getElementById('roiMapFilter').value = '0';
        document.getElementById('roiFilterValue').textContent = '0%';
        this.addProjectMarkers();
        
        // Limpiar detalles
        document.getElementById('projectDetails').innerHTML = `
            <div class="no-selection">
                <i class="fas fa-mouse-pointer"></i>
                <p>Haz clic en un marcador para ver detalles del proyecto</p>
            </div>
        `;
    }

    addMapStyles() {
        if (document.getElementById('map-styles')) return;
        
        const styles = document.createElement('style');
        styles.id = 'map-styles';
        styles.textContent = `
            .map-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
                animation: fadeIn 0.3s ease;
            }
            
            .map-overlay {
                background: rgba(0,0,0,0.9);
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            
            .map-container {
                background: white;
                border-radius: 20px;
                max-width: 1400px;
                width: 100%;
                height: 90vh;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                display: flex;
                flex-direction: column;
            }
            
            .map-header {
                background: linear-gradient(135deg, #2d5a27 0%, #3a7233 100%);
                color: white;
                padding: 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .map-title h2 {
                margin: 0 0 0.5rem 0;
                font-size: 1.5rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .map-title p {
                margin: 0;
                opacity: 0.9;
                font-size: 1rem;
            }
            
            .map-close-btn {
                background: rgba(255,255,255,0.2);
                border: none;
                color: white;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 1.25rem;
                transition: all 0.3s ease;
            }
            
            .map-close-btn:hover {
                background: rgba(255,255,255,0.3);
                transform: scale(1.1);
            }
            
            .map-controls {
                padding: 1.5rem 2rem;
                background: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .filter-controls {
                display: flex;
                gap: 2rem;
                align-items: center;
                flex-wrap: wrap;
            }
            
            .filter-group {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .filter-group label {
                font-size: 0.875rem;
                font-weight: 600;
                color: #2d5a27;
            }
            
            .filter-group select,
            .filter-group input {
                padding: 0.5rem;
                border: 2px solid #e9ecef;
                border-radius: 6px;
                font-size: 0.875rem;
                min-width: 120px;
            }
            
            .filter-group select:focus,
            .filter-group input:focus {
                outline: none;
                border-color: #2d5a27;
            }
            
            .map-stats {
                display: flex;
                gap: 2rem;
            }
            
            .stat-item {
                text-align: center;
            }
            
            .stat-value {
                display: block;
                font-size: 1.5rem;
                font-weight: 700;
                color: #2d5a27;
            }
            
            .stat-label {
                font-size: 0.8rem;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .map-content {
                flex: 1;
                display: flex;
                overflow: hidden;
            }
            
            .project-map {
                flex: 1;
                height: 100%;
                position: relative;
            }
            
            .map-sidebar {
                width: 350px;
                background: #f8f9fa;
                border-left: 1px solid #e9ecef;
                display: flex;
                flex-direction: column;
                overflow-y: auto;
            }
            
            .legend {
                padding: 1.5rem;
                border-bottom: 1px solid #e9ecef;
            }
            
            .legend h4 {
                margin: 0 0 1rem 0;
                color: #2d5a27;
                font-size: 1rem;
            }
            
            .legend-items {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .legend-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-size: 0.875rem;
            }
            
            .legend-marker {
                width: 16px;
                height: 16px;
                border-radius: 50%;
            }
            
            .legend-marker.staking { background: #2d5a27; }
            .legend-marker.ear { background: #28a745; }
            .legend-marker.crossfund { background: #17a2b8; }
            .legend-marker.trading { background: #d4af37; }
            .legend-marker.futuros { background: #ffc107; }
            
            .project-details {
                flex: 1;
                padding: 1.5rem;
            }
            
            .no-selection {
                text-align: center;
                color: #6c757d;
                padding: 2rem 1rem;
            }
            
            .no-selection i {
                font-size: 2rem;
                margin-bottom: 1rem;
                opacity: 0.5;
            }
            
            .project-detail-card {
                background: white;
                border-radius: 12px;
                padding: 1.5rem;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            
            .project-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1rem;
            }
            
            .project-header h4 {
                margin: 0;
                color: #2d5a27;
                font-size: 1.1rem;
                line-height: 1.3;
            }
            
            .status-badge {
                padding: 0.25rem 0.75rem;
                border-radius: 12px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
            }
            
            .status-badge.success {
                background: rgba(40, 167, 69, 0.1);
                color: #28a745;
            }
            
            .status-badge.warning {
                background: rgba(255, 193, 7, 0.1);
                color: #ffc107;
            }
            
            .status-badge.info {
                background: rgba(23, 162, 184, 0.1);
                color: #17a2b8;
            }
            
            .project-images {
                display: flex;
                gap: 0.5rem;
                margin-bottom: 1rem;
                justify-content: center;
            }
            
            .project-emoji {
                font-size: 1.5rem;
                padding: 0.5rem;
                background: #f8f9fa;
                border-radius: 8px;
            }
            
            .project-metrics {
                margin-bottom: 1.5rem;
            }
            
            .metric {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.75rem;
                font-size: 0.9rem;
            }
            
            .metric label {
                font-weight: 600;
                color: #6c757d;
            }
            
            .roi-value {
                color: #28a745;
                font-weight: 700;
            }
            
            .progress-bar {
                width: 60px;
                height: 6px;
                background: #e9ecef;
                border-radius: 3px;
                overflow: hidden;
                margin: 0 0.5rem;
            }
            
            .progress-fill {
                height: 100%;
                background: #28a745;
                transition: width 0.3s ease;
            }
            
            .project-info {
                margin-bottom: 1.5rem;
            }
            
            .info-row {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 0.5rem;
                font-size: 0.875rem;
            }
            
            .info-row i {
                width: 16px;
                color: #2d5a27;
            }
            
            .risk-badge {
                padding: 0.125rem 0.5rem;
                border-radius: 8px;
                font-size: 0.75rem;
                font-weight: 600;
            }
            
            .risk-badge.success {
                background: rgba(40, 167, 69, 0.1);
                color: #28a745;
            }
            
            .risk-badge.warning {
                background: rgba(255, 193, 7, 0.1);
                color: #ffc107;
            }
            
            .risk-badge.danger {
                background: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }
            
            .project-description {
                margin-bottom: 1.5rem;
                padding: 1rem;
                background: #f8f9fa;
                border-radius: 8px;
                font-size: 0.875rem;
                line-height: 1.5;
            }
            
            .project-actions {
                display: flex;
                gap: 0.75rem;
            }
            
            .map-footer {
                padding: 1.5rem 2rem;
                border-top: 1px solid #e9ecef;
                display: flex;
                gap: 1rem;
                justify-content: flex-end;
            }
            
            /* Estilos para marcadores personalizados */
            .custom-marker {
                background: transparent !important;
                border: none !important;
            }
            
            .marker-container {
                position: relative;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                cursor: pointer;
                transition: transform 0.3s ease;
            }
            
            .marker-container:hover {
                transform: scale(1.1);
            }
            
            .marker-icon {
                font-size: 1.25rem;
                z-index: 2;
            }
            
            .marker-pulse {
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 50%;
                opacity: 0.3;
                animation: pulse-marker 2s infinite;
            }
            
            @keyframes pulse-marker {
                0% {
                    transform: scale(1);
                    opacity: 0.3;
                }
                50% {
                    transform: scale(1.2);
                    opacity: 0.1;
                }
                100% {
                    transform: scale(1);
                    opacity: 0.3;
                }
            }
            
            /* Estilos para popups */
            .custom-popup .leaflet-popup-content-wrapper {
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            }
            
            .marker-popup h4 {
                margin: 0 0 0.75rem 0;
                color: #2d5a27;
                font-size: 1rem;
            }
            
            .popup-info {
                display: flex;
                gap: 0.5rem;
                margin-bottom: 0.75rem;
            }
            
            .category-badge {
                padding: 0.25rem 0.5rem;
                border-radius: 8px;
                font-size: 0.7rem;
                font-weight: 600;
                text-transform: uppercase;
            }
            
            .category-badge.staking { background: rgba(45, 90, 39, 0.1); color: #2d5a27; }
            .category-badge.ear { background: rgba(40, 167, 69, 0.1); color: #28a745; }
            .category-badge.crossfund { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }
            .category-badge.trading { background: rgba(212, 175, 55, 0.1); color: #d4af37; }
            .category-badge.futuros { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
            
            .roi-badge {
                padding: 0.25rem 0.5rem;
                border-radius: 8px;
                font-size: 0.7rem;
                font-weight: 600;
                background: rgba(40, 167, 69, 0.1);
                color: #28a745;
            }
            
            .popup-stats {
                display: flex;
                gap: 1rem;
                margin-bottom: 0.75rem;
                font-size: 0.8rem;
                color: #6c757d;
            }
            
            .stat {
                display: flex;
                align-items: center;
                gap: 0.25rem;
            }
            
            .popup-btn {
                width: 100%;
                padding: 0.5rem;
                background: #2d5a27;
                color: white;
                border: none;
                border-radius: 6px;
                font-size: 0.875rem;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            
            .popup-btn:hover {
                background: #3a7233;
            }
            
            /* Responsive */
            @media (max-width: 1200px) {
                .map-container {
                    max-width: 100%;
                    height: 95vh;
                }
                
                .map-sidebar {
                    width: 300px;
                }
            }
            
            @media (max-width: 768px) {
                .map-content {
                    flex-direction: column;
                }
                
                .map-sidebar {
                    width: 100%;
                    height: 300px;
                    border-left: none;
                    border-top: 1px solid #e9ecef;
                }
                
                .filter-controls {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 1rem;
                }
                
                .map-stats {
                    flex-direction: column;
                    gap: 0.5rem;
                }
            }
        `;
        document.head.appendChild(styles);
    }

    show() {
        const modal = document.querySelector('.map-modal');
        if (modal) {
            modal.style.display = 'block';
            
            // Inicializar mapa después de que el modal sea visible
            setTimeout(() => {
                this.initializeMap();
            }, 300);
        }
    }

    hide() {
        const modal = document.querySelector('.map-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }
}

// Funciones globales
function openProjectMap() {
    if (!window.projectMap) {
        window.projectMap = new InteractiveProjectMap();
    }
    window.projectMap.show();
}

function closeProjectMap() {
    if (window.projectMap) {
        window.projectMap.hide();
    }
}

function filterMapProjects(category) {
    if (window.projectMap) {
        window.projectMap.filterProjects();
    }
}

function updateROIFilter(value) {
    document.getElementById('roiFilterValue').textContent = `${value}%`;
}

function resetMapFilters() {
    if (window.projectMap) {
        window.projectMap.resetFilters();
    }
    AGROMARKET.showNotification('Filtros restablecidos', 'info');
}

function selectProject(projectId) {
    console.log('Proyecto seleccionado:', projectId);
    // La función showProjectDetails ya se ejecuta al hacer clic en el marcador
}

function viewProjectHistory(projectId) {
    AGROMARKET.showNotification(`Cargando historial del proyecto ${projectId}...`, 'info');
}

function investInProject(projectId) {
    closeProjectMap();
    AGROMARKET.showNotification('Redirigiendo a página de inversión...', 'info');
    
    setTimeout(() => {
        window.location.href = 'categorias.html';
    }, 2000);
}

function exportMapData() {
    AGROMARKET.showNotification('Exportando datos del mapa...', 'info');
    
    setTimeout(() => {
        AGROMARKET.showNotification('Datos exportados exitosamente', 'success');
    }, 2000);
}

function viewAllProjects() {
    closeProjectMap();
    window.location.href = 'categorias.html';
}

// Exportar para uso global
window.openProjectMap = openProjectMap;
window.closeProjectMap = closeProjectMap;
window.filterMapProjects = filterMapProjects;
window.updateROIFilter = updateROIFilter;
window.resetMapFilters = resetMapFilters;
window.selectProject = selectProject;
window.viewProjectHistory = viewProjectHistory;
window.investInProject = investInProject;
window.exportMapData = exportMapData;
window.viewAllProjects = viewAllProjects;

console.log('Interactive Project Map listo para usar');
