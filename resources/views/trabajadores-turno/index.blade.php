@extends('layouts.app')

@section('header', 'Trabajadores de turno')

@section('content')
    <x-page-header
        title="Trabajadores de turno"
        subtitle="Personas que reciben pago diario por turno desde la caja"
        icon="users"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="arrow-left"
                :href="route('gastos.index')"
            >
                Volver a gastos
            </x-button>
            <x-button
                variant="primary"
                icon="plus"
                :href="route('trabajadores-turno.create')"
            >
                Nuevo trabajador
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table
        :columns="$columns"
        :rows="$rows"
        :searchable="true"
        :paginate="true"
        :perPage="15"
        empty="Aún no hay trabajadores de turno. Crea el primero con el botón “Nuevo trabajador”."
    />
@endsection
