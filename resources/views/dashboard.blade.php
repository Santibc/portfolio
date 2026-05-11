@extends('layouts.app')
@section('title', 'Inicio')

@section('content')
<div class="space-y-6">

    {{-- Hero --}}
    <div data-reveal class="relative overflow-hidden rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 text-white shadow-soft-lg">
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-10 -left-10 w-56 h-56 rounded-full bg-accent-300/20 blur-3xl"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="brand-script text-2xl text-white/90">¡Hola de nuevo!</p>
                <h1 class="font-display text-2xl md:text-3xl font-bold mt-1">
                    Bienvenido, {{ Auth::user()->name }}
                </h1>
                <p class="text-white/80 mt-1.5 text-sm">
                    Aqui esta el resumen de tu actividad.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold px-4 py-2.5 rounded-xl backdrop-blur transition-all">
                    <x-icon name="user-cog" class="w-4 h-4" />
                    Mi perfil
                </a>
                <a href="{{ route('components.showcase') }}" class="inline-flex items-center gap-2 bg-white text-primary-700 text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-cream-50 transition-all">
                    <x-icon name="component" class="w-4 h-4" />
                    Ver componentes
                </a>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div data-reveal>
            <x-stat-card icon="users" label="Usuarios" value="1" :trend="0" trendLabel="vs mes anterior" color="primary" />
        </div>
        <div data-reveal>
            <x-stat-card icon="shield-check" label="Roles activos" value="1" color="accent" />
        </div>
        <div data-reveal>
            <x-stat-card icon="activity" label="Sesiones" value="—" color="emerald" />
        </div>
        <div data-reveal>
            <x-stat-card icon="zap" label="Estado" value="OK" color="sky" />
        </div>
    </div>

    {{-- Charts + recent --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div data-reveal class="lg:col-span-2">
            <x-card padding="p-6">
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-cream-900 dark:text-cream-50">Actividad de la semana</h3>
                            <p class="text-xs text-cream-600 dark:text-cream-400">Datos de demostracion</p>
                        </div>
                        <x-badge variant="success" icon="trending-up">+12.4%</x-badge>
                    </div>
                </x-slot:header>

                <x-chart
                    type="area"
                    :series="[
                        ['name' => 'Visitas', 'data' => [12, 18, 14, 22, 28, 24, 32]],
                    ]"
                    :options="[
                        'xaxis' => ['categories' => ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom']],
                        'fill' => ['type' => 'gradient', 'gradient' => ['shadeIntensity' => 1, 'opacityFrom' => 0.4, 'opacityTo' => 0.05, 'stops' => [0, 100]]],
                    ]"
                    :height="280"
                />
            </x-card>
        </div>

        <div data-reveal>
            <x-card padding="p-6">
                <x-slot:header>
                    <h3 class="font-semibold text-cream-900 dark:text-cream-50">Ultimas acciones</h3>
                </x-slot:header>

                <ul class="space-y-3">
                    @foreach (['Inicio de sesion' => ['log-in', 'hace 2 min'], 'Perfil actualizado' => ['user-check', 'hace 1 hora'], 'Nuevo usuario admin' => ['user-plus', 'ayer']] as $label => $meta)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex w-9 h-9 rounded-xl bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-200 items-center justify-center shrink-0">
                                <x-icon :name="$meta[0]" class="w-4 h-4" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-cream-900 dark:text-cream-100">{{ $label }}</p>
                                <p class="text-xs text-cream-500">{{ $meta[1] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        </div>
    </div>

    {{-- Quick links --}}
    <div data-reveal>
        <x-card padding="p-6">
            <h3 class="font-semibold text-cream-900 dark:text-cream-50 mb-4">Accesos rapidos</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="{{ route('profile.edit') }}" class="surface-card hover-lift p-4 flex items-center gap-3 group">
                    <span class="inline-flex w-10 h-10 rounded-xl bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-200 items-center justify-center">
                        <x-icon name="user-cog" class="w-5 h-5" />
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-cream-900 dark:text-cream-100">Mi perfil</p>
                        <p class="text-xs text-cream-600 dark:text-cream-400">Datos y foto</p>
                    </div>
                    <x-icon name="arrow-up-right" class="w-4 h-4 text-cream-400 group-hover:text-primary-600 transition-colors" />
                </a>
                <a href="{{ route('components.showcase') }}" class="surface-card hover-lift p-4 flex items-center gap-3 group">
                    <span class="inline-flex w-10 h-10 rounded-xl bg-accent-100 text-accent-700 dark:bg-accent-900/40 dark:text-accent-200 items-center justify-center">
                        <x-icon name="component" class="w-5 h-5" />
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-cream-900 dark:text-cream-100">Componentes UI</p>
                        <p class="text-xs text-cream-600 dark:text-cream-400">Showcase visual</p>
                    </div>
                    <x-icon name="arrow-up-right" class="w-4 h-4 text-cream-400 group-hover:text-primary-600 transition-colors" />
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full surface-card hover-lift p-4 flex items-center gap-3 group text-left">
                        <span class="inline-flex w-10 h-10 rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200 items-center justify-center">
                            <x-icon name="log-out" class="w-5 h-5" />
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-cream-900 dark:text-cream-100">Cerrar sesion</p>
                            <p class="text-xs text-cream-600 dark:text-cream-400">Salir de la cuenta</p>
                        </div>
                        <x-icon name="arrow-up-right" class="w-4 h-4 text-cream-400 group-hover:text-rose-500 transition-colors" />
                    </button>
                </form>
            </div>
        </x-card>
    </div>

</div>
@endsection
