@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="space-y-6">
    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 p-8 text-white shadow-card">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-16 h-64 w-64 rounded-full bg-primary-300/20 blur-3xl"></div>

        <div class="relative flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-medium backdrop-blur">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-white"></span>
                    {{ now()->isoFormat('dddd, D [de] MMMM') }}
                </div>
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Hola, {{ Auth::user()->name }} 👋
                </h1>
                <p class="mt-2 max-w-lg text-sm text-white/80 sm:text-base">
                    Bienvenido de vuelta. Aquí tienes un resumen de tu actividad.
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-4 py-2 text-sm font-medium backdrop-blur transition hover:bg-white/20">
                    <i class="bi bi-person-gear"></i>
                    Mi perfil
                </a>
            </div>
        </div>
    </section>

    {{-- Bento grid de stats --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card transition hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                    <i class="bi bi-people text-lg"></i>
                </div>
                <span class="inline-flex items-center gap-0.5 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-950 dark:text-green-400">
                    <i class="bi bi-arrow-up text-[10px]"></i> 0%
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold tracking-tight">—</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Usuarios totales</div>
            </div>
        </div>

        <div class="card transition hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 text-sky-600 dark:bg-sky-950 dark:text-sky-400">
                    <i class="bi bi-graph-up-arrow text-lg"></i>
                </div>
                <span class="inline-flex items-center gap-0.5 rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    —
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold tracking-tight">—</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Actividad</div>
            </div>
        </div>

        <div class="card transition hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-950 dark:text-violet-400">
                    <i class="bi bi-check2-circle text-lg"></i>
                </div>
                <span class="inline-flex items-center gap-0.5 rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    —
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold tracking-tight">—</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Completados</div>
            </div>
        </div>

        <div class="card transition hover:-translate-y-0.5 hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-400">
                    <i class="bi bi-bell text-lg"></i>
                </div>
                <span class="inline-flex items-center gap-0.5 rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    —
                </span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-bold tracking-tight">0</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Notificaciones</div>
            </div>
        </div>
    </section>

    {{-- Bento de contenido --}}
    <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Card grande --}}
        <div class="card lg:col-span-2">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight">Bienvenido al sistema</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Proyecto base listo para construir encima.</p>
                </div>
                <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 dark:bg-primary-950 dark:text-primary-400">
                    v1.0
                </span>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-dashed border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="mb-2 flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <div class="text-sm font-medium">Empieza a construir</div>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Crea modelos, controladores y vistas para tu nueva funcionalidad.
                    </p>
                </div>
                <div class="rounded-xl border border-dashed border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="mb-2 flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                        <i class="bi bi-puzzle"></i>
                    </div>
                    <div class="text-sm font-medium">Componentes listos</div>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Alertas, botones, modales, tablas y más en resources/views/components.
                    </p>
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="card">
            <h2 class="mb-4 text-lg font-semibold tracking-tight">Accesos rápidos</h2>
            <div class="space-y-2">
                <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 rounded-lg p-3 transition hover:bg-zinc-50 dark:hover:bg-zinc-800">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium">Mi perfil</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Editar datos y contraseña</div>
                    </div>
                    <i class="bi bi-chevron-right text-zinc-400 transition group-hover:translate-x-0.5"></i>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
