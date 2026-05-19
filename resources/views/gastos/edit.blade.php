@extends('layouts.app')

@section('header', 'Editar gasto')

@section('content')
    <x-page-header
        title="Editar gasto"
        :subtitle="'Turno #' . $gasto->turno_caja_id . ' · ' . $gasto->created_at->format('Y-m-d H:i')"
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

    <form action="{{ route('gastos.update', $gasto) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PATCH')

        @include('gastos._form')
    </form>
@endsection
