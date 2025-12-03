// AGROMARKET - JavaScript Principal

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todas las funcionalidades
    initNavigation();
    initMobileMenu();
    initScrollEffects();
    initCategoryCards();
    initStats();
    initTestimonials();
    
    console.log('AGROMARKET inicializado correctamente');
});

// Navegación suave
function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                const headerHeight = document.querySelector('.header').offsetHeight;
                const targetPosition = targetSection.offsetTop - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Actualizar enlace activo
                updateActiveNavLink(this);
            }
        });
    });
    
    // Manejar enlaces externos (páginas)
    const pageLinks = document.querySelectorAll('.nav-link[href$=".html"]');
    pageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Permitir navegación normal para enlaces de página
            return true;
        });
    });
}

// Actualizar enlace activo en navegación
function updateActiveNavLink(activeLink) {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => link.classList.remove('active'));
    activeLink.classList.add('active');
}

// Menú móvil
function initMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Cerrar menú al hacer click en un enlace
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
}

// Efectos de scroll
function initScrollEffects() {
    // Header con sombra al hacer scroll
    const header = document.querySelector('.header') || document.querySelector('.dashboard-header');
    
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }
    
    // Animación de elementos al aparecer en viewport
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observar elementos que necesitan animación
    const animatedElements = document.querySelectorAll('.category-card, .testimonial, .step, .stat-item');
    animatedElements.forEach(el => observer.observe(el));
}

// Interactividad de las tarjetas de categoría
function initCategoryCards() {
    const categoryCards = document.querySelectorAll('.category-card');
    
    categoryCards.forEach(card => {
        const button = card.querySelector('.btn-category');
        
        if (button) {
            button.addEventListener('click', function() {
                const categoryTitle = card.querySelector('.category-title').textContent;
                handleCategorySelection(categoryTitle);
            });
        }
        
        // Efecto hover mejorado
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

// Manejar selección de categoría
function handleCategorySelection(category) {
    // Simulación de navegación a página de categoría
    console.log(`Navegando a categoría: ${category}`);
    
    // Aquí se podría implementar la navegación real
    // Por ahora, mostramos un mensaje
    showNotification(`Explorando proyectos de ${category}`, 'info');
}

// Animación de contadores en estadísticas
function initStats() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const finalValue = target.textContent.trim();
                animateCounter(target, finalValue);
                statsObserver.unobserve(target);
            }
        });
    }, { threshold: 0.5 });
    
    statNumbers.forEach(stat => statsObserver.observe(stat));
}

// Animar contador
function animateCounter(element, finalValue) {
    const isMonetary = finalValue.includes('$');
    const hasPlus = finalValue.includes('+');
    const hasPercent = finalValue.includes('%');

    let numericValue = parseFloat(finalValue.replace(/[^0-9.]/g, ''));

    // Si no es un número válido, no animar (evita NaN en textos)
    if (isNaN(numericValue) || numericValue === 0) {
        element.textContent = finalValue;
        return;
    }

    if (finalValue.includes('M')) numericValue *= 1000000;
    if (finalValue.includes('k') || finalValue.includes('K')) numericValue *= 1000;
    
    let current = 0;
    const increment = numericValue / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= numericValue) {
            current = numericValue;
            clearInterval(timer);
        }
        
        let displayValue = current;
        let suffix = '';
        
        if (current >= 1000000) {
            displayValue = (current / 1000000).toFixed(1);
            suffix = 'M';
        } else if (current >= 1000) {
            displayValue = (current / 1000).toFixed(0);
            suffix = 'k';
        } else {
            displayValue = Math.floor(current);
        }
        
        let prefix = '';
        if (isMonetary) prefix = '$';
        
        let postfix = '';
        if (hasPlus) postfix += '+';
        if (hasPercent) postfix += '%';
        
        element.textContent = prefix + displayValue + suffix + postfix;
    }, 30);
}

// Rotación automática de testimonios
function initTestimonials() {
    const testimonials = document.querySelectorAll('.testimonial');
    
    if (testimonials.length > 0) {
        let currentTestimonial = 0;
        
        // Agregar indicadores de navegación
        const testimonialsContainer = document.querySelector('.testimonials-grid');
        const indicatorsContainer = document.createElement('div');
        indicatorsContainer.className = 'testimonial-indicators';
        
        testimonials.forEach((_, index) => {
            const indicator = document.createElement('button');
            indicator.className = 'testimonial-indicator';
            if (index === 0) indicator.classList.add('active');
            indicator.addEventListener('click', () => showTestimonial(index));
            indicatorsContainer.appendChild(indicator);
        });
        
        testimonialsContainer.parentNode.appendChild(indicatorsContainer);
        
        function showTestimonial(index) {
            const indicators = document.querySelectorAll('.testimonial-indicator');
            indicators.forEach((indicator, i) => {
                indicator.classList.toggle('active', i === index);
            });
            
            currentTestimonial = index;
        }
        
        // Auto-rotación cada 5 segundos
        setInterval(() => {
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            showTestimonial(currentTestimonial);
        }, 5000);
    }
}

