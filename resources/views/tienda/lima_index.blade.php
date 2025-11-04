@extends('tienda.lima_layout')

@section('title', $empresa->nombre . ' - Tienda Online')

@section('body-class', 'template-home')

@section('content')

{{-- Home Slider Section - Con imágenes reales --}}
<section class="section-slider-home" data-store="home-slider">
    <div class="js-home-main-slider swiper-container">
        <div class="swiper-wrapper">

            {{-- Slide 1 --}}
            <div class="swiper-slide">
                <div class="slider-slide-content" style="background-image: url('{{ asset('imagenes/empresas/1/carrusel/1755315050_689ffb6aaefa2_Captura_de_pantalla_2025-07-21_095007.png') }}'); background-size: cover; background-position: center; min-height: 500px; position: relative;">
                    <div class="container h-100">
                        <div class="row align-items-center h-100" style="min-height: 500px;">
                            <div class="col-12 text-center">
                                <h2 class="slider-title" style="font-family: var(--heading-font); font-size: 48px; font-weight: 700; color: #fff; margin-bottom: 10px; text-transform: lowercase; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                    team. verano
                                </h2>
                                <p class="slider-text" style="font-size: 16px; color: #fff; margin-bottom: 30px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                                    Miramos, tocamos y alcanzamos
                                </p>
                                <a href="#" class="btn-slider" style="display: inline-block; padding: 12px 35px; background-color: #fff; color: #000; text-decoration: none; border-radius: 25px; font-size: 14px; font-weight: 500; text-transform: lowercase;">
                                    yo veo
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Botones de navegación circular --}}
                    <div class="slider-nav-circles" style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 15px; z-index: 10;">
                        <button class="slider-nav-btn slider-nav-prev" style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.9); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                        <button class="slider-nav-btn slider-nav-next" style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.9); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Slide 2 --}}
            <div class="swiper-slide">
                <div class="slider-slide-content" style="background-image: url('{{ asset('imagenes/empresas/1/carrusel/1755316851_68a00273a0590_Captura_de_pantalla_2025-07-21_094739.png') }}'); background-size: cover; background-position: center; min-height: 500px; position: relative;">
                    <div class="container h-100">
                        <div class="row align-items-center h-100" style="min-height: 500px;">
                            <div class="col-12 text-center">
                                <h2 class="slider-title" style="font-family: var(--heading-font); font-size: 48px; font-weight: 700; color: #000; margin-bottom: 10px; text-transform: lowercase; text-shadow: 2px 2px 4px rgba(255,255,255,0.5);">
                                    nueva colección
                                </h2>
                                <p class="slider-text" style="font-size: 16px; color: #333; margin-bottom: 30px; text-shadow: 1px 1px 2px rgba(255,255,255,0.5);">
                                    Descubrí las últimas tendencias
                                </p>
                                <a href="#" class="btn-slider" style="display: inline-block; padding: 12px 35px; background-color: #000; color: #fff; text-decoration: none; border-radius: 25px; font-size: 14px; font-weight: 500; text-transform: lowercase;">
                                    ver ahora
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Botones de navegación circular --}}
                    <div class="slider-nav-circles" style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 15px; z-index: 10;">
                        <button class="slider-nav-btn slider-nav-prev" style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.8); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                <path d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                        <button class="slider-nav-btn slider-nav-next" style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.8); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Slide 3 --}}
            <div class="swiper-slide">
                <div class="slider-slide-content" style="background-image: url('{{ asset('imagenes/empresas/1/carrusel/1755316935_68a002c7f3a85_Captura_de_pantalla_2025-07-21_095145.png') }}'); background-size: cover; background-position: center; min-height: 500px; position: relative;">
                    <div class="container h-100">
                        <div class="row align-items-center h-100" style="min-height: 500px;">
                            <div class="col-12 text-center">
                                <h2 class="slider-title" style="font-family: var(--heading-font); font-size: 48px; font-weight: 700; color: #000; margin-bottom: 10px; text-transform: lowercase; text-shadow: 2px 2px 4px rgba(255,255,255,0.5);">
                                    envío gratis
                                </h2>
                                <p class="slider-text" style="font-size: 16px; color: #333; margin-bottom: 30px; text-shadow: 1px 1px 2px rgba(255,255,255,0.5);">
                                    En compras superiores a $12.000
                                </p>
                                <a href="#" class="btn-slider" style="display: inline-block; padding: 12px 35px; background-color: #000; color: #fff; text-decoration: none; border-radius: 25px; font-size: 14px; font-weight: 500; text-transform: lowercase;">
                                    comprar
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Botones de navegación circular --}}
                    <div class="slider-nav-circles" style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 15px; z-index: 10;">
                        <button class="slider-nav-btn slider-nav-prev" style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.8); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                <path d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                        <button class="slider-nav-btn slider-nav-next" style="width: 45px; height: 45px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.8); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Sección de Categorías con scroll horizontal --}}
