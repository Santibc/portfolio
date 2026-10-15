{{-- Formulario genérico (crear/editar) de Productos / Proceso / Galería --}}
@extends('layouts.app')

@php
    $editando = $elemento->exists;
    $accion = ($editando ? 'Editar ' : 'Agregar ').$vista['singular'];
@endphp

@section('title', 'Página web · '.$accion)
@section('header', 'Página web')

@section('content')
    @include('pagina-web._encabezado', ['subtitulo' => $vista['titulo'].' › '.$accion])

    <form method="POST" enctype="multipart/form-data"
          action="{{ $editando ? route($vista['ruta'].'.update', $elemento) : route($vista['ruta'].'.store') }}">
        @csrf
        @if ($editando)
            @method('PUT')
        @endif

        <x-card>
            <div class="grid gap-6 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <x-input-imagen :actual="$elemento->imagen_url ?: null" :proporcion="$vista['proporcion']" :quitable="$editando" />
                </div>

                <div class="space-y-4 lg:col-span-7">
                    @include($vista['campos'])

                    <x-input label="Posición" name="orden" type="number" min="0" max="999" :value="old('orden', $elemento->orden)"
                             :hint="'Número más bajo = aparece primero. Vacío = '.($editando ? 'no cambia.' : 'al final.')" />
                </div>
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-2">
                    <x-button variant="ghost" :href="route($vista['ruta'].'.index')">Cancelar</x-button>
                    <x-button type="submit" icon="save">{{ $editando ? 'Guardar cambios' : 'Agregar' }}</x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>
@endsection
