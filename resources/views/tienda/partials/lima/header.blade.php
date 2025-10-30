<header class="js-head-main head-main position-sticky">

    {{-- Topbar (Desktop Only) --}}
    @if(isset($empresa))
    <div class="js-topbar section-topbar container-fluid d-none d-md-block">
        <div class="row align-items-center justify-content-end">
            <div class="col">
                <ul class="list list-inline mb-0">
                    @if($empresa->facebook_url)
                    <li class="secondary-menu-item list-inline-item">
                        <a class="secondary-menu-link" href="{{ $empresa->facebook_url }}" target="_blank">Facebook</a>
                    </li>
                    @endif
                    @if($empresa->instagram_url)
                    <li class="secondary-menu-item list-inline-item">
                        <a class="secondary-menu-link" href="{{ $empresa->instagram_url }}" target="_blank">Instagram</a>
                    </li>
                    @endif
                </ul>
            </div>
            <div class="col-auto">
                @auth
                <ul class="list list-inline mb-0">
                    <li class="list-inline-item">
                        <a class="secondary-menu-link" href="{{ route('dashboard') }}">
                            <svg class="icon-inline icon-md" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                            </svg>
                            Mi Cuenta
                        </a>
                    </li>
                </ul>
                @else
                <ul class="list list-inline mb-0">
                    <li class="list-inline-item">
                        <a class="secondary-menu-link" href="{{ route('login') }}">Iniciar Sesión</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="secondary-menu-link" href="{{ route('register') }}">Registrarse</a>
                    </li>
                </ul>
                @endauth
            </div>
        </div>
    </div>
    @endif

    {{-- Adbar (Promotional messages) --}}
    @if(isset($empresa) && $empresa->mensaje_promocional)
    <section class="js-adbar section-adbar px-1">
        <div class="js-swiper-adbar swiper-container text-center">
            <div class="swiper-wrapper">
                <div class="swiper-slide slide-container px-4">
                    <span class="adbar-message">{{ $empresa->mensaje_promocional }}</span>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Main Header Row --}}
    <div class="container-fluid main-header-container">
        <div class="row align-items-center mb-1 mb-md-0">

            {{-- Mobile Hamburger --}}
            <div class="col-auto col-utility order-first pl-3 d-md-none">
                <a href="#" class="js-modal-open" data-toggle="#nav-hamburger">
                    <svg class="icon-inline icon-2x" width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </a>
            </div>

            {{-- Search Form (Desktop) --}}
            <div class="col-auto col-utility order-1 order-md-0 d-none d-md-inline-block">
                <form class="js-search-form search-container" action="{{ route('tienda.categorias', $empresa->slug) }}" method="get">
                    <input type="search"
                           name="buscar"
                           class="search-input"
                           placeholder="¿Qué estás buscando?"
                           autocomplete="off">
                    <button type="submit" class="search-btn">
                        <svg class="icon-inline" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Logo (Center) --}}
            <div class="col text-center order-md-0">
                <div id="logo" class="logo-img-container">
                    <a href="{{ route('tienda.empresa', $empresa->slug) }}">
                        @if(isset($empresa) && $empresa->logo)
                        <img src="{{ asset($empresa->logo) }}" alt="{{ $empresa->nombre }}" class="logo-img">
                        @else
                        <h1 class="logo-text mb-0">{{ $empresa->nombre ?? 'Lima Theme' }}</h1>
                        @endif
                    </a>
                    <h1 style="display: none;">{{ $empresa->nombre ?? 'Lima Theme' }}</h1>
                </div>
            </div>

            {{-- Cart Widget --}}
            <div class="col-auto col-utility-cart text-md-right order-2 pr-3 px-md-3">
                <div id="ajax-cart" class="cart-summary">
                    <a href="#" data-toggle="#modal-cart" class="js-modal-open btn btn-utility">
                        <svg class="icon-inline icon-lg" width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        <span class="js-cart-widget-amount badge badge-primary">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Mobile Search --}}
        <div class="row d-md-none">
            <div class="col-12 px-3 pb-2">
                <form class="js-search-form search-container" action="{{ route('tienda.categorias', $empresa->slug) }}" method="get">
                    <input type="search"
                           name="buscar"
                           class="search-input"
                           placeholder="¿Qué estás buscando?"
                           autocomplete="off">
                    <button type="submit" class="search-btn">
                        <svg class="icon-inline" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Desktop Navigation + Header Banners --}}
    <div class="js-menu-and-banners-row menu-and-banners-row row d-none d-md-flex">
        <div class="js-desktop-nav-col col">
            <div class="nav-desktop">
                <ul class="js-nav-desktop-list nav-desktop-list">
                    {{-- Home Link --}}
                    <li class="nav-item-desktop">
                        <div class="nav-item-container">
                            <a class="nav-list-link" href="{{ route('tienda.empresa', $empresa->slug) }}">Inicio</a>
                        </div>
                    </li>

                    {{-- Categories --}}
                    @if(isset($categorias) && $categorias->count() > 0)
                        @foreach($categorias as $categoria)
                        <li class="nav-item-desktop {{ $categoria->subcategorias && $categoria->subcategorias->count() > 0 ? 'nav-dropdown' : '' }}">
                            <div class="nav-item-container">
                                <a class="nav-list-link" href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $categoria->id]) }}">
                                    {{ $categoria->nombre }}
                                </a>
                            </div>

                            @if($categoria->subcategorias && $categoria->subcategorias->count() > 0)
                            <div class="js-desktop-dropdown nav-dropdown-content desktop-dropdown">
                                <div class="container">
                                    <ul class="list">
                                        @foreach($categoria->subcategorias as $subcategoria)
                                        <li>
                                            <a href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $subcategoria->id]) }}">
                                                {{ $subcategoria->nombre }}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif
                        </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        {{-- Header Banners (Optional) --}}
        @if(isset($empresa) && ($empresa->info_envio || $empresa->whatsapp))
        <div class="js-head-banners-col col-md-auto">
            <div class="head-banners row">
                @if($empresa->info_envio)
                <div class="col head-banner-item">
                    <div class="row align-items-center">
                        <div class="col-auto pr-0">
                            <svg class="icon-inline icon-2x" width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                            </svg>
                        </div>
                        <div class="col pl-2 ml-1">
                            <div class="head-banner-text font-small">{{ $empresa->info_envio }}</div>
                        </div>
                    </div>
                </div>
                @endif

                @if($empresa->whatsapp)
                <div class="col head-banner-item">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $empresa->whatsapp) }}" target="_blank">
                        <div class="row align-items-center">
                            <div class="col-auto pr-0">
                                <svg class="icon-inline icon-2x" width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                </svg>
                            </div>
                            <div class="col pl-2 ml-1">
                                <div class="head-banner-text font-small">Consultanos</div>
                                <div class="btn-link font-small">WhatsApp</div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

</header>
