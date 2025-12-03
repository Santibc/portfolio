// AGROMARKET - Dashboard

document.addEventListener('DOMContentLoaded', function() {
    initDashboard();
    initSidebar();
    initCharts();
    initUserMenu();
    // checkAuthRequired(); // DESHABILITADO - Laravel maneja la autenticación

    console.log('Dashboard inicializado correctamente');
});

// Inicializar dashboard
function initDashboard() {
    // Cargar datos del usuario desde localStorage/sessionStorage
    // loadUserData(); // DESHABILITADO - Los datos del usuario vienen de Laravel/Blade

    // Inicializar componentes del dashboard
    initSummaryCards();
    initInvestmentTable();
    initActivityFeed();
    initNotifications();
}

// Cargar datos del usuario
function loadUserData() {
    const userSession = localStorage.getItem('agromarket_user') || sessionStorage.getItem('agromarket_user');
    
    if (userSession) {
        const user = JSON.parse(userSession);
        updateUserInterface(user);
        AGROMARKET.AppState.setUser(user);
    }
}

// Actualizar interfaz con datos del usuario
function updateUserInterface(user) {
    const userName = document.querySelector('.user-name');
    const userRole = document.querySelector('.user-role');
    
    if (userName) userName.textContent = user.name || 'Usuario';
    if (userRole) userRole.textContent = getRoleDisplayName(user.type) || 'Inversionista';
}

// Obtener nombre de rol para mostrar
function getRoleDisplayName(type) {
    const roles = {
        'inversionista': 'Inversionista',
        'agricultor': 'Agricultor',
        'admin': 'Administrador',
        'supervisor': 'Supervisor'
    };
    return roles[type] || 'Usuario';
}

// Inicializar sidebar
function initSidebar() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const navLinks = document.querySelectorAll('.nav-link');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            // En desktop: colapsar/expandir
            if (window.innerWidth > 968) {
                sidebar.classList.toggle('collapsed');
                if (mainContent) {
                    mainContent.classList.toggle('sidebar-collapsed');
                }
                
                // Guardar estado en localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            } else {
                // En móvil: mostrar/ocultar
                sidebar.classList.toggle('active');
            }
        });
        
        // Restaurar estado del sidebar al cargar
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true' && window.innerWidth > 968) {
            sidebar.classList.add('collapsed');
            if (mainContent) {
                mainContent.classList.add('sidebar-collapsed');
            }
        }
        
        // Cerrar sidebar al hacer click fuera (móvil)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 968 && 
                !sidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
        
        // Manejar resize de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth > 968) {
                sidebar.classList.remove('active');
                // Restaurar estado colapsado en desktop
                const savedState = localStorage.getItem('sidebarCollapsed');
                if (savedState === 'true') {
                    sidebar.classList.add('collapsed');
                    if (mainContent) {
                        mainContent.classList.add('sidebar-collapsed');
                    }
                }
            } else {
                sidebar.classList.remove('collapsed');
                if (mainContent) {
                    mainContent.classList.remove('sidebar-collapsed');
                }
            }
        });
    }
    
    // Manejar navegación
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const linkText = this.querySelector('span') ? this.querySelector('span').textContent : 'Página';
            
            // Solo prevenir default para enlaces sin href válido o configuración
            if (!href || href === '#' || linkText === 'Configuración') {
                e.preventDefault();
                AGROMARKET.showNotification(`${linkText} - Próximamente`, 'info');
            } else {
                // Para enlaces válidos, permitir navegación normal
                console.log(`Navegando a: ${href}`);
            }
            
            // Actualizar estado activo
            navLinks.forEach(l => l.parentElement.classList.remove('active'));
            this.parentElement.classList.add('active');
            
            // Cerrar sidebar en móvil
            if (window.innerWidth <= 968) {
                sidebar.classList.remove('active');
            }
        });
    });
    
    // Manejar logout
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
}

// Manejar logout
function handleLogout() {
    if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        // Limpiar datos de sesión
        localStorage.removeItem('agromarket_user');
        sessionStorage.removeItem('agromarket_user');
        
        // Actualizar estado de la aplicación
        AGROMARKET.AppState.logout();
        
        // Redireccionar al login
        window.location.href = 'login.html';
    }
}

// Inicializar tarjetas de resumen
function initSummaryCards() {
    const summaryCards = document.querySelectorAll('.summary-card');
    
    // Animar números al hacer scroll
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const card = entry.target;
                const valueElement = card.querySelector('.card-value');
                if (valueElement && !valueElement.hasAttribute('data-animated')) {
                    animateCardValue(valueElement);
                    valueElement.setAttribute('data-animated', 'true');
                }
            }
        });
    }, { threshold: 0.5 });
    
    summaryCards.forEach(card => observer.observe(card));
}

