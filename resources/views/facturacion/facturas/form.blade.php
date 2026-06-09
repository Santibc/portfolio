@extends('layouts.app')

@section('title', $factura->exists ? 'Editar factura' : 'Nueva factura')

@section('content')
    @php
        $esEdit = $factura->exists;
        $esEditable = $esEdit ? $factura->esEditable() : true;
        $action = $esEdit
            ? route('facturacion.facturas.update', $factura)
            : route('facturacion.facturas.store');

        $estadoVariant = match ($factura->estado ?? 'borrador') {
            'borrador' => 'secondary',
            'emitida' => 'info',
            'enviada' => 'primary',
            'pagada' => 'success',
            'anulada' => 'danger',
            default => 'secondary',
        };

        $monedaCopId = optional($monedas->firstWhere('codigo', 'COP'))->id;
        $monedaInicialId = $factura->moneda_id ?? $monedaCopId;
    @endphp

    <div
        class="container-fluid py-4"
        x-data="facturaForm({
            items: @js($factura->items?->map(fn($i) => [
                'producto_id' => $i->producto_id,
                'referencia' => $i->referencia,
                'descripcion' => $i->descripcion,
                'color' => $i->color,
                'composicion' => $i->composicion,
                'codigo_pa' => $i->codigo_pa,
                'cantidad' => (float) $i->cantidad,
                'precio_unitario' => (float) $i->precio_unitario,
                'descuento' => (float) $i->descuento,
                'descuento_tipo' => $i->descuento_tipo ?? 'valor',
                'impuesto_porcentaje' => (float) $i->impuesto_porcentaje,
                'tallas' => is_array($i->tallas_json) ? implode(', ', $i->tallas_json) : '',
            ])->values()->all() ?? []),
            monedaId: {{ $monedaInicialId ?? 'null' }},
            monedaCopId: {{ $monedaCopId ?? 'null' }},
            tasaCambio: '{{ old('tasa_cambio', $factura->tasa_cambio) }}',
            flete: {{ (float) old('flete', $factura->flete ?? 0) }},
            seguro: {{ (float) old('seguro', $factura->seguro ?? 0) }},
            plantillaId: '{{ old('plantilla_factura_id', $factura->plantilla_factura_id ?? '') }}',
            clientesPlantilla: @js($clientes->pluck('plantilla_factura_id', 'id')->filter()->map(fn($id) => (string) $id)->all()),
            clientesMoneda: @js($clientes->pluck('moneda_preferida_id', 'id')->filter()->map(fn($id) => (string) $id)->all()),
            plantillasTipo: @js($plantillas->pluck('tipo', 'id')->map(fn($t) => (string) $t)->all()),
            importUrl: '{{ route('facturacion.facturas.importar') }}',
            csrf: '{{ csrf_token() }}',
            productos: @js($productos->map(fn($p) => [
                'id' => $p->id,
                'referencia' => $p->referencia,
                'descripcion' => $p->descripcion,
                'color' => $p->color,
                'composicion' => $p->composicion,
                'codigo_pa' => $p->codigo_pa,
                'precio_unitario' => (float) $p->precio_unitario,
                'es_prenda' => (bool) $p->es_prenda,
            ])->values()->all()),
        })"
    >
        {{-- Header --}}
        <x-manzer.page-header
            :title="$esEdit
                ? 'Factura ' . ($factura->numero_siigo ?? $factura->numero_interno)
                : 'Nueva factura'"
            :description="$esEdit
                ? 'Revisa o edita los datos de la factura.'
                : 'Completa los datos y agrega líneas para crear una factura.'"
        >
            <x-slot name="actions">
                @if ($esEdit)
                    <x-manzer.badge :variant="$estadoVariant" :text="ucfirst($factura->estado)" class="self-center" />
                @endif

                <x-manzer.button
                    variant="secondary"
                    icon="arrow-left"
                    href="{{ route('facturacion.facturas.index') }}"
                >
                    Volver
                </x-manzer.button>

                @if ($esEdit)
                    <a
                        href="{{ route('facturacion.facturas.previsualizar', $factura) }}"
                        target="_blank"
                        rel="noopener"
                        class="btn-secondary"
                    >
                        <i class="bi bi-eye"></i>
                        Previsualizar PDF
                    </a>
                    <a
                        href="{{ route('facturacion.facturas.pdf', $factura) }}"
                        target="_blank"
                        rel="noopener"
                        class="btn-secondary"
                    >
                        <i class="bi bi-download"></i>
                        Descargar PDF
                    </a>
                @endif
            </x-slot>
        </x-manzer.page-header>

        {{-- Avisos --}}
        @if ($esEdit && !$esEditable)
            <div class="mb-4">
                <x-manzer.alert type="warning" message="Esta factura ya fue emitida. No puedes modificarla." :dismissible="false" />
            </div>
        @endif

        {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

        <form action="{{ $action }}" method="POST" class="space-y-6">
            @csrf
            @if ($esEdit)
                @method('PUT')
            @endif

            <fieldset @disabled(!$esEditable) class="space-y-6">
                {{-- Card 1: Datos generales --}}
                <div class="card p-4 sm:p-6">
                    <div class="mb-4 flex items-center gap-2">
                        <i class="bi bi-receipt text-lg text-primary-600 dark:text-primary-400"></i>
                        <h2 class="text-base font-semibold tracking-tight">Datos generales</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="space-y-1.5 md:col-span-2">
                            <label for="cliente_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-person mr-1"></i>Cliente
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="cliente_id"
                                name="cliente_id"
                                required
                                @change="aplicarPlantillaDeCliente($event.target.value)"
                                class="input {{ $errors->has('cliente_id') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                                <option value="">Seleccionar cliente…</option>
                                @foreach ($clientes->pluck('nombre', 'id') as $id => $nombre)
                                    <option value="{{ $id }}" @selected((string) old('cliente_id', $factura->cliente_id) === (string) $id)>{{ $nombre }}</option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="plantilla_factura_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-file-earmark-ruled mr-1"></i>Plantilla
                            </label>
                            <select id="plantilla_factura_id" name="plantilla_factura_id" x-model="plantillaId" class="input">
                                <option value="">Usar plantilla default del sistema</option>
                                @foreach ($plantillas->pluck('nombre', 'id') as $id => $nombre)
                                    <option value="{{ $id }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Al seleccionar cliente se precarga su plantilla preferida.</p>
                            <template x-if="plantillaId && plantillasTipo[plantillaId]">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="plantillasTipo[plantillaId] === 'internacional'
                                        ? 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-400'
                                        : 'bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-400'">
                                    <i class="bi bi-globe"></i>
                                    <span x-text="plantillasTipo[plantillaId] === 'internacional' ? 'Factura internacional (exportación)' : 'Factura nacional'"></span>
                                </span>
                            </template>
                            @error('plantilla_factura_id')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="fecha" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-calendar-event mr-1"></i>Fecha
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="fecha"
                                name="fecha"
                                value="{{ old('fecha', $factura->fecha?->format('Y-m-d') ?? now()->toDateString()) }}"
                                required
                                class="input {{ $errors->has('fecha') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                            @error('fecha')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="vencimiento" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-calendar-check mr-1"></i>Vencimiento
                            </label>
                            <input
                                type="date"
                                id="vencimiento"
                                name="vencimiento"
                                value="{{ old('vencimiento', $factura->vencimiento?->format('Y-m-d')) }}"
                                class="input {{ $errors->has('vencimiento') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                            @error('vencimiento')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="moneda_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-currency-exchange mr-1"></i>Moneda
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="moneda_id"
                                name="moneda_id"
                                required
                                x-model.number="monedaId"
                                class="input {{ $errors->has('moneda_id') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                                @foreach ($monedas as $m)
                                    <option value="{{ $m->id }}" @selected((string) old('moneda_id', $monedaInicialId) === (string) $m->id)>{{ $m->codigo }}</option>
                                @endforeach
                            </select>
                            @error('moneda_id')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="tasa_cambio" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-arrow-left-right mr-1"></i>Tasa de cambio
                            </label>
                            <input
                                type="number"
                                id="tasa_cambio"
                                name="tasa_cambio"
                                step="0.0001"
                                min="0"
                                x-model="tasaCambio"
                                class="input {{ $errors->has('tasa_cambio') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Tasa COP por unidad de moneda (solo si moneda ≠ COP).</p>
                            @error('tasa_cambio')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Card: Datos de envío (opcionales, editables también después de crear) --}}
                <div class="card p-4 sm:p-6">
                    <div class="mb-4 flex items-center gap-2">
                        <i class="bi bi-truck text-lg text-primary-600 dark:text-primary-400"></i>
                        <h2 class="text-base font-semibold tracking-tight">Datos de envío</h2>
                        <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Opcional</span>
                    </div>
                    <p class="mb-4 text-xs text-zinc-500 dark:text-zinc-400">
                        Estos campos pueden completarse al crear la factura o después desde el listado
                        (botón <strong>Datos de envío</strong>). Útiles para facturas comerciales internacionales.
                    </p>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="space-y-1.5">
                            <label for="po_numero" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-hash mr-1"></i>PO# (Orden de compra)
                            </label>
                            <input
                                type="text"
                                id="po_numero"
                                name="po_numero"
                                value="{{ old('po_numero', $factura->po_numero) }}"
                                maxlength="60"
                                placeholder="Ej: PO-2026-04567"
                                class="input {{ $errors->has('po_numero') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Número de orden de compra del cliente.</p>
                            @error('po_numero')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="awb" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-airplane mr-1"></i>AWB (Guía aérea)
                            </label>
                            <input
                                type="text"
                                id="awb"
                                name="awb"
                                value="{{ old('awb', $factura->awb) }}"
                                maxlength="60"
                                placeholder="Ej: 123-45678901"
                                class="input {{ $errors->has('awb') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Air Waybill del transporte aéreo.</p>
                            @error('awb')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="shipper" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-box-seam mr-1"></i>Shipper (Transportista)
                            </label>
                            <input
                                type="text"
                                id="shipper"
                                name="shipper"
                                value="{{ old('shipper', $factura->shipper) }}"
                                maxlength="100"
                                placeholder="Ej: DHL Express"
                                class="input {{ $errors->has('shipper') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Empresa transportadora (DHL, FedEx, etc.).</p>
                            @error('shipper')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="remision" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-file-earmark-text mr-1"></i>Remisión
                            </label>
                            <input
                                type="text"
                                id="remision"
                                name="remision"
                                value="{{ old('remision', $factura->remision) }}"
                                maxlength="60"
                                placeholder="Ej: REM-2026-0006"
                                class="input {{ $errors->has('remision') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Número de remisión asociado a la factura.</p>
                            @error('remision')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="payment_terms" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                <i class="bi bi-cash-coin mr-1"></i>Términos de pago
                            </label>
                            <input
                                type="text"
                                id="payment_terms"
                                name="payment_terms"
                                value="{{ old('payment_terms', $factura->payment_terms) }}"
                                maxlength="100"
                                placeholder="Ej: Crédito ACH 30 días"
                                class="input {{ $errors->has('payment_terms') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                            >
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Condiciones de pago visibles en la factura.</p>
                            @error('payment_terms')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Card 2: Flags --}}
                <div class="card p-4 sm:p-6">
                    <div class="mb-4 flex items-center gap-2">
                        <i class="bi bi-flag text-lg text-primary-600 dark:text-primary-400"></i>
                        <h2 class="text-base font-semibold tracking-tight">Configuración</h2>
                    </div>

                    <label class="flex items-start gap-3 rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <input
                            type="checkbox"
                            id="es_electronica"
                            name="es_electronica"
                            value="1"
                            @checked(old('es_electronica', $factura->es_electronica))
                            class="mt-0.5 h-4 w-4 rounded border-zinc-300 text-primary-600 focus:ring-primary-500 dark:border-zinc-600 dark:bg-zinc-800"
                        >
                        <div class="flex-1">
                            <span class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                Generar factura electrónica ante la DIAN (vía Siigo)
                            </span>
                            <span class="mt-1 block text-xs text-zinc-500 dark:text-zinc-400">
                                Si está activa, al emitir la factura se enviará a Siigo. Nota: Siigo/DIAN siempre recibe en COP — usa la tasa de cambio de arriba.
                            </span>
                        </div>
                    </label>
                </div>

                {{-- Card 3: Líneas --}}
                <div class="card p-4 sm:p-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-list-ul text-lg text-primary-600 dark:text-primary-400"></i>
                            <h2 class="text-base font-semibold tracking-tight">Líneas de factura</h2>
                            <span
                                class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
                                x-text="items.length + ' ' + (items.length === 1 ? 'línea' : 'líneas')"
                            ></span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            {{-- Botón de errores de importación (solo si hubo) --}}
                            <button
                                type="button"
                                x-show="erroresImport.length > 0"
                                @click="modalErrores = true"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-100 dark:border-red-900 dark:bg-red-950/40 dark:text-red-400"
                                style="display: none;"
                            >
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span x-text="erroresImport.length + (erroresImport.length === 1 ? ' fila con error' : ' filas con error')"></span>
                            </button>

                            <a
                                href="{{ route('facturacion.facturas.importar.plantilla') }}"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"
                            >
                                <i class="bi bi-file-earmark-arrow-down"></i>
                                Plantilla Excel
                            </a>

                            <button
                                type="button"
                                @click="$refs.archivoExcel.click()"
                                :disabled="importando"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-400"
                            >
                                <i class="bi" :class="importando ? 'bi-arrow-repeat animate-spin' : 'bi-file-earmark-excel'"></i>
                                <span x-text="importando ? 'Importando…' : 'Importar Excel'"></span>
                            </button>
                            <input
                                type="file"
                                x-ref="archivoExcel"
                                accept=".xlsx,.xls,.csv"
                                class="hidden"
                                @change="importarExcel($event)"
                            >

                            <x-manzer.button
                                type="button"
                                variant="outline"
                                size="sm"
                                icon="plus-lg"
                                x-on:click="agregarLinea()"
                            >
                                Agregar línea
                            </x-manzer.button>
                        </div>
                    </div>

                    {{-- Buscador productos --}}
                    <div class="relative mb-4" x-data="{ abierto: false }" @click.outside="abierto = false">
                        <label for="productoBuscar" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            <i class="bi bi-search mr-1"></i>Agregar desde catálogo
                        </label>
                        <input
                            type="text"
                            id="productoBuscar"
                            placeholder="Buscar por referencia, descripción o color…"
                            x-model="productoBuscar"
                            @focus="abierto = true"
                            class="input"
                        >
                        <div
                            x-show="abierto && productosFiltrados.length > 0"
                            x-transition.opacity
                            class="absolute z-20 mt-1 max-h-72 w-full overflow-y-auto rounded-xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-800 dark:bg-zinc-900"
                            style="display: none;"
                        >
                            <template x-for="p in productosFiltrados" :key="p.id">
                                <button
                                    type="button"
                                    class="flex w-full items-start gap-3 border-b border-zinc-100 px-3 py-2 text-left hover:bg-primary-50 dark:border-zinc-800 dark:hover:bg-primary-950/40"
                                    @click="agregarDesdeProducto(p); abierto = false"
                                >
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-semibold text-primary-700 dark:text-primary-400" x-text="p.referencia"></span>
                                            <template x-if="p.es_prenda">
                                                <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[10px] font-medium text-sky-700 dark:bg-sky-950 dark:text-sky-400">Prenda</span>
                                            </template>
                                        </div>
                                        <div class="text-sm text-zinc-800 dark:text-zinc-200" x-text="p.descripcion"></div>
                                        <div class="text-xs text-zinc-500" x-text="p.color ? 'Color: ' + p.color : ''"></div>
                                    </div>
                                    <div class="text-right text-sm font-semibold text-zinc-700 dark:text-zinc-300" x-text="fmt(p.precio_unitario)"></div>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Tabla items --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="whitespace-nowrap px-2 py-2 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Ref.</th>
                                    <th class="whitespace-nowrap px-2 py-2 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Descripción</th>
                                    <th class="whitespace-nowrap px-2 py-2 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Color</th>
                                    <th class="whitespace-nowrap px-2 py-2 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Talla</th>
                                    <th class="whitespace-nowrap px-2 py-2 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Cant.</th>
                                    <th class="whitespace-nowrap px-2 py-2 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">P. Unit.</th>
                                    <th class="whitespace-nowrap px-2 py-2 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Descuento</th>
                                    <th class="whitespace-nowrap px-2 py-2 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">IVA %</th>
                                    <th class="whitespace-nowrap px-2 py-2 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total</th>
                                    <th class="whitespace-nowrap px-2 py-2 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <tr class="align-top">
                                        {{-- Hidden inputs: snapshot descriptivo que viaja al backend --}}
                                        <td class="px-2 py-2">
                                            <input type="hidden" :name="'items['+idx+'][referencia]'" :value="item.referencia ?? ''">
                                            <input type="hidden" :name="'items['+idx+'][composicion]'" :value="item.composicion ?? ''">
                                            <input type="hidden" :name="'items['+idx+'][codigo_pa]'" :value="item.codigo_pa ?? ''">
                                            {{-- No usamos x-model: con opciones generadas por x-for, Alpine no
                                                 sincroniza el valor inicial cuando el item se inyecta antes de que
                                                 las opciones existan (caso importar Excel). Usamos :selected por
                                                 opción + @change, que es fiable sin importar el orden de render. --}}
                                            <select
                                                :name="'items['+idx+'][producto_id]'"
                                                @change="item.producto_id = $event.target.value === '' ? '' : Number($event.target.value); aplicarProducto(item)"
                                                required
                                                class="input input-sm w-44"
                                            >
                                                <option value="" :selected="!item.producto_id">Seleccionar producto…</option>
                                                <template x-for="p in productos" :key="p.id">
                                                    <option
                                                        :value="p.id"
                                                        :selected="String(p.id) === String(item.producto_id)"
                                                        x-text="p.referencia + ' — ' + p.descripcion"
                                                    ></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="hidden" :name="'items['+idx+'][descripcion]'" :value="item.descripcion ?? ''">
                                            <span class="block min-w-[12rem] text-sm text-zinc-700 dark:text-zinc-300" x-text="item.descripcion || '—'"></span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="hidden" :name="'items['+idx+'][color]'" :value="item.color ?? ''">
                                            <span class="block w-24 text-sm text-zinc-600 dark:text-zinc-400" x-text="item.color || '—'"></span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <input
                                                type="text"
                                                :name="'items['+idx+'][tallas]'"
                                                x-model="item.tallas"
                                                maxlength="100"
                                                placeholder="S, M, L"
                                                title="Tallas separadas por coma. Se muestran en el campo size de la plantilla."
                                                class="input input-sm w-24"
                                            >
                                        </td>
                                        <td class="px-2 py-2">
                                            <input
                                                type="number"
                                                :name="'items['+idx+'][cantidad]'"
                                                x-model.number="item.cantidad"
                                                min="0"
                                                step="0.01"
                                                class="input input-sm ml-auto w-20 text-right"
                                            >
                                        </td>
                                        <td class="px-2 py-2">
                                            <input
                                                type="number"
                                                :name="'items['+idx+'][precio_unitario]'"
                                                x-model.number="item.precio_unitario"
                                                step="0.01"
                                                min="0"
                                                class="input input-sm ml-auto w-28 text-right"
                                            >
                                        </td>
                                        <td class="px-2 py-2">
                                            <div class="flex items-center justify-end gap-1">
                                                <input
                                                    type="number"
                                                    :name="'items['+idx+'][descuento]'"
                                                    x-model.number="item.descuento"
                                                    step="0.01"
                                                    min="0"
                                                    :max="item.descuento_tipo === 'porcentaje' ? 100 : null"
                                                    class="input input-sm w-20 text-right"
                                                >
                                                <select
                                                    :name="'items['+idx+'][descuento_tipo]'"
                                                    x-model="item.descuento_tipo"
                                                    class="input input-sm w-16 px-1 text-center"
                                                    title="Tipo de descuento"
                                                >
                                                    <option value="valor">$</option>
                                                    <option value="porcentaje">%</option>
                                                </select>
                                            </div>
                                            <p
                                                x-show="descuentoLinea(item) > 0"
                                                class="mt-0.5 text-right text-[10px] text-red-500"
                                                x-text="'-' + fmt(descuentoLinea(item))"
                                                style="display: none;"
                                            ></p>
                                        </td>
                                        <td class="px-2 py-2">
                                            <input
                                                type="number"
                                                :name="'items['+idx+'][impuesto_porcentaje]'"
                                                x-model.number="item.impuesto_porcentaje"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                class="input input-sm ml-auto w-20 text-right"
                                            >
                                        </td>
                                        <td class="px-2 py-2 text-right font-semibold text-zinc-800 dark:text-zinc-200">
                                            <span x-text="fmt(totalLinea(item))"></span>
                                        </td>
                                        <td class="px-2 py-2 text-right">
                                            <button
                                                type="button"
                                                @click="eliminar(idx)"
                                                class="rounded-lg p-2 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-950"
                                                aria-label="Eliminar línea"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Estado vacío --}}
                    <div
                        x-show="items.length === 0"
                        class="mt-4 rounded-xl border border-dashed border-zinc-300 bg-zinc-50/60 p-8 text-center dark:border-zinc-700 dark:bg-zinc-800/30"
                        style="display: none;"
                    >
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-950/40 dark:text-primary-400">
                            <i class="bi bi-basket text-xl"></i>
                        </div>
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">No has agregado líneas aún.</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Busca un producto del catálogo o crea una línea manual.</p>
                        <div class="mt-3 flex justify-center gap-2">
                            <x-manzer.button
                                type="button"
                                variant="outline"
                                size="sm"
                                icon="search"
                                x-on:click="document.getElementById('productoBuscar')?.focus()"
                            >
                                Buscar producto
                            </x-manzer.button>
                            <x-manzer.button
                                type="button"
                                variant="outline"
                                size="sm"
                                icon="plus-lg"
                                x-on:click="agregarLinea()"
                            >
                                Agregar línea
                            </x-manzer.button>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Totales --}}
                <div class="card p-4 sm:p-6">
                    <div class="mb-4 flex items-center gap-2">
                        <i class="bi bi-calculator text-lg text-primary-600 dark:text-primary-400"></i>
                        <h2 class="text-base font-semibold tracking-tight">Totales</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Inputs flete/seguro --}}
                        <div class="space-y-4">
                            <div class="space-y-1.5">
                                <label for="flete" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                    <i class="bi bi-truck mr-1"></i>Flete
                                </label>
                                <input
                                    type="number"
                                    id="flete"
                                    name="flete"
                                    step="0.01"
                                    min="0"
                                    x-model.number="flete"
                                    class="input {{ $errors->has('flete') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                                >
                                @error('flete')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="seguro" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                    <i class="bi bi-shield-check mr-1"></i>Seguro
                                </label>
                                <input
                                    type="number"
                                    id="seguro"
                                    name="seguro"
                                    step="0.01"
                                    min="0"
                                    x-model.number="seguro"
                                    class="input {{ $errors->has('seguro') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                                >
                                @error('seguro')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Resumen --}}
                        <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800/40">
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-zinc-600 dark:text-zinc-400">Subtotal</dt>
                                    <dd class="font-medium text-zinc-800 dark:text-zinc-200" x-text="fmt(subtotal)"></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-zinc-600 dark:text-zinc-400">Descuento</dt>
                                    <dd class="font-medium text-red-600 dark:text-red-400">
                                        <span x-text="'-' + fmt(descuentoTotal)"></span>
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-zinc-600 dark:text-zinc-400">IVA</dt>
                                    <dd class="font-medium text-zinc-800 dark:text-zinc-200" x-text="fmt(ivaTotal)"></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-zinc-600 dark:text-zinc-400">Flete</dt>
                                    <dd class="font-medium text-zinc-800 dark:text-zinc-200" x-text="fmt(flete)"></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-zinc-600 dark:text-zinc-400">Seguro</dt>
                                    <dd class="font-medium text-zinc-800 dark:text-zinc-200" x-text="fmt(seguro)"></dd>
                                </div>

                                <div class="my-2 border-t border-zinc-200 dark:border-zinc-700"></div>

                                <div class="flex items-baseline justify-between">
                                    <dt class="text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-200">TOTAL</dt>
                                    <dd class="text-xl font-bold text-primary-700 dark:text-primary-400" x-text="fmt(totalFactura)"></dd>
                                </div>

                                <div
                                    x-show="tasaCambio && monedaId !== monedaCopId"
                                    class="flex items-baseline justify-between pt-1"
                                    style="display: none;"
                                >
                                    <dt class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Total COP</dt>
                                    <dd class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                        <span x-text="'$ ' + fmt(totalCOP)"></span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Card 5: Observaciones --}}
                <div class="card p-4 sm:p-6">
                    <div class="mb-4 flex items-center gap-2">
                        <i class="bi bi-chat-left-text text-lg text-primary-600 dark:text-primary-400"></i>
                        <h2 class="text-base font-semibold tracking-tight">Observaciones</h2>
                    </div>

                    <div class="space-y-1.5">
                        <label for="observaciones" class="sr-only">Observaciones</label>
                        <textarea
                            id="observaciones"
                            name="observaciones"
                            rows="4"
                            placeholder="Notas visibles en la factura (condiciones, instrucciones, agradecimiento…)."
                            class="input {{ $errors->has('observaciones') ? 'ring-red-500 focus:ring-red-500' : '' }}"
                        >{{ old('observaciones', $factura->observaciones) }}</textarea>
                        @error('observaciones')
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </fieldset>

            {{-- Footer --}}
            <div class="flex flex-wrap items-center justify-end gap-2">
                <a
                    href="{{ route('facturacion.facturas.index') }}"
                    class="btn-ghost"
                >
                    Cancelar
                </a>

                @if ($esEditable)
                    <x-manzer.button type="submit" variant="primary" icon="check-lg">
                        Guardar
                    </x-manzer.button>
                @endif
            </div>
        </form>

        {{-- Form separado para "Emitir" (evita submits anidados) --}}
        @if ($esEdit && $esEditable)
            <form
                action="{{ route('facturacion.facturas.emitir', $factura) }}"
                method="POST"
                class="mt-3 flex justify-end"
                onsubmit="event.preventDefault(); const f=this; window.Swal.fire({title:'¿Emitir factura?',text:'Una vez emitida no podrás modificarla.',icon:'question',showCancelButton:true,confirmButtonText:'Sí, emitir',cancelButtonText:'Cancelar',confirmButtonColor:'#0ea5e9'}).then(r=>{if(r.isConfirmed)f.submit();});"
            >
                @csrf
                <x-manzer.button type="submit" variant="primary" icon="send-check">
                    Emitir factura
                </x-manzer.button>
            </form>
        @endif

        {{-- Modal: errores de importación Excel --}}
        <div
            x-show="modalErrores"
            x-transition.opacity
            @keydown.escape.window="modalErrores = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            style="display: none;"
        >
            <div @click.outside="modalErrores = false" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3 dark:border-zinc-800">
                    <h3 class="flex items-center gap-2 text-sm font-semibold text-red-700 dark:text-red-400">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Errores al importar
                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-700 dark:bg-red-950 dark:text-red-400" x-text="erroresImport.length"></span>
                    </h3>
                    <button type="button" @click="modalErrores = false" class="rounded-lg p-1 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="max-h-96 overflow-y-auto p-5">
                    <p class="mb-3 text-xs text-zinc-500 dark:text-zinc-400">
                        Estas filas no se importaron. Corrígelas en el Excel y vuelve a subirlo. El resto de líneas válidas sí se cargaron.
                    </p>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-zinc-400">
                                <th class="pb-2 pr-3">Fila</th>
                                <th class="pb-2 pr-3">Referencia</th>
                                <th class="pb-2">Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <template x-for="(e, i) in erroresImport" :key="i">
                                <tr>
                                    <td class="py-1.5 pr-3 font-mono text-xs text-zinc-600 dark:text-zinc-400" x-text="e.fila"></td>
                                    <td class="py-1.5 pr-3 font-mono text-xs text-zinc-700 dark:text-zinc-300" x-text="e.referencia"></td>
                                    <td class="py-1.5 text-xs text-red-600 dark:text-red-400" x-text="e.motivo"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end border-t border-zinc-200 px-5 py-3 dark:border-zinc-800">
                    <button type="button" @click="modalErrores = false" class="btn-secondary">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function facturaForm(inicial) {
            return {
                items: inicial.items && inicial.items.length ? inicial.items : [],
                monedaId: inicial.monedaId,
                monedaCopId: inicial.monedaCopId,
                tasaCambio: inicial.tasaCambio,
                flete: inicial.flete,
                seguro: inicial.seguro,
                plantillaId: inicial.plantillaId || '',
                clientesPlantilla: inicial.clientesPlantilla || {},
                clientesMoneda: inicial.clientesMoneda || {},
                plantillasTipo: inicial.plantillasTipo || {},
                productos: inicial.productos,
                productoBuscar: '',
                importUrl: inicial.importUrl,
                csrf: inicial.csrf,
                erroresImport: [],
                importando: false,
                modalErrores: false,

                /** La factura es internacional si la plantilla seleccionada lo es. */
                get esInternacional() {
                    return this.plantillasTipo[this.plantillaId] === 'internacional';
                },

                /** IVA por defecto: 0% en exportación (exenta), 19% nacional. */
                get ivaPorDefecto() {
                    return this.esInternacional ? 0 : 19;
                },

                productoPorId(id) {
                    return this.productos.find(p => String(p.id) === String(id)) || null;
                },

                /**
                 * Al elegir un producto en el select de una línea, copia su snapshot
                 * descriptivo y aplica el IVA por defecto según el tipo de factura.
                 * No toca cantidad/precio/descuento si ya tenían valor.
                 */
                aplicarProducto(item) {
                    const p = this.productoPorId(item.producto_id);
                    if (!p) {
                        item.referencia = '';
                        item.descripcion = '';
                        item.color = '';
                        item.composicion = '';
                        item.codigo_pa = '';
                        return;
                    }
                    item.referencia = p.referencia;
                    item.descripcion = p.descripcion;
                    item.color = p.color;
                    item.composicion = p.composicion;
                    item.codigo_pa = p.codigo_pa;
                    if (!item.precio_unitario) {
                        item.precio_unitario = p.precio_unitario;
                    }
                    item.impuesto_porcentaje = this.ivaPorDefecto;
                },

                /**
                 * Al cambiar el cliente, precarga su plantilla y moneda preferidas.
                 * El usuario puede cambiarlas después manualmente — este método no bloquea.
                 */
                aplicarPlantillaDeCliente(clienteId) {
                    if (!clienteId) return;
                    const plantillaPreferida = this.clientesPlantilla[clienteId];
                    if (plantillaPreferida) {
                        this.plantillaId = plantillaPreferida;
                    }
                    const monedaPreferida = this.clientesMoneda[clienteId];
                    if (monedaPreferida) {
                        this.monedaId = Number(monedaPreferida);
                    }
                },

                get productosFiltrados() {
                    if (!this.productoBuscar) {
                        return this.productos.slice(0, 20);
                    }
                    const q = this.productoBuscar.toLowerCase();
                    return this.productos
                        .filter(p => ((p.referencia || '') + ' ' + (p.descripcion || '') + ' ' + (p.color || '')).toLowerCase().includes(q))
                        .slice(0, 20);
                },

                agregarDesdeProducto(p) {
                    this.items.push({
                        producto_id: p.id,
                        referencia: p.referencia,
                        descripcion: p.descripcion,
                        color: p.color,
                        composicion: p.composicion,
                        codigo_pa: p.codigo_pa,
                        cantidad: 1,
                        precio_unitario: p.precio_unitario,
                        descuento: 0,
                        descuento_tipo: 'valor',
                        impuesto_porcentaje: this.ivaPorDefecto,
                        tallas: '',
                    });
                    this.productoBuscar = '';
                },

                agregarLinea() {
                    this.items.push({
                        producto_id: '',
                        referencia: '',
                        descripcion: '',
                        color: '',
                        composicion: '',
                        codigo_pa: '',
                        cantidad: 1,
                        precio_unitario: 0,
                        descuento: 0,
                        descuento_tipo: 'valor',
                        impuesto_porcentaje: this.ivaPorDefecto,
                        tallas: '',
                    });
                },

                /**
                 * Sube el Excel al backend, agrega las líneas válidas y guarda los
                 * errores por fila para mostrarlos en el modal.
                 */
                async importarExcel(event) {
                    const input = event.target;
                    const archivo = input.files && input.files[0];
                    if (!archivo) return;

                    this.importando = true;
                    this.erroresImport = [];

                    const formData = new FormData();
                    formData.append('archivo', archivo);
                    formData.append('iva_default', this.ivaPorDefecto);

                    try {
                        const resp = await fetch(this.importUrl, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                            body: formData,
                        });

                        if (!resp.ok) {
                            const data = await resp.json().catch(() => ({}));
                            const msg = data.message || 'No se pudo procesar el archivo. Verifica que sea un Excel válido.';
                            window.Swal.fire({ icon: 'error', title: 'Error al importar', text: msg });
                            return;
                        }

                        const data = await resp.json();
                        const nuevos = (data.items || []).map(it => ({
                            producto_id: it.producto_id,
                            referencia: it.referencia,
                            descripcion: it.descripcion,
                            color: it.color,
                            composicion: it.composicion,
                            codigo_pa: it.codigo_pa,
                            cantidad: Number(it.cantidad) || 0,
                            precio_unitario: Number(it.precio_unitario) || 0,
                            descuento: Number(it.descuento) || 0,
                            descuento_tipo: it.descuento_tipo === 'porcentaje' ? 'porcentaje' : 'valor',
                            impuesto_porcentaje: Number(it.impuesto_porcentaje) || 0,
                            tallas: it.tallas || '',
                        }));

                        this.items.push(...nuevos);
                        this.erroresImport = data.errores || [];

                        if (nuevos.length > 0) {
                            window.Swal.fire({
                                icon: this.erroresImport.length ? 'warning' : 'success',
                                title: nuevos.length + (nuevos.length === 1 ? ' línea importada' : ' líneas importadas'),
                                text: this.erroresImport.length
                                    ? this.erroresImport.length + ' fila(s) con error. Revísalas en el botón de alerta.'
                                    : 'Todas las filas se importaron correctamente.',
                                timer: this.erroresImport.length ? undefined : 2000,
                                showConfirmButton: this.erroresImport.length > 0,
                            });
                        } else if (this.erroresImport.length > 0) {
                            this.modalErrores = true;
                        } else {
                            window.Swal.fire({ icon: 'info', title: 'Sin líneas', text: 'El archivo no contenía filas para importar.' });
                        }
                    } catch (e) {
                        window.Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un problema al subir el archivo.' });
                    } finally {
                        this.importando = false;
                        input.value = '';
                    }
                },

                eliminar(idx) {
                    this.items.splice(idx, 1);
                },

                // Monto de descuento efectivo de la línea (espejo de FacturaItem::calcularDescuento en PHP).
                descuentoLinea(item) {
                    const base = (Number(item.cantidad) || 0) * (Number(item.precio_unitario) || 0);
                    const valor = Math.max(Number(item.descuento) || 0, 0);
                    const desc = item.descuento_tipo === 'porcentaje' ? base * valor / 100 : valor;
                    return Math.min(desc, Math.max(base, 0));
                },

                subtotalLinea(item) {
                    return (Number(item.cantidad) || 0) * (Number(item.precio_unitario) || 0) - this.descuentoLinea(item);
                },

                totalLinea(item) {
                    const sub = this.subtotalLinea(item);
                    return sub + (sub * (Number(item.impuesto_porcentaje) || 0) / 100);
                },

                get subtotal() {
                    return this.items.reduce((s, it) => s + (Number(it.cantidad) || 0) * (Number(it.precio_unitario) || 0), 0);
                },

                get descuentoTotal() {
                    return this.items.reduce((s, it) => s + this.descuentoLinea(it), 0);
                },

                get ivaTotal() {
                    return this.items.reduce((s, it) => s + this.subtotalLinea(it) * (Number(it.impuesto_porcentaje) || 0) / 100, 0);
                },

                get totalFactura() {
                    return this.subtotal - this.descuentoTotal + this.ivaTotal + Number(this.flete || 0) + Number(this.seguro || 0);
                },

                get totalCOP() {
                    if (!this.tasaCambio) return 0;
                    return this.totalFactura * Number(this.tasaCambio);
                },

                fmt(n) {
                    return Number(n || 0).toLocaleString('es-CO', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    });
                },
            };
        }
    </script>
@endpush