<section class="section-categories" style="padding: 40px 0; background-color: #fff;">
    <div class="container">
        <div style="position: relative;">
            <h2 style="font-family: var(--heading-font); font-size: 24px; font-weight: 700; text-transform: lowercase; margin-bottom: 30px;">categorías destacadas</h2>

            <div class="categories-slider-wrapper" style="position: relative;">
                <button class="cat-nav-prev" style="position: absolute; left: -15px; top: 50%; transform: translateY(-50%); z-index: 10; width: 40px; height: 40px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.7); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>

                <div class="categories-scroll" style="display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth; padding: 10px 5px; -ms-overflow-style: none; scrollbar-width: none;">
                    <div class="cat-item" style="min-width: 200px; flex-shrink: 0; text-align: center; cursor: pointer;">
                        <div style="width: 200px; height: 250px; background-color: #f5f5f5; border-radius: 8px; overflow: hidden; margin-bottom: 10px;">
                            <img src="{{ asset('imagenes/categorias/1755572783_68a3ea2f4cffb_Captura de pantalla 2025-07-28 201952.png') }}" alt="Categoría" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0;">colección</h3>
                    </div>

                    <div class="cat-item" style="min-width: 200px; flex-shrink: 0; text-align: center; cursor: pointer;">
                        <div style="width: 200px; height: 250px; background-color: #f5f5f5; border-radius: 8px; overflow: hidden; margin-bottom: 10px;">
                            <img src="{{ asset('imagenes/categorias/1755572835_68a3ea6351dd5_Captura de pantalla 2025-07-21 094907.png') }}" alt="Categoría" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0;">beauty</h3>
                    </div>

                    <div class="cat-item" style="min-width: 200px; flex-shrink: 0; text-align: center; cursor: pointer;">
                        <div style="width: 200px; height: 250px; background-color: #f5f5f5; border-radius: 8px; overflow: hidden; margin-bottom: 10px;">
                            <img src="{{ asset('imagenes/categorias/1757978303_68c89ebf47540_Gemini_Generated_Image_fb93zffb93zffb93.png') }}" alt="Categoría" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0;">lifestyle</h3>
                    </div>

                    <div class="cat-item" style="min-width: 200px; flex-shrink: 0; text-align: center; cursor: pointer;">
                        <div style="width: 200px; height: 250px; background-color: #f5f5f5; border-radius: 8px; overflow: hidden; margin-bottom: 10px;">
                            <img src="{{ asset('imagenes/categorias/1757978490_68c89f7a9604f_Gemini_Generated_Image_tm4jvitm4jvitm4j.png') }}" alt="Categoría" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0;">bazar</h3>
                    </div>

                    <div class="cat-item" style="min-width: 200px; flex-shrink: 0; text-align: center; cursor: pointer;">
                        <div style="width: 200px; height: 250px; background-color: #f5f5f5; border-radius: 8px; overflow: hidden; margin-bottom: 10px;">
                            <img src="{{ asset('imagenes/categorias/1757979087_68c8a1cf473da_Gemini_Generated_Image_tvp1jttvp1jttvp1.png') }}" alt="Categoría" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0;">sale</h3>
                    </div>
                </div>

                <button class="cat-nav-next" style="position: absolute; right: -15px; top: 50%; transform: translateY(-50%); z-index: 10; width: 40px; height: 40px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.7); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- Sección de Productos con scroll horizontal --}}
