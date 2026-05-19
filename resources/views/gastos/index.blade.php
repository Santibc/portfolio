@extends('layouts.app')

@section('header', 'Gastos')

@section('content')
    <x-page-header
        title="Gastos"
        subtitle="Salidas de dinero registradas durante los turnos de caja"
        icon="wallet"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="users"
                :href="route('trabajadores-turno.index')"
            >
                Trabajadores de turno
            </x-button>
            <x-button
                variant="primary"
                icon="plus"
                :href="route('gastos.create')"
            >
                Nuevo gasto
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table
        :columns="$columns"
        :rows="$rows"
        :searchable="true"
        :paginate="true"
        :perPage="15"
        empty="Aún no hay gastos registrados. Crea el primero con el botón “Nuevo gasto”."
    />
@endsection
