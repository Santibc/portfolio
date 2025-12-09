{{-- Modern Navigation Bar --}}
<nav class="modern-navbar" id="modernNavbar">
    <div class="nav-container">
        {{-- Logo Section --}}
        <a href="{{ url('/') }}" class="nav-logo">
            <img src="{{ asset($config->logo ?? 'images/logo1.png') }}" alt="Betogether" class="logo-nav">
        </a>

        {{-- Navigation Links --}}
        <div class="nav-links">
            {{-- Public Links --}}
            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="bi bi-house"></i>
                    <span>Inicio</span>
                </div>
            </a>
            <a href="{{ route('planes') }}" class="nav-link {{ request()->routeIs('planes') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span>Planes</span>
                </div>
            </a>
            <a href="{{ route('eventos') }}" class="nav-link {{ request()->routeIs('eventos*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="bi bi-calendar-event"></i>
                    <span>Eventos</span>
                </div>
            </a>
            <a href="{{ route('contacto') }}" class="nav-link {{ request()->routeIs('contacto') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="bi bi-envelope"></i>
                    <span>Contacto</span>
                </div>
            </a>

            <div class="nav-separator"></div>

            {{-- Auth Links --}}
            @auth
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <div class="nav-link-content">
                        <i class="bi bi-person-circle"></i>
                        <span>Mi cuenta</span>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="nav-link logout-btn">
                        <div class="nav-link-content">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Cerrar sesión</span>
                        </div>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-link login-btn">
                    <div class="nav-link-content">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Iniciar sesión</span>
                    </div>
                </a>
                <a href="{{ route('register') }}" class="nav-link register-btn">
                    <div class="nav-link-content">
                        <i class="bi bi-person-plus"></i>
                        <span>Registrarse</span>
                    </div>
                </a>
            @endauth
        </div>

        {{-- Mobile Menu Button --}}
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <div class="hamburger">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </div>
        </button>
    </div>

    {{-- Mobile Menu Overlay --}}
    <div class="mobile-menu-overlay" id="mobileMenuOverlay">
        <div class="mobile-menu">
            <div class="mobile-menu-header">
                <img src="{{ asset($config->logo ?? 'images/logo1.png') }}" alt="Betogether" class="mobile-logo">
                <button class="close-mobile-menu" id="closeMobileMenu">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="mobile-menu-links">
                {{-- Public Links Mobile --}}
                <a href="{{ url('/') }}" class="mobile-nav-link {{ request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-house"></i>
                    <span>Inicio</span>
                </a>
                <a href="{{ route('planes') }}" class="mobile-nav-link {{ request()->routeIs('planes') ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3-gap"></i>
                    <span>Planes</span>
                </a>
                <a href="{{ route('eventos') }}" class="mobile-nav-link {{ request()->routeIs('eventos*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i>
                    <span>Eventos</span>
                </a>
                <a href="{{ route('contacto') }}" class="mobile-nav-link {{ request()->routeIs('contacto') ? 'active' : '' }}">
                    <i class="bi bi-envelope"></i>
                    <span>Contacto</span>
                </a>

                <hr style="border-color: #eee; margin: 10px 0;">

                {{-- Auth Links Mobile --}}
                @auth
                    <a href="{{ route('dashboard') }}" class="mobile-nav-link">
                        <i class="bi bi-person-circle"></i>
                        <span>Mi cuenta</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mobile-nav-link logout-mobile">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Cerrar sesión</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="mobile-nav-link">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Iniciar sesión</span>
                    </a>
                    <a href="{{ route('register') }}" class="mobile-nav-link register-mobile">
                        <i class="bi bi-person-plus"></i>
                        <span>Registrarse</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Navbar JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('modernNavbar');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const closeMobileMenu = document.getElementById('closeMobileMenu');

    // Navbar scroll effect
    let lastScrollY = window.scrollY;
    let ticking = false;

    function updateNavbar() {
        const scrollY = window.scrollY;

        if (scrollY > 20) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        lastScrollY = scrollY;
        ticking = false;
    }

    function onScroll() {
        if (!ticking) {
            requestAnimationFrame(updateNavbar);
            ticking = true;
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });

    // Mobile menu functionality
    function toggleMobileMenu(show) {
        if (show) {
            mobileMenuOverlay.classList.add('active');
            mobileMenuBtn.classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            mobileMenuOverlay.classList.remove('active');
            mobileMenuBtn.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Open mobile menu
    mobileMenuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMobileMenu(true);
    });

    // Close mobile menu
    closeMobileMenu.addEventListener('click', function() {
        toggleMobileMenu(false);
    });

    // Close mobile menu when clicking overlay
    mobileMenuOverlay.addEventListener('click', function(e) {
        if (e.target === mobileMenuOverlay) {
            toggleMobileMenu(false);
        }
    });

    // Close mobile menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            toggleMobileMenu(false);
        }
    });

    // Close mobile menu when clicking on mobile nav links
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link:not(.logout-mobile)');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            toggleMobileMenu(false);
        });
    });

    // Smooth hover animations for nav links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Logo click to scroll to top (only on home page)
    const logoSection = document.querySelector('.nav-logo');
    if (logoSection && window.location.pathname === '/') {
        logoSection.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Add ripple effect to nav links
    function createRipple(event) {
        const button = event.currentTarget;
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        const rect = button.getBoundingClientRect();
        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - rect.left - radius}px`;
        circle.style.top = `${event.clientY - rect.top - radius}px`;
        circle.classList.add('ripple');

        const ripple = button.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);

        setTimeout(() => {
            circle.remove();
        }, 600);
    }

    // Apply ripple effect to nav links
    navLinks.forEach(link => {
        link.addEventListener('click', createRipple);
    });

    // Add subtle animation to navbar on first load
    setTimeout(() => {
        navbar.style.transform = 'translateY(0)';
        navbar.style.opacity = '1';
    }, 100);

    // Performance optimization: Debounce resize events
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Close mobile menu on resize to desktop
            if (window.innerWidth > 768) {
                toggleMobileMenu(false);
            }
        }, 100);
    });
});
</script>
