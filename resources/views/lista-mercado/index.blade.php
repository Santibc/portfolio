@extends('layouts.app')

@section('header', 'Lista Mercado')

@section('content')
    <x-page-header
        title="Lista Mercado"
        subtitle="Mercado planificado por tipo de lugar"
        icon="clipboard-list"
    />

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('lista-mercado.plantilla.index') }}"
           class="inline-flex items-center gap-2 font-semibold rounded-xl px-4 py-2.5 text-sm bg-white hover:bg-cream-100 text-cream-900 border border-cream-300 dark:bg-cream-900 dark:hover:bg-cream-800 dark:text-cream-100 dark:border-cream-700 transition-all">
            <x-icon name="list-checks" class="w-4 h-4" />
            Plantilla
        </a>

        @if ($mercado)
            <form action="{{ route('lista-mercado.cancelar', $mercado) }}" method="POST"
                  onsubmit="event.preventDefault(); return swalConfirm(this, {title: '¿Cancelar este mercado?', text: 'Los registros ya hechos se conservarán.', icon: 'warning', confirmButtonText: 'Sí, cancelar', confirmButtonColor: '#e11d48'});">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 font-semibold rounded-xl px-3 py-1.5 text-sm bg-transparent hover:bg-cream-100 text-cream-800 dark:hover:bg-cream-800 dark:text-cream-200 transition-all">
                    <x-icon name="x" class="w-4 h-4" />
                    Cancelar mercado
                </button>
            </form>
            <form action="{{ route('lista-mercado.finalizar', $mercado) }}" method="POST"
                  onsubmit="event.preventDefault(); return swalConfirm(this, {title: '¿Finalizar mercado ahora?', text: 'Los items pendientes se marcarán como saltados.', icon: 'question', confirmButtonText: 'Sí, finalizar'});">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 font-semibold rounded-xl px-3 py-1.5 text-sm bg-transparent hover:bg-cream-100 text-cream-800 dark:hover:bg-cream-800 dark:text-cream-200 transition-all">
                    <x-icon name="check-circle" class="w-4 h-4" />
                    Finalizar mercado
                </button>
            </form>
        @endif
    </div>

    @if (! $mercado)
        <div class="surface-card p-6">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="flex-1 space-y-3">
                    <h2 class="text-xl font-bold text-cream-900 dark:text-cream-50">{{ $lista->nombre }}</h2>

                    @if ($resumenLista->total === 0)
                        <p class="text-sm text-cream-600 dark:text-cream-400">
                            La lista aún no tiene productos.
                            <a href="{{ route('lista-mercado.plantilla.index') }}"
                               class="text-primary-700 hover:underline dark:text-primary-300">
                                Agregar productos
                            </a>.
                        </p>
                    @endif

                    @if ($resumenLista->total > 0)
                        <p class="text-sm text-cream-600 dark:text-cream-400">
                            {{ $resumenLista->total }} {{ $resumenLista->total === 1 ? 'producto' : 'productos' }}
                            distribuidos en {{ $resumenLista->porTipo->count() }}
                            {{ $resumenLista->porTipo->count() === 1 ? 'lugar' : 'lugares' }}.
                        </p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach ($resumenLista->porTipo as $nombre => $cnt)
                                <span class="inline-flex items-center font-semibold rounded-full bg-primary-100 text-primary-800 dark:bg-primary-900/40 dark:text-primary-200 text-xs px-2.5 py-1 gap-1.5">
                                    {{ $nombre }} · {{ $cnt }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <form action="{{ route('lista-mercado.iniciar') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit"
                            @disabled($resumenLista->total === 0)
                            class="inline-flex items-center gap-2 font-semibold rounded-xl text-base px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white shadow-soft border border-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <x-icon name="play" class="w-4 h-4" />
                        Iniciar mercado
                    </button>
                </form>
            </div>
        </div>
    @endif

    @if ($mercado)
        <p class="mb-4 text-sm text-cream-700 dark:text-cream-300">
            Mercado iniciado <span class="font-semibold">{{ $mercado->iniciado_en->diffForHumans() }}</span>
        </p>

        @if ($tipos->isEmpty())
            <div class="surface-card p-8 text-center">
                <p class="text-cream-700 dark:text-cream-300">No hay items en este mercado. Verifica que la lista tenga productos.</p>
            </div>
        @endif

        @if ($tipos->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($tipos as $g)
                    @php
                        $isDone = $g->finalizado;
                        $borderCls = $isDone
                            ? 'opacity-60 border-emerald-300 dark:border-emerald-700'
                            : 'border-cream-200 dark:border-cream-800 hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-md';
                        $iconName = $isDone ? 'check-circle' : 'map-pin';
                        $iconColor = $isDone ? 'text-emerald-500' : 'text-primary-500';
                        $progressColor = $isDone ? 'bg-emerald-500' : 'bg-primary-500';
                        $progressPct = $g->progreso;
                    @endphp
                    <a href="{{ $isDone ? '#' : route('lista-mercado.tipo', $g->tipo) }}"
                       @if ($isDone) onclick="return false;" @endif
                       class="block rounded-2xl bg-white dark:bg-cream-900/40 border {{ $borderCls }} p-5 transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-bold text-cream-900 dark:text-cream-50 flex items-center gap-2">
                                <x-icon :name="$iconName" :class="'w-5 h-5 ' . $iconColor" />
                                {{ $g->tipo->nombre }}
                            </h3>
                            <span class="text-xs font-semibold text-cream-600 dark:text-cream-400">
                                {{ $g->completados }}/{{ $g->total }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <div class="w-full h-2 rounded-full bg-cream-200 dark:bg-cream-800 overflow-hidden">
                                <div class="h-full {{ $progressColor }} rounded-full transition-all duration-500" style="width: {{ $progressPct }}%"></div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-1.5">
                            @if ($g->registrados > 0)
                                <span class="inline-flex items-center font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 text-[11px] px-2 py-0.5 gap-1">
                                    <x-icon name="check" class="w-3 h-3" />
                                    {{ $g->registrados }} registrados
                                </span>
                            @endif
                            @if ($g->saltados > 0)
                                <span class="inline-flex items-center font-semibold rounded-full bg-cream-200 text-cream-800 dark:bg-cream-800 dark:text-cream-200 text-[11px] px-2 py-0.5 gap-1">
                                    <x-icon name="skip-forward" class="w-3 h-3" />
                                    {{ $g->saltados }} saltados
                                </span>
                            @endif
                            @if ($g->pendientes > 0)
                                <span class="inline-flex items-center font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200 text-[11px] px-2 py-0.5 gap-1">
                                    <x-icon name="clock" class="w-3 h-3" />
                                    {{ $g->pendientes }} pendientes
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    @if (session('tipo_completado'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = document.documentElement.classList.contains('dark');
                Swal.fire({
                    icon: 'success',
                    title: '¡{{ session('tipo_completado') }} completado!',
                    text: 'Continúa con el siguiente lugar.',
                    timer: 2500,
                    showConfirmButton: false,
                    background: isDark ? '#1a1610' : '#fffdfa',
                    color: isDark ? '#fbf5e9' : '#3e2723',
                });
            });
        </script>
    @endif
@endpush