// Sistema de notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Estilos CSS inline para la notificación
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '1rem 1.5rem',
        borderRadius: '8px',
        color: 'white',
        zIndex: '10000',
        transform: 'translateX(400px)',
        transition: 'transform 0.3s ease',
        maxWidth: '300px',
        wordWrap: 'break-word'
    });
    
    // Colores según el tipo
    const colors = {
        info: '#2D5A27',
        success: '#28A745',
        warning: '#FFC107',
        error: '#DC3545'
    };
    
    notification.style.background = colors[type] || colors.info;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Animar salida y remover
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Formularios (para futuras implementaciones)
function initForms() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this);
        });
    });
}

function handleFormSubmit(form) {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    console.log('Datos del formulario:', data);
    showNotification('Formulario enviado correctamente', 'success');
}

// Validación de campos
function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    
    if (required && !value) {
        showFieldError(field, 'Este campo es obligatorio');
        return false;
    }
    
    if (type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, 'Ingresa un email válido');
        return false;
    }
    
    clearFieldError(field);
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    errorElement.style.color = '#DC3545';
    errorElement.style.fontSize = '0.875rem';
    errorElement.style.marginTop = '0.25rem';
    
    field.parentNode.appendChild(errorElement);
    field.style.borderColor = '#DC3545';
}

function clearFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    field.style.borderColor = '';
}

// Utilidades
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Gestión del estado de la aplicación
const AppState = {
    user: null,
    currentPage: 'home',
    investments: [],
    
    setUser(userData) {
        this.user = userData;
        this.updateUI();
    },
    
    updateUI() {
        // Actualizar interfaz según el estado del usuario
        if (this.user) {
            this.showLoggedInUI();
        } else {
            this.showLoggedOutUI();
        }
    },
    
    showLoggedInUI() {
        const authButtons = document.querySelector('.nav-auth');
        if (authButtons) {
            authButtons.innerHTML = `
                <button class="btn-secondary">Mi Cuenta</button>
                <button class="btn-primary" onclick="AppState.logout()">Cerrar Sesión</button>
            `;
        }
    },
    
    showLoggedOutUI() {
        const authButtons = document.querySelector('.nav-auth');
        if (authButtons) {
            authButtons.innerHTML = `
                <button class="btn-secondary">Iniciar Sesión</button>
                <button class="btn-primary">Registrarse</button>
            `;
        }
    },
    
    logout() {
        this.user = null;
        this.updateUI();
        showNotification('Sesión cerrada correctamente', 'info');
    }
};

// Función para agregar nueva inversión demo
function addDemoInvestment(investmentData) {
    let investments = JSON.parse(localStorage.getItem('demo_investments') || '[]');
    
    const newInvestment = {
        id: 'inv_' + Date.now(),
        projectName: investmentData.name,
        category: investmentData.category || 'STAKING',
        location: investmentData.location || 'Colombia',
        amount: investmentData.amount,
        progress: Math.floor(Math.random() * 30) + 10, // 10-40% progreso inicial
        roi: parseFloat(investmentData.rentabilidad.replace('%', '')),
        returns: Math.round(investmentData.amount * (parseFloat(investmentData.rentabilidad.replace('%', '')) / 100) * 0.3), // 30% de retornos iniciales
        startDate: new Date().toISOString().split('T')[0],
        endDate: new Date(Date.now() + (parseInt(investmentData.plazo) * 30 * 24 * 60 * 60 * 1000)).toISOString().split('T')[0],
        status: 'active',
        icon: investmentData.icon || 'fas fa-seedling'
    };
    
    investments.push(newInvestment);
    localStorage.setItem('demo_investments', JSON.stringify(investments));
    
    // Agregar actividad
    let activity = JSON.parse(localStorage.getItem('demo_activity') || '[]');
    activity.unshift({
        type: 'investment',
        icon: 'fas fa-plus',
        title: 'Nueva inversión realizada',
        description: `Proyecto: ${investmentData.name}`,
        amount: `-$${investmentData.amount.toLocaleString()}`,
        time: 'Ahora',
        category: 'info'
    });
    localStorage.setItem('demo_activity', JSON.stringify(activity));
    
    console.log('Nueva inversión demo agregada:', newInvestment);
}

// Exportar funciones para uso global
window.AGROMARKET = {
    showNotification,
    validateField,
    AppState,
    debounce,
    throttle,
    addDemoInvestment
};
