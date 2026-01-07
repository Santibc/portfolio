<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/ico.png') }}"/>
    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- CSS personalizado y Bootstrap --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    @stack('styles')
    @yield('css')

    <style>
        :root {
            --admin-primary: #1a1a1a;
            --admin-primary-hover: #333333;
            --admin-beige: #f5f5f5;
            --admin-text: #333333;
            --admin-text-light: #666666;
            --admin-bg: #FAFAFA;
            --admin-white: #FFFFFF;
            --admin-border: #E5E5E5;
            --font-serif: 'Playfair Display', Georgia, serif;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--admin-bg);
            font-family: var(--font-sans);
            color: var(--admin-text);
        }

        /* ============ SIDEBAR MINIMALISTA ============ */
        .sidebar {
            width: 240px;
            transition: all 0.3s ease;
            background: var(--admin-white);
            border-right: 1px solid var(--admin-border);
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        .sidebar.collapsed {
            width: 70px;
            overflow-x: hidden !important;
        }

        /* Header minimalista */
        header {
            height: 64px;
            background: var(--admin-white);
            border-bottom: 1px solid var(--admin-border);
            color: var(--admin-text);
            position: fixed;
            top: 0;
            right: 0;
            transition: left 0.3s ease;
            padding-right: 1rem;
        }

        header .text-muted {
            color: var(--admin-text-light) !important;
        }

        header .fw-semibold {
            color: var(--admin-text) !important;
        }

        #toggleSidebar {
            color: var(--admin-text) !important;
            border: none !important;
            background-color: transparent;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #toggleSidebar:hover {
            background-color: var(--admin-beige) !important;
            color: var(--admin-primary) !important;
        }

        #toggleSidebar i {
            font-size: 20px;
        }

        main {
            padding-top: 80px;
            transition: margin-left 0.3s ease;
        }

        /* Header user info */
        .header-user-info {
            flex-grow: 1;
            justify-content: flex-end;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-user-info .text-end {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: calc(100% - 50px);
        }

        .header-user-info .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--admin-beige);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--admin-primary);
            font-weight: 600;
            font-size: 14px;
        }

        /* ============ NAVIGATION SIDEBAR ============ */
        .sidebar-header {
            padding: 20px 16px;
            border-bottom: 1px solid var(--admin-border);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 80px;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-logo-img {
            max-width: 140px;
            height: auto;
            transition: all 0.3s;
        }

        .sidebar-logo-icon {
            width: 36px;
            display: none;
        }

        .sidebar.collapsed .sidebar-logo-img {
            display: none !important;
        }

        .sidebar.collapsed .sidebar-logo-icon {
            display: block !important;
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            margin-bottom: 4px;
            border-radius: 8px;
            color: var(--admin-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background: var(--admin-beige);
            color: var(--admin-primary);
        }

        .sidebar-link.active {
            background: var(--admin-primary);
            color: white;
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .sidebar-link span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-divider {
            height: 1px;
            background: var(--admin-border);
            margin: 16px 0;
        }

        .sidebar-section-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--admin-text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 14px 4px;
            margin-top: 8px;
        }

        /* Submenu */
        .sidebar-submenu-container .sidebar-link .sidebar-chevron {
            margin-left: auto;
            font-size: 12px;
            transition: transform 0.2s;
        }

        .sidebar-submenu-container .sidebar-link[aria-expanded="true"] .sidebar-chevron {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            padding-left: 20px;
            border-left: 2px solid var(--admin-border);
            margin-left: 26px;
            margin-bottom: 8px;
        }

        .sidebar-sublink {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 6px;
            color: var(--admin-text-light);
            text-decoration: none;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .sidebar-sublink:hover {
            background: var(--admin-beige);
            color: var(--admin-primary);
        }

        .sidebar-sublink.active {
            background: rgba(26, 26, 26, 0.1);
            color: var(--admin-primary);
            font-weight: 500;
        }

        .sidebar-sublink i {
            width: 16px;
            text-align: center;
            font-size: 14px;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid var(--admin-border);
        }

        .sidebar-footer .sidebar-link {
            color: #dc3545;
        }

        .sidebar-footer .sidebar-link:hover {
            background: #fee2e2;
            color: #dc3545;
        }

        /* Collapsed state */
        .sidebar.collapsed .sidebar-link span,
        .sidebar.collapsed .sidebar-sublink span,
        .sidebar.collapsed .sidebar-chevron,
        .sidebar.collapsed .sidebar-section-title {
            display: none;
        }

        .sidebar.collapsed .sidebar-link {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.collapsed .sidebar-submenu {
            padding-left: 0;
            border-left: none;
            margin-left: 0;
            background: var(--admin-beige);
            border-radius: 6px;
            padding: 6px;
        }

        .sidebar.collapsed .sidebar-sublink {
            justify-content: center;
            padding: 10px;
        }

        /* Scrollbar */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--admin-border);
            border-radius: 2px;
        }

        @media (max-width: 768px) {
            .header-user-info .text-end {
                display: none;
            }
        }

        /* ============ CONTENT CARDS ============ */
        .bg-white {
            background: var(--admin-white) !important;
        }

        .card, .content-card {
            background: var(--admin-white);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            box-shadow: none;
        }

        .card-header {
            background: var(--admin-bg);
            border-bottom: 1px solid var(--admin-border);
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
        }

        .btn-primary:hover {
            background-color: var(--admin-primary-hover);
            border-color: var(--admin-primary-hover);
        }

        .btn-outline-primary {
            color: var(--admin-primary);
            border-color: var(--admin-primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
            color: white;
        }

        /* Links */
        a {
            color: var(--admin-primary);
        }

        a:hover {
            color: var(--admin-primary-hover);
        }

        /* Tables */
        .table thead th {
            background: var(--admin-bg);
            border-bottom: 2px solid var(--admin-border);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--admin-text-light);
        }

        .table-dark thead th {
            background: var(--admin-primary) !important;
            color: white;
        }

        /* ============ FIX PARA SELECT DROPDOWN ============ */
        /* Evitar que la flecha del select se superponga con el texto */
        .form-select,
        select.form-select,
        select {
            padding-right: 2.5rem !important;
            background-position: right 0.75rem center !important;
        }
    </style>
</head>
<body>

    {{-- Sidebar fijo --}}
    <div class="sidebar position-fixed top-0 start-0 vh-100 d-flex flex-column" style="z-index: 1030;">
        @include('layouts.navigation-vertical')
    </div>

    {{-- Contenedor principal --}}
    <div>
        {{-- Header fijo --}}
        <header id="appHeader" class="position-fixed top-0 d-flex justify-content-between align-items-center px-3" style="z-index: 1020;">
            <button id="toggleSidebar" class="btn btn-sm" title="Menú">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center gap-2 header-user-info">
                <div class="text-end">
                    <div class="fw-semibold" style="font-size: 14px;">{{ Auth::user()->name }}</div>
                    <div class="text-muted" style="font-size: 12px;">{{ Auth::user()->email }}</div>
                </div>
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
            </div>
        </header>

        {{-- Contenido --}}
        <main id="appMainContent">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>
    </div>

    {{-- JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Lógica unificada del sidebar --}}
    <script>
    (() => {
      const sidebar       = document.querySelector('.sidebar');
      const appHeader     = document.getElementById('appHeader');
      const appMainContent= document.getElementById('appMainContent');
      const toggleBtn     = document.getElementById('toggleSidebar');

      if (!sidebar || !appHeader || !appMainContent || !toggleBtn) return;

      let isManuallyToggled = false;
      const TRANSITION_MS = 300;

      const updateLayout = () => {
        const sidebarWidth = sidebar.offsetWidth;
        appHeader.style.left = `${sidebarWidth}px`;
        appHeader.style.right = '0';
        appMainContent.style.marginLeft = `${sidebarWidth}px`;
      };

      const saveSidebarState = () => {
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed') ? 'true' : 'false');
      };

      const handleResponsive = () => {
        if (isManuallyToggled) return;
        if (window.innerWidth <= 768) {
          sidebar.classList.add('collapsed');
        } else {
          sidebar.classList.remove('collapsed');
        }
        setTimeout(updateLayout, TRANSITION_MS);
      };

      const restoreSidebarState = () => {
        const saved = localStorage.getItem('sidebarCollapsed');
        if (saved !== null) {
          isManuallyToggled = true;
          if (saved === 'true') sidebar.classList.add('collapsed');
          else sidebar.classList.remove('collapsed');
        } else {
          handleResponsive();
        }
        setTimeout(updateLayout, TRANSITION_MS);
      };

      document.addEventListener('DOMContentLoaded', () => {
        restoreSidebarState();

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
      });

      toggleBtn.addEventListener('click', () => {
        isManuallyToggled = true;
        sidebar.classList.toggle('collapsed');
        saveSidebarState();
        setTimeout(updateLayout, TRANSITION_MS);
      });

      let resizeTimer;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
          handleResponsive();
          updateLayout();
        }, 250);
      });

      document.addEventListener('click', (e) => {
        if (!sidebar.classList.contains('collapsed')) return;
        const trigger = e.target.closest('[data-bs-toggle="collapse"]');
        if (!trigger) return;

        e.preventDefault();
        e.stopPropagation();

        const targetSel = trigger.getAttribute('href') || trigger.dataset.bsTarget;
        if (targetSel) {
          const targetEl = document.querySelector(targetSel);
          if (targetEl) {
            new bootstrap.Collapse(targetEl, { toggle: true });
          }
        }
      });
    })();
    </script>

    <script>
      document.documentElement.style.overflowX = 'hidden';
      document.body.style.overflowX = 'hidden';
    </script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>