// Animar valor de tarjeta
function animateCardValue(element) {
    const finalValue = element.textContent.trim();

    // No animar si tiene sufijos compactos (M para millones, K para miles)
    if (finalValue.includes('M') || finalValue.includes('K')) {
        return; // Dejar el valor original sin animar
    }

    // Detectar prefijos y sufijos ANTES de extraer el número
    const isMonetary = finalValue.includes('$');
    const hasPercent = finalValue.includes('%');

    // Extraer cualquier sufijo de texto (como " meses", " días", etc.)
    let textSuffix = '';
    const match = finalValue.match(/(\d+(?:[.,]\d+)?)\s*(.*)$/);
    if (match && match[2]) {
        textSuffix = match[2].replace('$', '').replace('%', '').trim();
        // Si el sufijo restante es solo texto, conservarlo
        if (textSuffix && !/^\d/.test(textSuffix)) {
            textSuffix = ' ' + textSuffix;
        } else {
            textSuffix = '';
        }
    }

    // Extraer el valor numérico distinguiendo separadores de miles vs decimales
    let cleanValue = finalValue;

    // Determinar si tiene símbolo de moneda
    const hasMonetary = cleanValue.includes('$');

    if (cleanValue.includes('.') && cleanValue.includes(',')) {
        // Ambos punto y coma: detectar formato
        // Formato español: $1.000,50 (punto=miles, coma=decimal) → 1000.50
        // Formato inglés: $1,000.50 (coma=miles, punto=decimal) → 1000.50
        const dotIndex = cleanValue.indexOf('.');
        const commaIndex = cleanValue.indexOf(',');

        if (dotIndex < commaIndex) {
            // Punto antes que coma: formato español $1.000,50
            cleanValue = cleanValue.replace(/\./g, '').replace(',', '.');
        } else {
            // Coma antes que punto: formato inglés $1,000.50
            cleanValue = cleanValue.replace(/,/g, '');
        }
    } else if (cleanValue.includes(',') && !cleanValue.includes('.')) {
        // Solo coma: puede ser separador de miles (inglés) o decimal (español)
        const parts = cleanValue.match(/(\d+),(\d+)/);
        if (parts) {
            const afterComma = parts[2];
            // Si tiene exactamente 3 dígitos después de la coma Y tiene $, es separador de miles
            // Ej: $500,000 → 500000
            if (afterComma.length === 3 && hasMonetary) {
                cleanValue = cleanValue.replace(/,/g, '');
            } else {
                // Es decimal: 10,5% → 10.5%
                cleanValue = cleanValue.replace(',', '.');
            }
        }
    } else if (cleanValue.includes('.')) {
        // Solo punto: detectar si es miles o decimal
        const parts = cleanValue.match(/(\d+)\.(\d+)/);
        if (parts) {
            const afterDot = parts[2];
            // Si tiene exactamente 3 dígitos después del punto Y tiene $, es separador de miles
            // Ej: $1.000 → 1000
            if (afterDot.length === 3 && hasMonetary) {
                cleanValue = cleanValue.replace(/\./g, '');
            }
            // Si tiene 1-2 dígitos después del punto, es decimal
            // Ej: 10.00% → 10.00
            // Dejar como está
        }
    }

    let numericValue = parseFloat(cleanValue.replace(/[^0-9.-]/g, ''));

    // Si no es un número válido, no animar (evita NaN en textos como "Borrador", "En Revisión", etc.)
    if (isNaN(numericValue) || numericValue === 0) {
        return; // Dejar el valor original sin animar
    }

    // Si el valor es muy grande, no animar para mejor rendimiento
    if (numericValue > 100000) {
        return; // Dejar el valor original sin animar
    }

    let current = 0;
    const increment = numericValue / 30;

    const timer = setInterval(() => {
        current += increment;
        if (current >= numericValue) {
            current = numericValue;
            clearInterval(timer);
        }

        let displayValue = Math.floor(current);
        let prefix = '';
        let suffix = '';

        if (isMonetary) {
            prefix = '$';
            // Formatear con separadores de miles en español (punto)
            displayValue = displayValue.toLocaleString('es-CO');
        }

        if (hasPercent) suffix = '%';

        // Agregar el sufijo de texto si existe (como " meses")
        suffix += textSuffix;

        element.textContent = prefix + displayValue + suffix;
    }, 50);
}

// Inicializar gráficos
function initCharts() {
    initPortfolioChart();
    initPeriodButtons();
    animateDistributionBars();
}