<section class="section-products" style="padding: 40px 0; background-color: #f9f9f9;">
    <div class="container">
        <div style="position: relative;">
            <h2 style="font-family: var(--heading-font); font-size: 24px; font-weight: 700; text-transform: lowercase; margin-bottom: 30px;">productos destacados</h2>

            <div class="products-slider-wrapper" style="position: relative;">
                <button class="prod-nav-prev" style="position: absolute; left: -15px; top: 50%; transform: translateY(-50%); z-index: 10; width: 40px; height: 40px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.7); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>

                <div class="products-scroll" style="display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth; padding: 10px 5px; -ms-overflow-style: none; scrollbar-width: none;">
                    <div class="prod-item" style="min-width: 250px; flex-shrink: 0; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s ease;">
                        <div style="width: 100%; height: 300px; overflow: hidden;">
                            <img src="{{ asset('imagenes/productos/29/1755569156_68a3dc04bebfb_COLAGENO-x-100.jpg') }}" alt="Producto" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 15px;">
                            <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0 0 8px 0; color: #333;">producto 1</h3>
                            <p style="font-size: 16px; font-weight: 700; margin: 0; color: #000;">$12.000</p>
                        </div>
                    </div>

                    <div class="prod-item" style="min-width: 250px; flex-shrink: 0; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s ease;">
                        <div style="width: 100%; height: 300px; overflow: hidden;">
                            <img src="{{ asset('imagenes/productos/33/1757979378_68c8a2f2ba843_Gemini_Generated_Image_f69d6mf69d6mf69d.png') }}" alt="Producto" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 15px;">
                            <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0 0 8px 0; color: #333;">producto 2</h3>
                            <p style="font-size: 16px; font-weight: 700; margin: 0; color: #000;">$8.500</p>
                        </div>
                    </div>

                    <div class="prod-item" style="min-width: 250px; flex-shrink: 0; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s ease;">
                        <div style="width: 100%; height: 300px; overflow: hidden;">
                            <img src="{{ asset('imagenes/productos/34/1757980662_68c8a7f623ff5_Gemini_Generated_Image_tm4jvitm4jvitm4j.png') }}" alt="Producto" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 15px;">
                            <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0 0 8px 0; color: #333;">producto 3</h3>
                            <p style="font-size: 16px; font-weight: 700; margin: 0; color: #000;">$15.000</p>
                        </div>
                    </div>

                    <div class="prod-item" style="min-width: 250px; flex-shrink: 0; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s ease;">
                        <div style="width: 100%; height: 300px; overflow: hidden;">
                            <img src="{{ asset('imagenes/productos/35/1757981722_68c8ac1a259cc_Gemini_Generated_Image_fb93zffb93zffb93.png') }}" alt="Producto" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 15px;">
                            <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0 0 8px 0; color: #333;">producto 4</h3>
                            <p style="font-size: 16px; font-weight: 700; margin: 0; color: #000;">$9.800</p>
                        </div>
                    </div>

                    <div class="prod-item" style="min-width: 250px; flex-shrink: 0; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s ease;">
                        <div style="width: 100%; height: 300px; overflow: hidden;">
                            <img src="{{ asset('imagenes/productos/36/1757986974_68c8c09e9a941_Gemini_Generated_Image_3llsd3llsd3llsd3.png') }}" alt="Producto" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 15px;">
                            <h3 style="font-family: var(--body-font); font-size: 14px; font-weight: 600; text-transform: lowercase; margin: 0 0 8px 0; color: #333;">producto 5</h3>
                            <p style="font-size: 16px; font-weight: 700; margin: 0; color: #000;">$11.200</p>
                        </div>
                    </div>
                </div>

                <button class="prod-nav-next" style="position: absolute; right: -15px; top: 50%; transform: translateY(-50%); z-index: 10; width: 40px; height: 40px; border-radius: 50%; background-color: rgba(0, 0, 0, 0.7); border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- Sección de Producto Destacado con galería --}}
