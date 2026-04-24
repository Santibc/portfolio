@extends('layouts.app')

@section('title', 'Administración')

@section('content')
    <x-manzer.page-header
        title="Administración"
        description="Configura los catálogos y datos de la empresa emisora."
    />

    @if (session('success'))
        <div class="mb-4">
            <x-manzer.alert type="success" :message="session('success')" dismissible />
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4">
            <x-manzer.alert type="error" :message="session('error')" dismissible />
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('admin.monedas.index') }}" class="block focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-xl">
            <x-manzer.stat-card
                icon="currency-exchange"
                :value="$counts['monedas']"
                label="Monedas"
                variant="primary"
            />
        </a>

        <a href="{{ route('admin.impuestos.index') }}" class="block focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-xl">
            <x-manzer.stat-card
                icon="percent"
                :value="$counts['impuestos']"
                label="Impuestos"
                variant="info"
            />
        </a>

        <a href="{{ route('admin.tipos-descuento.index') }}" class="block focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-xl">
            <x-manzer.stat-card
                icon="tag"
                :value="$counts['tipos_descuento']"
                label="Tipos de descuento"
                variant="warning"
            />
        </a>

        <a href="{{ route('admin.incoterms.index') }}" class="block focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-xl">
            <x-manzer.stat-card
                icon="globe2"
                :value="$counts['incoterms']"
                label="Incoterms"
                variant="success"
            />
        </a>

        <a href="{{ route('admin.puertos.index') }}" class="block focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-xl">
            <x-manzer.stat-card
                icon="geo-alt"
                :value="$counts['puertos']"
                label="Puertos"
                variant="danger"
            />
        </a>

        <a href="{{ route('admin.tipos-pago.index') }}" class="block focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-xl">
            <x-manzer.stat-card
                icon="credit-card"
                :value="$counts['tipos_pago']"
                label="Tipos de pago"
                variant="secondary"
            />
        </a>
    </div>

    <div class="mt-8">
        <a href="{{ route('admin.empresa.edit') }}" class="block focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-xl">
            <div class="card transition hover:-translate-y-0.5 hover:shadow-lg border border-primary-200 dark:border-primary-900">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                        <i class="bi bi-building text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-lg font-semibold tracking-tight">Datos de empresa, DIAN y banco</div>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            Razón social, NIT, resoluciones DIAN, cuenta bancaria y contacto financiero.
                        </p>
                    </div>
                    <i class="bi bi-chevron-right text-zinc-400"></i>
                </div>
            </div>
        </a>
    </div>
@endsection