// Inicializar gráfico de portafolio
function initPortfolioChart() {
    const chartContainer = document.getElementById('portfolioChart');
    if (!chartContainer) return;
    
    // Aquí se implementaría un gráfico real (Chart.js, D3.js, etc.)
    // Por ahora, mostrar placeholder
    chartContainer.style.background = 'linear-gradient(45deg, #f8f9fa, #e9ecef)';
    chartContainer.style.display = 'flex';
    chartContainer.style.alignItems = 'center';
    chartContainer.style.justifyContent = 'center';
    chartContainer.style.color = '#6c757d';
    chartContainer.style.fontSize = '1.1rem';
    chartContainer.innerHTML = '<i class="fas fa-chart-line" style="margin-right: 0.5rem;"></i>Gráfico de evolución del portafolio';
}

// Inicializar botones de período
function initPeriodButtons() {
    const periodButtons = document.querySelectorAll('.chart-period');
    
    periodButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remover clase active de todos los botones
            periodButtons.forEach(btn => btn.classList.remove('active'));
            
            // Agregar clase active al botón clickeado
            this.classList.add('active');
            
            // Simular actualización del gráfico
            const period = this.getAttribute('data-period');
            updateChartPeriod(period);
        });
    });
}

// Actualizar período del gráfico
function updateChartPeriod(period) {
    AGROMARKET.showNotification(`Gráfico actualizado para: ${period}`, 'info');
    // Aquí se implementaría la lógica real de actualización del gráfico
}

// Animar barras de distribución
function animateDistributionBars() {
    const distributionBars = document.querySelectorAll('.bar-fill');
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width;
                bar.style.width = '0%';
                
                setTimeout(() => {
                    bar.style.width = width;
                }, 300);
                
                observer.unobserve(bar);
            }
        });
    }, { threshold: 0.5 });
    
    distributionBars.forEach(bar => observer.observe(bar));
}

// Inicializar tabla de inversiones
function initInvestmentTable() {
    const actionButtons = document.querySelectorAll('.btn-icon');
    
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('title');
            const row = this.closest('.table-row');
            const projectName = row.querySelector('.project-name').textContent;
            
            handleInvestmentAction(action, projectName);
        });
    });
    
    // Simular datos dinámicos
    updateInvestmentProgress();
}

// Manejar acciones de inversión
function handleInvestmentAction(action, projectName) {
    if (action === 'Ver detalles') {
        AGROMARKET.showNotification(`Abriendo detalles de: ${projectName}`, 'info');
        // Aquí se abriría un modal o se navegaría a la página de detalles
    } else if (action === 'Trading') {
        AGROMARKET.showNotification(`Iniciando trading para: ${projectName}`, 'info');
        // Aquí se abriría la interfaz de trading
    }
}

// Actualizar progreso de inversiones
function updateInvestmentProgress() {
    const progressBars = document.querySelectorAll('.progress-fill');
    
    // Simular actualización periódica del progreso
    setInterval(() => {
        progressBars.forEach(bar => {
            const currentWidth = parseInt(bar.style.width);
            if (currentWidth < 100) {
                const newWidth = Math.min(currentWidth + Math.random(), 100);
                bar.style.width = newWidth + '%';
                
                // Actualizar texto de progreso
                const progressText = bar.parentElement.nextElementSibling;
                if (progressText && progressText.classList.contains('progress-text')) {
                    progressText.textContent = Math.floor(newWidth) + '%';
                }
            }
        });
    }, 30000); // Actualizar cada 30 segundos
}

// Inicializar feed de actividad
function initActivityFeed() {
    // Simular nuevas actividades
    setTimeout(() => {
        addNewActivity();
    }, 10000); // Agregar nueva actividad después de 10 segundos
}

// Agregar nueva actividad
function addNewActivity() {
    const activityList = document.querySelector('.activity-list');
    if (!activityList) return;
    
    const newActivity = createActivityItem({
        type: 'success',
        icon: 'fas fa-arrow-up',
        title: 'Nuevo dividendo',
        description: 'Proyecto: Aguacates Premium',
        time: 'Ahora',
        amount: '+$320'
    });
    
    // Agregar al inicio de la lista
    activityList.insertBefore(newActivity, activityList.firstChild);
    
    // Animar entrada
    newActivity.style.opacity = '0';
    newActivity.style.transform = 'translateY(-20px)';
    
    setTimeout(() => {
        newActivity.style.transition = 'all 0.3s ease';
        newActivity.style.opacity = '1';
        newActivity.style.transform = 'translateY(0)';
    }, 100);
    
    // Mostrar notificación
    AGROMARKET.showNotification('¡Nuevo dividendo recibido!', 'success');
}