<section class="section-featured-product" style="padding: 60px 0; background-color: #fff;">
    <div class="container">
        <div class="row align-items-center">
            {{-- Columna de imágenes --}}
            <div class="col-12 col-md-6">
                <div class="row">
                    {{-- Thumbnails a la izquierda --}}
                    <div class="col-3">
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div class="product-thumb active" data-image="{{ asset('imagenes/productos/29/1755569156_68a3dc04bebfb_COLAGENO-x-100.jpg') }}" style="width: 100%; aspect-ratio: 1; background-color: #f5f5f5; border-radius: 8px; overflow: hidden; cursor: pointer; border: 2px solid #000; transition: all 0.3s ease;">
                                <img src="{{ asset('imagenes/productos/29/1755569156_68a3dc04bebfb_COLAGENO-x-100.jpg') }}" alt="Imagen 1" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="product-thumb" data-image="{{ asset('imagenes/productos/29/1755569156_68a3dc04c1ba8_images.jpg') }}" style="width: 100%; aspect-ratio: 1; background-color: #f5f5f5; border-radius: 8px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">
                                <img src="{{ asset('imagenes/productos/29/1755569156_68a3dc04c1ba8_images.jpg') }}" alt="Imagen 2" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="product-thumb" data-image="{{ asset('imagenes/productos/33/1757979378_68c8a2f2ba843_Gemini_Generated_Image_f69d6mf69d6mf69d.png') }}" style="width: 100%; aspect-ratio: 1; background-color: #f5f5f5; border-radius: 8px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease;">
                                <img src="{{ asset('imagenes/productos/33/1757979378_68c8a2f2ba843_Gemini_Generated_Image_f69d6mf69d6mf69d.png') }}" alt="Imagen 3" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                    </div>

                    {{-- Imagen principal --}}
                    <div class="col-9">
                        <div id="main-product-image" style="width: 100%; aspect-ratio: 3/4; background-color: #fce4ec; border-radius: 12px; overflow: hidden; position: relative;">
                            <img id="product-main-img" src="{{ asset('imagenes/productos/29/1755569156_68a3dc04bebfb_COLAGENO-x-100.jpg') }}" alt="Producto destacado" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna de información del producto --}}
            <div class="col-12 col-md-6 mt-4 mt-md-0" style="padding-left: 40px;">
                <h2 style="font-family: var(--heading-font); font-size: 32px; font-weight: 700; text-transform: lowercase; margin-bottom: 10px; color: #000;">medias Girl Power</h2>

                <p style="font-size: 14px; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">SKU: AA5665</p>

                <p style="font-size: 28px; font-weight: 700; color: #000; margin-bottom: 30px;">$960,00</p>

                {{-- Selector de color --}}
                <div style="margin-bottom: 25px;">
                    <label style="font-size: 14px; font-weight: 600; text-transform: lowercase; margin-bottom: 12px; display: block; color: #000;">Color</label>
                    <div style="display: flex; gap: 12px;">
                        <div class="color-option active" data-color="blanco" style="width: 40px; height: 40px; border-radius: 50%; background-color: #fff; border: 2px solid #000; cursor: pointer; position: relative; transition: all 0.3s ease;">
                        </div>
                        <div class="color-option" data-color="negro" style="width: 40px; height: 40px; border-radius: 50%; background-color: #000; border: 2px solid transparent; cursor: pointer; position: relative; transition: all 0.3s ease;">
                        </div>
                    </div>
                </div>

                {{-- Cantidad --}}
                <div style="margin-bottom: 30px;">
                    <label style="font-size: 14px; font-weight: 600; text-transform: lowercase; margin-bottom: 12px; display: block; color: #000;">Cantidad</label>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <button class="qty-minus" style="width: 40px; height: 40px; border: 1px solid #ddd; background-color: #fff; border-radius: 50%; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">-</button>
                        <input type="number" class="qty-input" value="1" min="1" style="width: 70px; height: 40px; text-align: center; border: 1px solid #ddd; border-radius: 25px; font-size: 16px; font-weight: 600;">
                        <button class="qty-plus" style="width: 40px; height: 40px; border: 1px solid #ddd; background-color: #fff; border-radius: 50%; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">+</button>
                    </div>
                    <p style="font-size: 12px; color: #666; margin-top: 10px; margin-bottom: 0;">4 en stock</p>
                </div>

                {{-- Botón agregar al carrito --}}
                <button style="width: 100%; padding: 15px; background-color: #d4a5d4; color: #000; border: none; border-radius: 30px; font-size: 16px; font-weight: 600; text-transform: lowercase; cursor: pointer; transition: all 0.3s ease; margin-bottom: 20px;">
                    Agregar al carrito
                </button>

                {{-- Descripción --}}
                <p style="font-size: 14px; color: #666; line-height: 1.6; margin: 0;">
                    Medias de algodón, talle único.
                </p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
/* Home Slider Styles */
.section-slider-home {
    width: 100%;
    position: relative;
    margin: 0;
    padding: 0;
    background-color: #fff;
}

/* Ensure all sections are visible */
body, main {
    background-color: #fff;
    min-height: 100vh;
}

.section-categories,
.section-products,
.section-featured-product {
    position: relative;
    z-index: 1;
    background-color: #fff;
    min-height: 200px;
}

.section-products {
    background-color: #f9f9f9 !important;
}

/* Container fixes */
.container {
    position: relative;
    z-index: 1;
}

.section-slider-home .swiper-container {
    width: 100%;
    height: auto;
}

.slider-slide-content {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.slider-nav-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.slider-nav-btn svg {
    pointer-events: none;
}

/* Hide scrollbar for categories and products */
.categories-scroll::-webkit-scrollbar,
.products-scroll::-webkit-scrollbar {
    display: none;
}

/* Hover effects */
.cat-item:hover,
.prod-item:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}

