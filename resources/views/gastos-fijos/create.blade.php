@extends('layouts.app')

@section('header', 'Nuevo gasto fijo')

@section('content')
    <x-page-header
        title="Nuevo gasto fijo"
        subtitle="Registra un pago fijo del negocio (arriendo, servicios, etc.)"
        icon="receipt"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('gastos-fijos.index')">
                Volver
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if (empty($conceptosOptions))
        <x-card>
            <x-empty-state
                icon="settings"
                title="No hay conceptos activos"
                description="Primero crea al menos un concepto de gasto fijo (arriendo, energía, agua...)."
            >
                <x-slot:actions>
                    <x-button variant="primary" icon="plus" :href="route('gastos-fijos.conceptos.index')">
                        Gestionar conceptos
                    </x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-card>
    @else
        <form action="{{ route('gastos-fijos.store') }}" method="POST" class="max-w-xl">
            @csrf
            @include('gastos-fijos._form')
        </form>
    @endif
@endsection