// Crear elemento de actividad
function createActivityItem(data) {
    const activityItem = document.createElement('div');
    activityItem.className = 'activity-item';
    
    activityItem.innerHTML = `
        <div class="activity-icon ${data.type}">
            <i class="${data.icon}"></i>
        </div>
        <div class="activity-content">
            <div class="activity-title">${data.title}</div>
            <div class="activity-description">${data.description}</div>
            <div class="activity-time">${data.time}</div>
        </div>
        <div class="activity-amount ${data.amount.startsWith('+') ? 'positive' : 'negative'}">${data.amount}</div>
    `;
    
    return activityItem;
}

// Inicializar notificaciones
function initNotifications() {
    const notificationBtn = document.querySelector('.notification-btn');
    
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            toggleNotificationPanel();
        });
    }
    
    // Simular nuevas notificaciones
    setInterval(() => {
        updateNotificationBadge();
    }, 60000); // Actualizar cada minuto
}

// Toggle del panel de notificaciones
function toggleNotificationPanel() {
    // Aquí se implementaría un panel de notificaciones
    AGROMARKET.showNotification('Panel de notificaciones - Próximamente', 'info');
    
    // Limpiar badge
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.textContent = '0';
        badge.style.display = 'none';
    }
}

// Actualizar badge de notificaciones
function updateNotificationBadge() {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        const currentCount = parseInt(badge.textContent) || 0;
        const newCount = currentCount + Math.floor(Math.random() * 3);
        
        if (newCount > 0) {
            badge.textContent = newCount;
            badge.style.display = 'block';
        }
    }
}

// Inicializar menú de usuario
function initUserMenu() {
    const userMenu = document.querySelector('.user-menu');
    
    if (userMenu) {
        userMenu.addEventListener('click', function() {
            toggleUserDropdown();
        });
        
        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!userMenu.contains(e.target)) {
                closeUserDropdown();
            }
        });
    }
}

// Toggle dropdown de usuario
function toggleUserDropdown() {
    // Aquí se implementaría un dropdown de usuario
    AGROMARKET.showNotification('Menú de usuario - Próximamente', 'info');
}

// Cerrar dropdown de usuario
function closeUserDropdown() {
    // Implementar cierre del dropdown
}

// Verificar autenticación requerida
function checkAuthRequired() {
    const userSession = localStorage.getItem('agromarket_user') || sessionStorage.getItem('agromarket_user');
    
    if (!userSession) {
        AGROMARKET.showNotification('Sesión expirada. Redirigiendo al login...', 'warning');
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 2000);
        return;
    }
    
    const user = JSON.parse(userSession);
    
    // Verificar tipo de usuario para dashboard específico
    if (user.type === 'agricultor' && !window.location.pathname.includes('dashboard-agricultor')) {
        window.location.href = 'dashboard-agricultor.html';
        return;
    }
    
    if (user.type !== 'agricultor' && window.location.pathname.includes('dashboard-agricultor')) {
        window.location.href = 'dashboard.html';
        return;
    }
}

// Utilidades del dashboard
const DashboardUtils = {
    formatCurrency(amount) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },
    
    formatPercentage(value) {
        return new Intl.NumberFormat('es-CO', {
            style: 'percent',
            minimumFractionDigits: 1,
            maximumFractionDigits: 1
        }).format(value / 100);
    },
    
    formatDate(date) {
        return new Intl.DateTimeFormat('es-CO', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }).format(new Date(date));
    },
    
    calculateROI(investment, returns) {
        return ((returns - investment) / investment) * 100;
    },
    
    getProjectStatusColor(status) {
        const colors = {
            'active': '#28a745',
            'pending': '#ffc107',
            'completed': '#17a2b8',
            'cancelled': '#dc3545'
        };
        return colors[status] || '#6c757d';
    }
};

// Manejo de errores del dashboard
window.addEventListener('error', function(e) {
    console.error('Error en dashboard:', e.error);
    
    // Solo mostrar notificación, no recargar automáticamente
    if (e.error && e.error.message && !e.error.message.includes('Chart')) {
        AGROMARKET.showNotification('Se detectó un error menor. El dashboard sigue funcionando.', 'warning');
    }
});

// Manejo de resize para responsive
window.addEventListener('resize', AGROMARKET.debounce(function() {
    const sidebar = document.querySelector('.sidebar');
    
    if (window.innerWidth > 968) {
        sidebar.classList.remove('active');
    }
}, 250));

// Exportar utilidades
window.DashboardUtils = DashboardUtils;
