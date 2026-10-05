@extends('layouts.app')

@section('header', 'Editar usuario')

@section('content')
    <x-page-header
        :title="$usuario->name"
        subtitle="Editar datos, rol o contraseña"
        icon="user-cog"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('usuarios.index')">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="max-w-xl">
        @csrf
        @method('PATCH')
        @include('usuarios._form')
    </form>
@endsection
