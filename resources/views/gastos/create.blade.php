@extends('layouts.app')

@section('header', 'Nuevo gasto')

@section('content')
    <x-page-header
        title="Nuevo gasto"
        subtitle="Registra una salida de dinero del turno de caja actual"
        icon="wallet"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="arrow-left"
                :href="route('gastos.index')"
            >
                Volver
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($turnoActivo === null)
        <x-card>
            <x-empty-state
                icon="lock"
                title="No hay caja abierta"
                description="Para registrar un gasto primero debes abrir un turno de caja."
            >
                <x-slot:actions>
                    <x-button variant="primary" icon="unlock" :href="route('caja.index')">
                        Abrir caja
                    </x-button>
                </x-slot:actions>
            </x-empty-state>
        </x-card>
    @else
        <form action="{{ route('gastos.store') }}" method="POST" class="max-w-2xl">
            @csrf

            <div class="mb-4">
                <x-alert variant="info" title="Turno activo">
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                        <span><strong>Turno #{{ $turnoActivo->id }}</strong></span>
                        <span>Abierto: {{ $turnoActivo->abierto_en->format('Y-m-d H:i') }}</span>
                        <span>Ventas: <strong class="tabular-nums">{{ $turnoActivo->total_ventas_formateado }}</strong></span>
                    </div>
                </x-alert>
            </div>

            @include('gastos._form')
        </form>
    @endif
@endsection
