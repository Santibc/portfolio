@extends('layouts.app')

@section('header', 'Empleados')

@section('content')
    <x-page-header
        title="Empleados"
        subtitle="Personal de nómina: salario, seguridad social y ahorro acumulado"
        icon="users"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="gauge" :href="route('nomina-dashboard.index')">Dashboard</x-button>
            <x-button variant="primary" icon="plus" :href="route('empleados.create')">Nuevo empleado</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table
        :columns="$columns"
        :rows="$rows"
        :searchable="true"
        :paginate="true"
        :perPage="15"
        empty="Aún no hay empleados. Crea el primero con el botón “Nuevo empleado”."
    />
@endsection
