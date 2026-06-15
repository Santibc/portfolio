@extends('layouts.app')

@section('header', 'Nuevo empleado')

@section('content')
    <x-page-header
        title="Nuevo empleado"
        subtitle="Registra un empleado de nómina con su salario y datos de seguridad social"
        icon="user-plus"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('empleados.index')">Volver</x-button>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('empleados.store') }}" method="POST" class="max-w-3xl">
        @csrf
        @include('empleados._form', ['empleado' => null, 'metodosOptions' => $metodosOptions])
    </form>
@endsection
