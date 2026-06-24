@extends('layouts.app')

@section('header', 'Gastos fijos')

@section('content')
    <x-page-header
        title="Gastos fijos mensuales"
        subtitle="Arriendo, servicios y demás pagos fijos del negocio"
        icon="receipt"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="settings"
                :href="route('gastos-fijos.conceptos.index')"
            >
                Conceptos
            </x-button>
            <x-button
                variant="primary"
                icon="plus"
                :href="route('gastos-fijos.create')"
            >
                Registrar gasto fijo
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
        <x-stat-card
            icon="receipt"
            color="rose"
            label="Total este mes"
            :value="'$ ' . number_format($totalMes, 0, ',', '.')"
        />
        <x-stat-card
            icon="calendar"
            color="primary"
            label="Mes en curso"
            :value="ucfirst(now()->translatedFormat('F Y'))"
        />
    </div>

    <x-data-table
        :columns="$columns"
        :rows="$rows"
        :searchable="true"
        :paginate="true"
        :perPage="5"
        :filters="[['key' => 'metodo', 'label' => 'Método']]"
        empty="Aún no hay gastos fijos registrados. Crea el primero con el botón “Registrar gasto fijo”."
    />
@endsection
