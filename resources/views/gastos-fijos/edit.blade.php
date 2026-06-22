@extends('layouts.app')

@section('header', 'Editar gasto fijo')

@section('content')
    <x-page-header
        title="Editar gasto fijo"
        subtitle="Modifica el concepto, valor, método o fecha de pago"
        icon="receipt"
    >
        <x-slot:actions>
            <x-button variant="ghost" icon="arrow-left" :href="route('gastos-fijos.index')">
                Volver
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <form action="{{ route('gastos-fijos.update', $gastoFijo) }}" method="POST" class="max-w-xl">
        @csrf
        @method('PUT')
        @include('gastos-fijos._form')
    </form>
@endsection
