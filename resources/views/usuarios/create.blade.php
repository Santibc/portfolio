@extends('layouts.app')

@section('header', 'Nuevo usuario')

@section('content')
    <x-page-header
        title="Nuevo usuario"
        subtitle="Crea el acceso de una persona y elige su rol"
        icon="user-plus"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('usuarios.index')">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('usuarios.store') }}" method="POST" class="max-w-xl">
        @csrf
        @include('usuarios._form')
    </form>
@endsection
