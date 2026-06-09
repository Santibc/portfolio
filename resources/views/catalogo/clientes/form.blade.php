@extends('layouts.app')

@section('title', $cliente->exists ? 'Editar cliente' : 'Nuevo cliente')

@section('content')
    <div class="container-fluid py-4">
        <x-manzer.page-header
            :title="$cliente->exists ? 'Editar ' . $cliente->nombre : 'Nuevo cliente'"
            description="Define los datos fiscales y comerciales del cliente."
        >
            <x-slot name="actions">
                <x-manzer.button
                    variant="secondary"
                    icon="arrow-left"
                    href="{{ route('catalogos.clientes.index') }}"
                >
                    Volver
                </x-manzer.button>
            </x-slot>
        </x-manzer.page-header>

        {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

        <form
            action="{{ $cliente->exists ? route('catalogos.clientes.update', $cliente) : route('catalogos.clientes.store') }}"
            method="POST"
            x-data="{ tipo: '{{ old('tipo', $cliente->tipo ?? 'nacional') }}' }"
            class="space-y-6"
        >
            @csrf
            @if ($cliente->exists)
                @method('PUT')
            @endif

            {{-- Sección 1 — Clasificación --}}
            <div class="card p-6">
                <div class="mb-4 flex items-center gap-2">
                    <i class="bi bi-globe text-primary-600 dark:text-primary-400"></i>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Clasificación</h2>
                </div>

                <input type="hidden" name="tipo" :value="tipo">

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Tipo de cliente <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                @click="tipo = 'nacional'"
                                :class="tipo === 'nacional'
                                    ? 'border-primary-500 bg-primary-50 text-primary-700 dark:border-primary-500 dark:bg-primary-950 dark:text-primary-300'
                                    : 'border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800'"
                                class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 text-sm font-medium transition"
                            >
                                <i class="bi bi-building"></i>
                                Nacional
                            </button>
                            <button
                                type="button"
                                @click="tipo = 'internacional'"
                                :class="tipo === 'internacional'
                                    ? 'border-primary-500 bg-primary-50 text-primary-700 dark:border-primary-500 dark:bg-primary-950 dark:text-primary-300'
                                    : 'border-zinc-200 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800'"
                                class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 text-sm font-medium transition"
                            >
                                <i class="bi bi-globe"></i>
                                Internacional
                            </button>
                        </div>
                        @error('tipo')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 2 — Identidad fiscal --}}
            <div class="card p-6">
                <div class="mb-4 flex items-center gap-2">
                    <i class="bi bi-card-text text-primary-600 dark:text-primary-400"></i>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Identidad fiscal</h2>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <x-manzer.form-group
                        label="Tipo de identificación"
                        name="tipo_identificacion"
                        type="select"
                        :options="[
                            '' => '—',
                            'NIT' => 'NIT',
                            'CC' => 'Cédula',
                            'CE' => 'C. Extranjería',
                            'PAS' => 'Pasaporte',
                            'VAT' => 'VAT internacional',
                        ]"
                        :value="$cliente->tipo_identificacion ?? ''"
                    />

                    <x-manzer.form-group
                        label="Identificación"
                        name="identificacion"
                        type="text"
                        placeholder="Número / documento"
                        :value="$cliente->identificacion ?? ''"
                    />

                    <x-manzer.form-group
                        label="Nombre comercial"
                        name="nombre_comercial"
                        type="text"
                        placeholder="Nombre comercial (opcional)"
                        :value="$cliente->nombre_comercial ?? ''"
                    />
                </div>

                <div class="mt-4">
                    <x-manzer.form-group
                        label="Nombre / Razón social"
                        name="nombre"
                        type="text"
                        required
                        placeholder="Razón social completa"
                        :value="$cliente->nombre ?? ''"
                    />
                </div>
            </div>

            {{-- Sección 3 — Contacto --}}
            <div class="card p-6">
                <div class="mb-4 flex items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-primary-600 dark:text-primary-400"></i>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Contacto</h2>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-manzer.form-group
                        label="Email"
                        name="email"
                        type="email"
                        icon="envelope"
                        placeholder="contacto@cliente.com"
                        :value="$cliente->email ?? ''"
                    />

                    <x-manzer.form-group
                        label="Teléfono"
                        name="telefono"
                        type="text"
                        icon="telephone"
                        placeholder="+57 300 000 0000"
                        :value="$cliente->telefono ?? ''"
                    />
                </div>

                <div class="mt-4">
                    <x-manzer.form-group
                        label="Dirección de facturación"
                        name="direccion_facturacion"
                        type="text"
                        placeholder="Dirección fiscal"
                        :value="$cliente->direccion_facturacion ?? ''"
                    />
                </div>

                <div class="mt-4">
                    <x-manzer.form-group
                        label="Dirección de envío"
                        name="direccion_envio"
                        type="text"
                        placeholder="Dirección de entrega"
                        help="Si difiere de la de facturación."
                        :value="$cliente->direccion_envio ?? ''"
                    />
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-manzer.form-group
                        label="País"
                        name="pais"
                        type="text"
                        placeholder="Colombia"
                        :value="$cliente->pais ?? 'Colombia'"
                    />

                    <x-manzer.form-group
                        label="Ciudad"
                        name="ciudad"
                        type="text"
                        placeholder="Ciudad"
                        :value="$cliente->ciudad ?? ''"
                    />
                </div>
            </div>

            {{-- Sección 4 — Internacional (solo si tipo === 'internacional') --}}
            <div
                x-show="tipo === 'internacional'"
                x-transition
                class="rounded-2xl border-2 border-sky-200 bg-sky-50/50 p-6 dark:border-sky-900 dark:bg-sky-950/30"
                style="display: none;"
            >
                <div class="mb-4 flex items-center gap-2">
                    <i class="bi bi-globe text-sky-600 dark:text-sky-400"></i>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Datos para comercio internacional</h2>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <x-manzer.form-group
                        label="Moneda preferida"
                        name="moneda_preferida_id"
                        type="select"
                        :options="['' => 'Moneda por defecto'] + $monedas->pluck('codigo', 'id')->toArray()"
                        :value="$cliente->moneda_preferida_id ?? ''"
                    />

                    <x-manzer.form-group
                        label="Incoterm"
                        name="incoterm_id"
                        type="select"
                        :options="['' => '—'] + $incoterms->pluck('codigo', 'id')->toArray()"
                        :value="$cliente->incoterm_id ?? ''"
                    />

                    <x-manzer.form-group
                        label="Puerto de embarque"
                        name="puerto_id"
                        type="select"
                        :options="['' => '—'] + $puertos->pluck('nombre', 'id')->toArray()"
                        :value="$cliente->puerto_id ?? ''"
                    />
                </div>

                <div class="mt-4">
                    <x-manzer.form-group
                        label="Tipo de pago"
                        name="tipo_pago_id"
                        type="select"
                        :options="['' => '—'] + $tiposPago->pluck('nombre', 'id')->toArray()"
                        :value="$cliente->tipo_pago_id ?? ''"
                    />
                </div>

                <div class="mt-4">
                    <x-manzer.form-group
                        label="Datos bancarios destino"
                        name="datos_bancarios_destino"
                        type="textarea"
                        :rows="4"
                        placeholder="Banco, IBAN, SWIFT, beneficiario…"
                        help="Aparecerá en la factura del cliente internacional como bloque bancario."
                        :value="$cliente->datos_bancarios_destino ?? ''"
                    />
                </div>
            </div>

            {{-- Sección — Preferencias de facturación (común para nacional e internacional) --}}
            <div class="card p-6">
                <div class="mb-4 flex items-center gap-2">
                    <i class="bi bi-file-earmark-ruled text-primary-600 dark:text-primary-400"></i>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Preferencias de facturación</h2>
                </div>

                <x-manzer.form-group
                    label="Plantilla de factura preferida"
                    name="plantilla_factura_id"
                    type="select"
                    :options="['' => 'Usar plantilla por defecto del sistema'] + $plantillas->pluck('nombre', 'id')->toArray()"
                    :value="old('plantilla_factura_id', $cliente->plantilla_factura_id ?? '')"
                    help="Al crear una factura para este cliente se preselecciona automáticamente, pero se puede cambiar en ese momento."
                />
            </div>

            {{-- Sección 5 — Notas --}}
            <div class="card p-6">
                <div class="mb-4 flex items-center gap-2">
                    <i class="bi bi-journal-text text-primary-600 dark:text-primary-400"></i>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Notas y estado</h2>
                </div>

                <x-manzer.form-group
                    label="Observaciones"
                    name="observaciones"
                    type="textarea"
                    :rows="3"
                    placeholder="Notas internas (opcional)"
                    :value="$cliente->observaciones ?? ''"
                />

                <div class="mt-4">
                    <input type="hidden" name="activo" value="0">
                    <label class="inline-flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="activo"
                            name="activo"
                            value="1"
                            @checked(old('activo', $cliente->exists ? $cliente->activo : true))
                            class="h-4 w-4 rounded border-zinc-300 text-primary-600 focus:ring-primary-500 dark:border-zinc-600 dark:bg-zinc-800"
                        >
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">Cliente activo</span>
                    </label>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <a
                    href="{{ route('catalogos.clientes.index') }}"
                    class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100"
                >
                    Cancelar
                </a>
                <x-manzer.button type="submit" variant="primary" icon="check-lg">
                    Guardar cliente
                </x-manzer.button>
            </div>
        </form>
    </div>
@endsection