.cat-nav-prev:hover,
.cat-nav-next:hover,
.prod-nav-prev:hover,
.prod-nav-next:hover {
    background-color: rgba(0, 0, 0, 0.9);
    transform: translateY(-50%) scale(1.1);
}

/* Product gallery styles */
.product-thumb:hover {
    opacity: 0.8;
}

.qty-minus:hover,
.qty-plus:hover {
    background-color: #f5f5f5;
    border-color: #000;
}

.color-option:hover {
    transform: scale(1.1);
}

/* Hide number input arrows */
.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.qty-input[type=number] {
    -moz-appearance: textfield;
}

/* Responsive */
@media (max-width: 768px) {
    .slider-title {
        font-size: 32px !important;
    }

    .slider-slide-content {
        min-height: 400px !important;
    }

    .slider-nav-circles {
        bottom: 20px !important;
    }

    .cat-nav-prev,
    .cat-nav-next,
    .prod-nav-prev,
    .prod-nav-next {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize home main slider
    if (document.querySelector('.js-home-main-slider')) {
        const homeSlider = new Swiper('.js-home-main-slider', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            speed: 800,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
        });

        // Custom navigation buttons
        document.querySelectorAll('.slider-nav-prev').forEach(btn => {
            btn.addEventListener('click', () => homeSlider.slidePrev());
        });

        document.querySelectorAll('.slider-nav-next').forEach(btn => {
            btn.addEventListener('click', () => homeSlider.slideNext());
        });
    }

    // Categories horizontal scroll
    const categoriesScroll = document.querySelector('.categories-scroll');
    const catPrevBtn = document.querySelector('.cat-nav-prev');
    const catNextBtn = document.querySelector('.cat-nav-next');

    if (categoriesScroll && catPrevBtn && catNextBtn) {
        catPrevBtn.addEventListener('click', () => {
            categoriesScroll.scrollBy({
                left: -240,
                behavior: 'smooth'
            });
        });

        catNextBtn.addEventListener('click', () => {
            categoriesScroll.scrollBy({
                left: 240,
                behavior: 'smooth'
            });
        });
    }

    // Products horizontal scroll
    const productsScroll = document.querySelector('.products-scroll');
    const prodPrevBtn = document.querySelector('.prod-nav-prev');
    const prodNextBtn = document.querySelector('.prod-nav-next');

    if (productsScroll && prodPrevBtn && prodNextBtn) {
        prodPrevBtn.addEventListener('click', () => {
            productsScroll.scrollBy({
                left: -290,
                behavior: 'smooth'
            });
        });

        prodNextBtn.addEventListener('click', () => {
            productsScroll.scrollBy({
                left: 290,
                behavior: 'smooth'
            });
        });
    }

    // Product gallery thumbnails
    const productThumbs = document.querySelectorAll('.product-thumb');
    const mainProductImg = document.getElementById('product-main-img');

    productThumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            // Remove active class from all thumbnails
            productThumbs.forEach(t => {
                t.style.border = '2px solid transparent';
                t.classList.remove('active');
            });

            // Add active class to clicked thumbnail
            thumb.style.border = '2px solid #000';
            thumb.classList.add('active');

            // Change main image
            const newImage = thumb.getAttribute('data-image');
            mainProductImg.src = newImage;
        });
    });

    // Color selector
    const colorOptions = document.querySelectorAll('.color-option');

    colorOptions.forEach(option => {
        option.addEventListener('click', () => {
            // Remove active class from all colors
            colorOptions.forEach(c => {
                c.style.border = c.style.backgroundColor === 'rgb(255, 255, 255)'
                    ? '2px solid #ddd'
                    : '2px solid transparent';
                c.classList.remove('active');
            });

            // Add active class to clicked color
            option.style.border = '2px solid #000';
            option.classList.add('active');
        });
    });

    // Quantity controls
    const qtyInput = document.querySelector('.qty-input');
    const qtyMinus = document.querySelector('.qty-minus');
    const qtyPlus = document.querySelector('.qty-plus');

    if (qtyMinus && qtyInput) {
        qtyMinus.addEventListener('click', () => {
            let currentValue = parseInt(qtyInput.value);
            if (currentValue > 1) {
                qtyInput.value = currentValue - 1;
            }
        });
    }

    if (qtyPlus && qtyInput) {
        qtyPlus.addEventListener('click', () => {
            let currentValue = parseInt(qtyInput.value);
            qtyInput.value = currentValue + 1;
        });
    }
});
</script>
@endpush
