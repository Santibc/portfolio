@extends('layouts.app')

@section('header', 'Editar empleado')

@section('content')
    <x-page-header
        title="Editar empleado"
        :subtitle="$empleado->nombre"
        icon="user-cog"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('empleados.index')">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('empleados.update', $empleado) }}" method="POST" class="max-w-3xl">
        @csrf
        @method('PUT')
        @include('empleados._form', ['empleado' => $empleado, 'metodosOptions' => $metodosOptions])
    </form>
@endsection
