@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
    <div class="container-fluid py-4">
        <x-manzer.page-header
            title="Facturas"
            description="Borradores, emitidas y anuladas — filtrables por cliente, estado y fecha."
        >
            <x-slot name="actions">
                <x-manzer.button
                    variant="secondary"
                    icon="arrow-left"
                    href="{{ route('dashboard') }}"
                >
                    Volver
                </x-manzer.button>
                <x-manzer.button
                    variant="primary"
                    icon="plus-lg"
                    href="{{ route('facturacion.facturas.create') }}"
                >
                    Nueva factura
                </x-manzer.button>
            </x-slot>
        </x-manzer.page-header>

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

        {{-- Filtros --}}
        <div class="card mb-4 p-4">
            <form action="{{ route('facturacion.facturas.index') }}" method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
                <div class="md:col-span-2">
                    <label for="desde" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <i class="bi bi-calendar-event mr-1"></i>Desde
                    </label>
                    <input
                        type="date"
                        id="desde"
                        name="desde"
                        value="{{ $filtros['desde'] ?? '' }}"
                        class="input"
                    >
                </div>

                <div class="md:col-span-2">
                    <label for="hasta" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <i class="bi bi-calendar-event mr-1"></i>Hasta
                    </label>
                    <input
                        type="date"
                        id="hasta"
                        name="hasta"
                        value="{{ $filtros['hasta'] ?? '' }}"
                        class="input"
                    >
                </div>

                <div class="md:col-span-2">
                    <label for="estado" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <i class="bi bi-flag mr-1"></i>Estado
                    </label>
                    <select id="estado" name="estado" class="input">
                        @foreach (['' => 'Todos', 'borrador' => 'Borrador', 'emitida' => 'Emitida', 'enviada' => 'Enviada', 'pagada' => 'Pagada', 'anulada' => 'Anulada'] as $v => $l)
                            <option value="{{ $v }}" @selected(($filtros['estado'] ?? '') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label for="cliente_id" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <i class="bi bi-person mr-1"></i>Cliente
                    </label>
                    <select id="cliente_id" name="cliente_id" class="input">
                        <option value="">Todos</option>
                        @foreach ($clientes->pluck('nombre', 'id') as $id => $nombre)
                            <option value="{{ $id }}" @selected((string) ($filtros['cliente_id'] ?? '') === (string) $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="es_electronica" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        <i class="bi bi-file-earmark-text mr-1"></i>Electrónica
                    </label>
                    <select id="es_electronica" name="es_electronica" class="input">
                        @foreach (['' => 'Todas', '1' => 'Electrónicas', '0' => 'No electrónicas'] as $v => $l)
                            <option value="{{ $v }}" @selected((string) ($filtros['es_electronica'] ?? '') === (string) $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-1">
                    <x-manzer.button type="submit" variant="primary" icon="funnel" class="w-full">
                        Filtrar
                    </x-manzer.button>
                </div>
            </form>
        </div>

        {{-- Tabla / vacío --}}
        @if ($facturas->isEmpty())
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-12 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-950/40 dark:text-primary-400">
                    <i class="bi bi-receipt text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">No hay facturas que coincidan</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Ajusta los filtros o crea una nueva factura.</p>
                <div class="mt-4">
                    <x-manzer.button variant="primary" icon="plus-lg" href="{{ route('facturacion.facturas.create') }}">
                        Crear primera factura
                    </x-manzer.button>
                </div>
            </div>
        @else
            <x-manzer.data-table :headers="['#', 'Fecha', 'Cliente', 'Moneda', 'Total', 'Total COP', 'Estado', 'Electrónica', 'Acciones']">
                @foreach ($facturas as $f)
                    @php
                        $estadoVariant = match ($f->estado) {
                            'borrador' => 'secondary',
                            'emitida' => 'info',
                            'enviada' => 'primary',
                            'pagada' => 'success',
                            'anulada' => 'danger',
                            default => 'secondary',
                        };
                        $esBorrador = $f->estado === 'borrador';
                    @endphp

                    <x-manzer.table-row>
                        <x-manzer.table-cell>
                            <span class="font-mono font-semibold">{{ $f->numero_siigo ?? $f->numero_interno }}</span>
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $f->fecha?->format('d/m/Y') ?? '—' }}
                            </span>
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ $f->cliente?->nombre ?? '—' }}</div>
                            @if ($f->cliente)
                                @if (($f->cliente->tipo ?? null) === 'internacional')
                                    <x-manzer.badge variant="info" text="Internacional" />
                                @else
                                    <x-manzer.badge variant="secondary" text="Nacional" />
                                @endif
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            <span class="font-mono text-xs">{{ $f->moneda?->codigo ?? '—' }}</span>
                        </x-manzer.table-cell>

                        <x-manzer.table-cell class="font-semibold">
                            {{ $f->moneda?->simbolo }} {{ number_format((float) $f->total, 2, ',', '.') }}
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            {{ $f->total_cop ? '$ ' . number_format((float) $f->total_cop, 0, ',', '.') : '—' }}
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            <x-manzer.badge :variant="$estadoVariant" :text="ucfirst($f->estado)" />
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            @if ($f->es_electronica)
                                <x-manzer.badge variant="success" text="Sí" />
                            @else
                                <x-manzer.badge variant="secondary" text="No" />
                            @endif
                        </x-manzer.table-cell>

                        <x-manzer.table-cell>
                            <div class="flex flex-wrap items-center gap-2">
                                <a
                                    href="{{ route('facturacion.facturas.pdf', $f) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn bg-white text-primary-600 ring-1 ring-inset ring-primary-200 hover:bg-primary-50 dark:bg-transparent dark:text-primary-400 dark:ring-primary-900 dark:hover:bg-primary-950 text-xs px-3 py-1.5"
                                    aria-label="Ver PDF"
                                >
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    Ver PDF
                                </a>

                                {{-- Botón: Datos de envío (siempre disponible, incluso después de emitida) --}}
                                <button
                                    type="button"
                                    onclick="abrirModalEnvio({{ $f->id }}, {{ json_encode(['po_numero' => $f->po_numero, 'awb' => $f->awb, 'shipper' => $f->shipper]) }})"
                                    class="btn bg-white text-amber-700 ring-1 ring-inset ring-amber-300 hover:bg-amber-50 dark:bg-transparent dark:text-amber-300 dark:ring-amber-700 dark:hover:bg-amber-950 text-xs px-3 py-1.5"
                                    aria-label="Datos de envío"
                                >
                                    <i class="bi bi-truck"></i>
                                    Envío
                                </button>

                                @if ($esBorrador)
                                    <x-manzer.button
                                        variant="outline"
                                        size="sm"
                                        icon="pencil"
                                        href="{{ route('facturacion.facturas.edit', $f) }}"
                                        aria-label="Editar"
                                    >
                                        Editar
                                    </x-manzer.button>

                                    {{-- Botón: Emitir electrónica (solo borrador) --}}
                                    <form
                                        action="{{ route('facturacion.facturas.emitir-electronica', $f) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="event.preventDefault(); const f=this; window.Swal.fire({title:'¿Emitir factura electrónica?',text:'La factura se enviará a Siigo y a la DIAN para timbrado. Una vez emitida no podrás editarla.',icon:'question',showCancelButton:true,confirmButtonText:'Sí, emitir',cancelButtonText:'Cancelar',confirmButtonColor:'#f97316'}).then(r=>{if(r.isConfirmed){Swal.fire({title:'Timbrando...',html:'Enviando a Siigo y DIAN',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});f.submit();}});"
                                    >
                                        @csrf
                                        <x-manzer.button
                                            type="submit"
                                            variant="primary"
                                            size="sm"
                                            icon="cloud-upload"
                                            aria-label="Emitir electrónica"
                                        >
                                            Emitir DIAN
                                        </x-manzer.button>
                                    </form>

                                    <form
                                        action="{{ route('facturacion.facturas.destroy', $f) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="event.preventDefault(); const f=this; window.Swal.fire({title:'¿Eliminar factura?',text:'Esta acción no se puede deshacer.',icon:'warning',showCancelButton:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar',confirmButtonColor:'#dc2626'}).then(r=>{if(r.isConfirmed)f.submit();});"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <x-manzer.button
                                            type="submit"
                                            variant="danger"
                                            size="sm"
                                            icon="trash"
                                            aria-label="Eliminar"
                                        >
                                            Eliminar
                                        </x-manzer.button>
                                    </form>
                                @endif
                            </div>
                        </x-manzer.table-cell>
                    </x-manzer.table-row>
                @endforeach
            </x-manzer.data-table>

            <div class="mt-4">
                {{ $facturas->appends($filtros ?? [])->links() }}
            </div>
        @endif
    </div>

    {{-- Modal: Editar datos de envío (PO, AWB, Shipper) --}}
    <div
        x-data="{ open: false, facturaId: null, po: '', awb: '', shipper: '' }"
        x-on:abrir-modal-envio.window="
            open = true;
            facturaId = $event.detail.id;
            po = $event.detail.data.po_numero ?? '';
            awb = $event.detail.data.awb ?? '';
            shipper = $event.detail.data.shipper ?? '';
        "
        x-on:keydown.escape.window="open = false"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
        role="dialog"
        aria-modal="true"
    >
        <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-zinc-900/60 backdrop-blur-sm"></div>
        <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-800">
            <form :action="`{{ url('facturacion/facturas') }}/${facturaId}/datos-envio`" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PATCH')

                <div class="flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-800">
                    <h2 class="text-lg font-semibold tracking-tight">
                        <i class="bi bi-truck mr-1"></i>
                        Datos de envío
                    </h2>
                    <button type="button" @click="open = false" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Puedes editar estos campos en cualquier momento — se usan en la plantilla de la factura pero no afectan el timbrado DIAN.
                </p>

                <div class="space-y-3">
                    <div>
                        <label for="modal-po_numero" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            <i class="bi bi-hash mr-1"></i>PO# (Orden de compra)
                        </label>
                        <input type="text" id="modal-po_numero" name="po_numero" x-model="po" maxlength="60" placeholder="Ej: PO-2026-04567" class="input mt-1">
                    </div>
                    <div>
                        <label for="modal-awb" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            <i class="bi bi-airplane mr-1"></i>AWB (Guía aérea)
                        </label>
                        <input type="text" id="modal-awb" name="awb" x-model="awb" maxlength="60" placeholder="Ej: 123-45678901" class="input mt-1">
                    </div>
                    <div>
                        <label for="modal-shipper" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            <i class="bi bi-box-seam mr-1"></i>Shipper (Transportista)
                        </label>
                        <input type="text" id="modal-shipper" name="shipper" x-model="shipper" maxlength="100" placeholder="Ej: DHL Express" class="input mt-1">
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                    <button type="button" @click="open = false" class="btn btn-ghost text-sm px-4 py-2">Cancelar</button>
                    <button type="submit" class="btn btn-primary text-sm px-4 py-2">
                        <i class="bi bi-check-lg mr-1"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Dispara el evento para abrir el modal con los datos de la factura seleccionada.
            function abrirModalEnvio(id, data) {
                window.dispatchEvent(new CustomEvent('abrir-modal-envio', { detail: { id, data } }));
            }
        </script>
    @endpush
@endsection
