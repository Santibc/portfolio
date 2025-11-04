@extends('tienda.sport_layout')

@section('title', 'Sport Store - Tienda Deportiva')

@push('styles')
<style>
    /* Hero Slider */
    .hero-slider {
        position: relative;
        width: 100%;
        height: 600px;
        overflow: hidden;
        background-color: #f5f5f5;
    }

    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
    }

    .slide.active {
        opacity: 1;
    }

    .slide-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .slide-content {
        position: absolute;
        top: 50%;
        left: 10%;
        transform: translateY(-50%);
        color: #fff;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        max-width: 600px;
    }

    .slide-title {
        font-size: 80px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .slide-subtitle {
        font-size: 24px;
        margin-bottom: 30px;
    }

    .slider-dots {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 10;
    }

    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .dot.active {
        background-color: #fff;
    }

    .slider-arrows {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        display: flex;
        justify-content: space-between;
        padding: 0 20px;
        z-index: 10;
    }

    .arrow {
        background-color: rgba(0,0,0,0.5);
        color: #fff;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .arrow:hover {
        background-color: rgba(0,0,0,0.8);
    }

    /* Section Styles */
    .section {
        padding: 60px 0;
    }

    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-title {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .section-subtitle {
        font-size: 18px;
        opacity: 0.7;
    }

    /* Categories Grid */
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .category-card {
        position: relative;
        height: 400px;
        overflow: hidden;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: transform 0.3s;
    }

    .category-card:hover {
        transform: scale(1.02);
    }

    .category-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .category-card:hover img {
        transform: scale(1.1);
    }

    .category-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        display: flex;
        align-items: flex-end;
        padding: 30px;
    }

    .category-title {
        color: #fff;
        font-size: 32px;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }

    .product-card {
        background: #fff;
        border-radius: var(--border-radius);
        overflow: hidden;
        transition: all 0.3s;
        border: 1px solid #eee;
    }

    .product-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transform: translateY(-5px);
    }

    .product-image-container {
        position: relative;
        height: 300px;
        overflow: hidden;
        background-color: #f5f5f5;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .product-card:hover .product-image {
        transform: scale(1.1);
    }

    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #e74c3c;
        color: #fff;
        padding: 5px 15px;
        border-radius: var(--border-radius);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .product-info {
        padding: 20px;
    }

    .product-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .product-price {
        font-size: 24px;
        font-weight: 700;
        color: var(--accent-color);
        margin-bottom: 15px;
    }

    .product-price-old {
        font-size: 16px;
        text-decoration: line-through;
        opacity: 0.5;
        margin-left: 10px;
    }

    .add-to-cart {
        width: 100%;
        padding: 12px;
        background-color: var(--button-background);
        color: var(--button-foreground);
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-family: var(--heading-font);
        font-weight: 700;
        text-transform: uppercase;
        transition: all 0.3s;
    }

    .add-to-cart:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    /* Banner Section */
    .promo-banner {
        background-color: #000;
        color: #fff;
        padding: 80px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .promo-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(231,76,60,0.3) 0%, rgba(52,152,219,0.3) 100%);
        z-index: 0;
    }

    .promo-content {
        position: relative;
        z-index: 1;
    }

    .promo-title {
        font-size: 64px;
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .promo-subtitle {
        font-size: 24px;
        margin-bottom: 30px;
    }

    /* Features Section */
    .features-section {
        background-color: #f8f9fa;
        padding: 60px 0;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
    }

    .feature-item {
        text-align: center;
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 20px;
        background-color: var(--accent-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .feature-icon svg {
        width: 30px;
        height: 30px;
        fill: #fff;
    }

    .feature-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .feature-description {
        opacity: 0.7;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .slide-title {
            font-size: 40px;
        }

        .slide-subtitle {
            font-size: 18px;
        }

        .hero-slider {
            height: 400px;
        }

        .section-title {
            font-size: 32px;
        }

        .promo-title {
            font-size: 36px;
        }

        .categories-grid {
            grid-template-columns: 1fr;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }
</style>
@endpush

@section('content')

<!-- Hero Slider -->
<section class="hero-slider">
    <div class="slide active" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="slide-content">
            <h2 class="slide-title">NUEVA COLECCIÓN</h2>
            <p class="slide-subtitle">Descubre las últimas tendencias en deportes</p>
            <a href="#" class="btn">VER PRODUCTOS</a>
        </div>
    </div>

    <div class="slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div class="slide-content">
            <h2 class="slide-title">HASTA 50% OFF</h2>
            <p class="slide-subtitle">En productos seleccionados</p>
            <a href="#" class="btn">APROVECHA AHORA</a>
        </div>
    </div>

    <div class="slide" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div class="slide-content">
            <h2 class="slide-title">EQUIPAMIENTO PRO</h2>
            <p class="slide-subtitle">Para los atletas más exigentes</p>
            <a href="#" class="btn">EXPLORAR</a>
        </div>
    </div>

    <div class="slider-arrows">
        <div class="arrow arrow-left" onclick="previousSlide()">
            <svg viewBox="0 0 24 24" fill="white" width="24" height="24">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </div>
        <div class="arrow arrow-right" onclick="nextSlide()">
            <svg viewBox="0 0 24 24" fill="white" width="24" height="24">
                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
            </svg>
        </div>
    </div>

    <div class="slider-dots">
        <span class="dot active" onclick="currentSlide(0)"></span>
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
    </div>
</section>

<!-- Categories Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">CATEGORÍAS</h2>
            <p class="section-subtitle">Encuentra lo que necesitas</p>
        </div>

        <div class="categories-grid">
            <div class="category-card">
                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&h=600&fit=crop" alt="Calzado">
                <div class="category-overlay">
                    <h3 class="category-title">CALZADO</h3>
                </div>
            </div>

            <div class="category-card">
                <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600&h=600&fit=crop" alt="Indumentaria">
                <div class="category-overlay">
                    <h3 class="category-title">INDUMENTARIA</h3>
                </div>
            </div>

            <div class="category-card">
                <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=600&h=600&fit=crop" alt="Accesorios">
                <div class="category-overlay">
                    <h3 class="category-title">ACCESORIOS</h3>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">PRODUCTOS DESTACADOS</h2>
            <p class="section-subtitle">Los mejores productos seleccionados para ti</p>
        </div>

        <div class="products-grid">
            <!-- Product 1 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop" alt="Zapatillas Running" class="product-image">
                    <span class="product-badge">NUEVO</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Zapatillas Running Pro</h3>
                    <div class="product-price">
                        $89.990
                    </div>
                    <button class="add-to-cart">AGREGAR AL CARRITO</button>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://images.unsplash.com/photo-1556906781-9a412961c28c?w=400&h=400&fit=crop" alt="Remera Deportiva" class="product-image">
                    <span class="product-badge">-30%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Remera Deportiva DRY-FIT</h3>
                    <div class="product-price">
                        $34.990
                        <span class="product-price-old">$49.990</span>
                    </div>
                    <button class="add-to-cart">AGREGAR AL CARRITO</button>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=400&h=400&fit=crop" alt="Mochila Deportiva" class="product-image">
                </div>
                <div class="product-info">
                    <h3 class="product-title">Mochila Deportiva XL</h3>
                    <div class="product-price">
                        $45.990
                    </div>
                    <button class="add-to-cart">AGREGAR AL CARRITO</button>
                </div>
            </div>

            <!-- Product 4 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=400&h=400&fit=crop" alt="Short Training" class="product-image">
                    <span class="product-badge">-20%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Short Training Premium</h3>
                    <div class="product-price">
                        $27.990
                        <span class="product-price-old">$34.990</span>
                    </div>
                    <button class="add-to-cart">AGREGAR AL CARRITO</button>
                </div>
            </div>

            <!-- Product 5 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400&h=400&fit=crop" alt="Zapatillas Basketball" class="product-image">
                    <span class="product-badge">NUEVO</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Zapatillas Basketball Elite</h3>
                    <div class="product-price">
                        $119.990
                    </div>
                    <button class="add-to-cart">AGREGAR AL CARRITO</button>
                </div>
            </div>

            <!-- Product 6 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://images.unsplash.com/photo-1598024055266-e772a5f8f0b3?w=400&h=400&fit=crop" alt="Buzo Deportivo" class="product-image">
                </div>
                <div class="product-info">
                    <h3 class="product-title">Buzo Deportivo Tech</h3>
                    <div class="product-price">
                        $64.990
                    </div>
                    <button class="add-to-cart">AGREGAR AL CARRITO</button>
                </div>
            </div>

            <!-- Product 7 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://images.unsplash.com/photo-1544216717-3bbf52512659?w=400&h=400&fit=crop" alt="Gorra Deportiva" class="product-image">
                </div>
                <div class="product-info">
                    <h3 class="product-title">Gorra Deportiva UV Protection</h3>
                    <div class="product-price">
                        $19.990
                    </div>
                    <button class="add-to-cart">AGREGAR AL CARRITO</button>
                </div>
            </div>

            <!-- Product 8 -->
            <div class="product-card">
                <div class="product-image-container">
                    <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=400&h=400&fit=crop" alt="Botella Deportiva" class="product-image">
                    <span class="product-badge">-15%</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Botella Térmica 750ml</h3>
                    <div class="product-price">
                        $16.990
                        <span class="product-price-old">$19.990</span>
                    </div>
                    <button class="add-to-cart">AGREGAR AL CARRITO</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Promo Banner -->
<section class="promo-banner">
    <div class="promo-content">
        <h2 class="promo-title">TEMPORADA DE OFERTAS</h2>
        <p class="promo-subtitle">Hasta 50% de descuento en productos seleccionados</p>
        <a href="#" class="btn" style="background-color: #fff; color: #000;">VER TODAS LAS OFERTAS</a>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                    </svg>
                </div>
                <h3 class="feature-title">ENVÍO GRATIS</h3>
                <p class="feature-description">En compras mayores a $50.000</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                    </svg>
                </div>
                <h3 class="feature-title">COMPRA SEGURA</h3>
                <p class="feature-description">Protegemos tus datos</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/>
                    </svg>
                </div>
                <h3 class="feature-title">DEVOLUCIONES</h3>
                <p class="feature-description">30 días para devoluciones</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </div>
                <h3 class="feature-title">SOPORTE 24/7</h3>
                <p class="feature-description">Estamos para ayudarte</p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Slider functionality
    let currentSlideIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');

    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        if (index >= slides.length) {
            currentSlideIndex = 0;
        } else if (index < 0) {
            currentSlideIndex = slides.length - 1;
        } else {
            currentSlideIndex = index;
        }

        slides[currentSlideIndex].classList.add('active');
        dots[currentSlideIndex].classList.add('active');
    }

    function nextSlide() {
        showSlide(currentSlideIndex + 1);
    }

    function previousSlide() {
        showSlide(currentSlideIndex - 1);
    }

    function currentSlide(index) {
        showSlide(index);
    }

    // Auto play slider
    setInterval(() => {
        nextSlide();
    }, 5000);

    // Add to cart functionality (placeholder)
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            alert('Producto agregado al carrito!');
        });
    });
</script>
@endpush
