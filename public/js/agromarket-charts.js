// AGROMARKET - Gráficos Interactivos Avanzados

// Configuración global de Chart.js
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#6c757d';
Chart.defaults.borderColor = '#e9ecef';

// Colores del tema AGROMARKET
const COLORS = {
    primary: '#2d5a27',
    secondary: '#d4af37',
    success: '#28a745',
    warning: '#ffc107',
    danger: '#dc3545',
    info: '#17a2b8',
    light: '#f8f9fa',
    dark: '#343a40'
};

// Variables globales para los gráficos
let portfolioChart = null;
let performanceChart = null;
let distributionChart = null;
let chartsInitialized = false;

// Inicializar todos los gráficos
function initCharts() {
    // Evitar múltiples inicializaciones
    if (chartsInitialized) {
        console.log('Gráficos ya inicializados, omitiendo...');
        return;
    }
    
    // Destruir gráficos existentes antes de crear nuevos
    destroyExistingCharts();
    
    initPortfolioEvolutionChart();
    initPerformanceChart();
    initDistributionChart();
    initMiniCharts();
    
    chartsInitialized = true;
    window.chartsInitialized = true;
    console.log('Gráficos interactivos inicializados');
}

// Destruir gráficos existentes
function destroyExistingCharts() {
    if (portfolioChart) {
        portfolioChart.destroy();
        portfolioChart = null;
    }
    if (performanceChart) {
        performanceChart.destroy();
        performanceChart = null;
    }
    if (distributionChart) {
        distributionChart.destroy();
        distributionChart = null;
    }
}

// Gráfico de evolución del portafolio
function initPortfolioEvolutionChart() {
    const ctx = document.getElementById('portfolioEvolutionChart');
    if (!ctx) return;

    // Datos simulados de evolución
    const data = generatePortfolioData();

    portfolioChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Valor del Portafolio',
                data: data.values,
                borderColor: COLORS.primary,
                backgroundColor: `${COLORS.primary}20`,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: COLORS.primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8
            }, {
                label: 'Capital Invertido',
                data: data.invested,
                borderColor: COLORS.info,
                backgroundColor: `${COLORS.info}10`,
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                borderDash: [5, 5]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: COLORS.primary,
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: $${context.parsed.y.toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxTicksLimit: 8
                    }
                },
                y: {
                    beginAtZero: false,
                    grid: {
                        color: '#f1f3f4'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Gráfico de rendimiento por categoría
function initPerformanceChart() {
    const ctx = document.getElementById('performanceChart');
    if (!ctx) return;

    const data = {
        labels: ['STAKING', 'EAR', 'CROSS FUND', 'TRADING', 'FUTUROS'],
        datasets: [{
            label: 'ROI (%)',
            data: [28.5, 12.3, 15.8, 8.7, 22.1],
            backgroundColor: [
                `${COLORS.primary}80`,
                `${COLORS.success}80`,
                `${COLORS.info}80`,
                `${COLORS.secondary}80`,
                `${COLORS.warning}80`
            ],
            borderColor: [
                COLORS.primary,
                COLORS.success,
                COLORS.info,
                COLORS.secondary,
                COLORS.warning
            ],
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false
        }]
    };

    performanceChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    callbacks: {
                        label: function(context) {
                            return `ROI: ${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f3f4'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeOutBounce'
            }
        }
    });
}

// Gráfico de distribución (dona)
function initDistributionChart() {
    const ctx = document.getElementById('distributionChart');
    if (!ctx) return;

    const data = {
        labels: ['STAKING', 'EAR', 'CROSS FUND', 'TRADING'],
        datasets: [{
            data: [45, 25, 20, 10],
            backgroundColor: [
                COLORS.primary,
                COLORS.success,
                COLORS.info,
                COLORS.secondary
            ],
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 10
        }]
    };

    distributionChart = new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const dataset = data.datasets[0];
                                    const value = dataset.data[i];
                                    return {
                                        text: `${label} (${value}%)`,
                                        fillStyle: dataset.backgroundColor[i],
                                        strokeStyle: dataset.borderColor,
                                        lineWidth: dataset.borderWidth,
                                        pointStyle: 'circle',
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.parsed}% del portafolio`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 2000
            }
        }
    });
}

// Mini gráficos para las tarjetas
function initMiniCharts() {
    // Mini gráfico de tendencia para ROI
    const roiCtx = document.getElementById('roiTrendChart');
    if (roiCtx) {
        new Chart(roiCtx, {
            type: 'line',
            data: {
                labels: ['', '', '', '', '', '', ''],
                datasets: [{
                    data: [10, 12, 11, 13, 12, 14, 13.5],
                    borderColor: COLORS.success,
                    backgroundColor: `${COLORS.success}20`,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                },
                elements: {
                    point: { radius: 0 }
                }
            }
        });
    }

    // Mini gráfico para balance
    const balanceCtx = document.getElementById('balanceTrendChart');
    if (balanceCtx) {
        new Chart(balanceCtx, {
            type: 'bar',
            data: {
                labels: ['', '', '', '', '', '', ''],
                datasets: [{
                    data: [3000, 3200, 3100, 3400, 3300, 3500, 3247],
                    backgroundColor: COLORS.primary,
                    borderRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
    }
}

// Generar datos de evolución del portafolio
function generatePortfolioData() {
    const labels = [];
    const values = [];
    const invested = [];
    
    const startDate = new Date();
    startDate.setMonth(startDate.getMonth() - 6);
    
    let currentValue = 10000;
    let currentInvested = 10000;
    
    for (let i = 0; i < 30; i++) {
        const date = new Date(startDate);
        date.setDate(date.getDate() + (i * 6));
        labels.push(date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' }));
        
        // Simular crecimiento con volatilidad
        const growth = (Math.random() - 0.3) * 200 + 50;
        currentValue += growth;
        
        // Simular nuevas inversiones ocasionales
        if (Math.random() > 0.8) {
            currentInvested += Math.random() * 1000 + 500;
        }
        
        values.push(Math.round(currentValue));
        invested.push(Math.round(currentInvested));
    }
    
    return { labels, values, invested };
}

// Actualizar gráficos con nuevos datos
function updateCharts() {
    if (portfolioChart) {
        const newData = generatePortfolioData();
        portfolioChart.data.labels = newData.labels;
        portfolioChart.data.datasets[0].data = newData.values;
        portfolioChart.data.datasets[1].data = newData.invested;
        portfolioChart.update('active');
    }
}

// Cambiar período de tiempo en gráficos
function changeChartPeriod(period) {
    console.log(`Cambiando período a: ${period}`);
    
    // Simular cambio de datos según el período
    let dataPoints;
    switch(period) {
        case '1M': dataPoints = 30; break;
        case '3M': dataPoints = 90; break;
        case '6M': dataPoints = 180; break;
        case '1Y': dataPoints = 365; break;
        default: dataPoints = 30;
    }
    
    // Actualizar gráficos con nuevos datos
    updateCharts();
    
    AGROMARKET.showNotification(`Gráficos actualizados para período: ${period}`, 'info');
}

// Exportar funciones globales
window.initCharts = initCharts;
window.updateCharts = updateCharts;
window.changeChartPeriod = changeChartPeriod;
window.chartsInitialized = chartsInitialized;

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Solo inicializar si estamos en una página con gráficos
    if (document.getElementById('portfolioEvolutionChart')) {
        // Esperar un poco para que Chart.js se cargue completamente
        setTimeout(() => {
            if (!chartsInitialized) {
                initCharts();
            }
        }, 1000);
    }
});
