@extends('layouts.landing')

@section('contenido')
    @include('landing.partials.navegacion')

    <main id="contenido">
        @include('landing.partials.inicio')
        @include('landing.partials.cinta')
        @include('landing.partials.que-es')
        @include('landing.partials.sello')
        @include('landing.partials.como-se-arma')
        @include('landing.partials.sello')
        @include('landing.partials.menu')
        @include('landing.partials.sello')
        @include('landing.partials.el-local')
        @include('landing.partials.sello')
        @include('landing.partials.ubicacion')
        @include('landing.partials.sello')
        @include('landing.partials.pedir')
    </main>

    @include('landing.partials.pie')

    {{-- Botón flotante de pedido (aparece cuando el botón principal sale de pantalla) --}}
    <x-button variant="pedido" size="lg" :href="$enlaces['whatsapp']" target="_blank" rel="noopener" data-wa-flotante
              aria-label="Pedir por WhatsApp"
              class="wa-flotante fixed bottom-[max(1rem,env(safe-area-inset-bottom))] right-[max(1rem,env(safe-area-inset-right))] z-50 !rounded-full !px-4 sm:!px-5">
        <x-brand-icon name="whatsapp" class="h-7 w-7" />
        <span class="hidden sm:inline">Pedir</span>
    </x-button>

    {{-- Lienzo del paquete 3D (decorativo: todo el contenido vive en el HTML) --}}
    <div id="bag-stage" aria-hidden="true"
         data-telefono="{{ $negocio['whatsapp_visible'] }}" data-direccion="{{ $negocio['direccion'] }}"
         data-barrio="{{ $negocio['barrio'] }}" data-ciudad="{{ $negocio['ciudad'] }}" data-instagram="{{ $negocio['instagram_usuario'] }}">
        <canvas id="bag-canvas"></canvas>
    </div>
@endsection
