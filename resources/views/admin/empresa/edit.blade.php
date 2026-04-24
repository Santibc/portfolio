@extends('layouts.app')

@section('title', 'Datos de empresa')

@section('content')
    <x-manzer.page-header
        title="Datos de empresa"
        description="Información corporativa, resoluciones DIAN, cuenta bancaria y contacto financiero."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.index') }}">
                Volver
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

    @if ($errors->any())
        <div class="mb-4">
            <x-manzer.alert type="error" dismissible>
                <strong class="block font-semibold">Revisa los siguientes campos:</strong>
                <ul class="mt-2 list-disc pl-5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-manzer.alert>
        </div>
    @endif

    <form
        action="{{ route('admin.empresa.update') }}"
        method="POST"
        enctype="multipart/form-data"
        x-data="{ tab: 'empresa' }"
        class="space-y-6"
    >
        @csrf
        @method('PUT')

        {{-- Tabs --}}
        <div class="card p-2">
            <nav class="flex flex-wrap gap-1" aria-label="Secciones">
                <button
                    type="button"
                    @click="tab = 'empresa'"
                    :class="tab === 'empresa' ? 'bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'"
                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition"
                >
                    <i class="bi bi-building"></i> Empresa
                </button>
                <button
                    type="button"
                    @click="tab = 'dian'"
                    :class="tab === 'dian' ? 'bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'"
                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition"
                >
                    <i class="bi bi-file-earmark-text"></i> DIAN
                </button>
                <button
                    type="button"
                    @click="tab = 'banco'"
                    :class="tab === 'banco' ? 'bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'"
                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition"
                >
                    <i class="bi bi-bank"></i> Banco
                </button>
                <button
                    type="button"
                    @click="tab = 'contacto'"
                    :class="tab === 'contacto' ? 'bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'"
                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition"
                >
                    <i class="bi bi-person-lines-fill"></i> Contacto financiero
                </button>
            </nav>
        </div>

        {{-- Sección Empresa --}}
        <div x-show="tab === 'empresa'" x-transition class="card space-y-5">
            <div class="flex items-center gap-3 border-b border-zinc-200 pb-3 dark:border-zinc-800">
                <i class="bi bi-building text-xl text-primary-600"></i>
                <div>
                    <h3 class="text-lg font-semibold">Información de la empresa</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Datos fiscales y de identidad corporativa.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-manzer.form-group
                    label="Razón social"
                    name="razon_social"
                    type="text"
                    :value="old('razon_social', $empresa['empresa.razon_social'] ?? '')"
                    :required="true"
                    icon="building"
                    placeholder="CLC & CIA S.A.S."
                />

                <x-manzer.form-group
                    label="NIT"
                    name="nit"
                    type="text"
                    :value="old('nit', $empresa['empresa.nit'] ?? '')"
                    :required="true"
                    icon="upc"
                    placeholder="900.123.456-7"
                />

                <div class="md:col-span-2">
                    <x-manzer.form-group
                        label="Dirección"
                        name="direccion"
                        type="text"
                        :value="old('direccion', $empresa['empresa.direccion'] ?? '')"
                        :required="true"
                        icon="geo-alt"
                        placeholder="Cra. 10 # 20-30, Bogotá"
                    />
                </div>

                <x-manzer.form-group
                    label="Teléfono"
                    name="telefono"
                    type="text"
                    :value="old('telefono', $empresa['empresa.telefono'] ?? '')"
                    :required="true"
                    icon="telephone"
                    placeholder="+57 300 000 0000"
                />

                <x-manzer.form-group
                    label="Email"
                    name="email"
                    type="email"
                    :value="old('email', $empresa['empresa.email'] ?? '')"
                    :required="true"
                    icon="envelope"
                    placeholder="facturacion@clc.com"
                />

                <x-manzer.form-group
                    label="Sitio web"
                    name="sitio_web"
                    type="text"
                    :value="old('sitio_web', $empresa['empresa.sitio_web'] ?? '')"
                    icon="globe"
                    placeholder="https://www.clc.com"
                />

                <x-manzer.form-group
                    label="Logo"
                    name="logo"
                    type="file"
                    icon="image"
                    accept="image/*"
                    help="Se sube a public/uploads/empresa/. Formato PNG/JPG."
                />

                <div class="md:col-span-2">
                    <x-manzer.form-group
                        label="Régimen tributario"
                        name="regimen_tributario"
                        type="textarea"
                        :value="old('regimen_tributario', $empresa['empresa.regimen_tributario'] ?? '')"
                        icon="file-text"
                        placeholder="Responsable de IVA, Gran contribuyente, etc."
                        :rows="3"
                    />
                </div>
            </div>

            @if (! empty($empresa['empresa.logo_path']))
                <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                    <p class="mb-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">Logo actual</p>
                    <img src="{{ asset($empresa['empresa.logo_path']) }}" alt="Logo empresa" class="h-20 w-auto object-contain">
                </div>
            @endif
        </div>

        {{-- Sección DIAN --}}
        <div x-show="tab === 'dian'" x-transition x-cloak class="card space-y-5">
            <div class="flex items-center gap-3 border-b border-zinc-200 pb-3 dark:border-zinc-800">
                <i class="bi bi-file-earmark-text text-xl text-primary-600"></i>
                <div>
                    <h3 class="text-lg font-semibold">Resoluciones DIAN</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Texto legal que se imprime al pie de las facturas.</p>
                </div>
            </div>

            <x-manzer.form-group
                label="Resolución CLC"
                name="dian_resolucion_clc"
                type="textarea"
                :value="old('dian_resolucion_clc', $dian['dian.resolucion_texto_clc'] ?? '')"
                icon="file-earmark-text"
                placeholder="Resolución DIAN Nº XXXX de YYYY..."
                :rows="5"
            />

            <x-manzer.form-group
                label="Resolución FV"
                name="dian_resolucion_fv"
                type="textarea"
                :value="old('dian_resolucion_fv', $dian['dian.resolucion_texto_fv'] ?? '')"
                icon="file-earmark-text"
                placeholder="Resolución DIAN Nº XXXX de YYYY..."
                :rows="5"
            />
        </div>

        {{-- Sección Banco --}}
        <div x-show="tab === 'banco'" x-transition x-cloak class="card space-y-5">
            <div class="flex items-center gap-3 border-b border-zinc-200 pb-3 dark:border-zinc-800">
                <i class="bi bi-bank text-xl text-primary-600"></i>
                <div>
                    <h3 class="text-lg font-semibold">Datos bancarios</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Cuenta para recibir pagos.</p>
                </div>
            </div>

            <x-manzer.alert type="info" dismissible="false" message="Estos datos aparecen en las facturas internacionales." />

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-manzer.form-group
                    label="Nombre del banco"
                    name="banco_nombre"
                    type="text"
                    :value="old('banco_nombre', $banco['banco.nombre'] ?? '')"
                    :required="true"
                    icon="bank"
                    placeholder="Bancolombia"
                />

                <x-manzer.form-group
                    label="País"
                    name="banco_pais"
                    type="text"
                    :value="old('banco_pais', $banco['banco.pais'] ?? '')"
                    :required="true"
                    icon="flag"
                    placeholder="Colombia"
                />

                <div class="md:col-span-2">
                    <x-manzer.form-group
                        label="Dirección del banco"
                        name="banco_direccion"
                        type="text"
                        :value="old('banco_direccion', $banco['banco.direccion'] ?? '')"
                        :required="true"
                        icon="geo-alt"
                        placeholder="Cra. 48 # 26-85, Medellín"
                    />
                </div>

                <x-manzer.form-group
                    label="Titular"
                    name="banco_titular"
                    type="text"
                    :value="old('banco_titular', $banco['banco.titular'] ?? '')"
                    :required="true"
                    icon="person"
                    placeholder="CLC & CIA S.A.S."
                />

                <x-manzer.form-group
                    label="Moneda de la cuenta"
                    name="banco_moneda"
                    type="text"
                    :value="old('banco_moneda', $banco['banco.moneda'] ?? '')"
                    :required="true"
                    icon="currency-exchange"
                    placeholder="USD"
                />

                <x-manzer.form-group
                    label="Código SWIFT"
                    name="banco_swift"
                    type="text"
                    :value="old('banco_swift', $banco['banco.swift'] ?? '')"
                    :required="true"
                    icon="hash"
                    placeholder="COLOCOBM"
                />

                <x-manzer.form-group
                    label="Número de cuenta"
                    name="banco_numero_cuenta"
                    type="text"
                    :value="old('banco_numero_cuenta', $banco['banco.numero_cuenta'] ?? '')"
                    :required="true"
                    icon="credit-card"
                    placeholder="0000-0000-0000-0000"
                />
            </div>
        </div>

        {{-- Sección Contacto financiero --}}
        <div x-show="tab === 'contacto'" x-transition x-cloak class="card space-y-5">
            <div class="flex items-center gap-3 border-b border-zinc-200 pb-3 dark:border-zinc-800">
                <i class="bi bi-person-lines-fill text-xl text-primary-600"></i>
                <div>
                    <h3 class="text-lg font-semibold">Contacto financiero</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Persona responsable de temas de cobro y facturación.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <x-manzer.form-group
                    label="Nombre"
                    name="contacto_nombre"
                    type="text"
                    :value="old('contacto_nombre', $contacto['contacto_financiero.nombre'] ?? '')"
                    :required="true"
                    icon="person"
                    placeholder="María Gómez"
                />

                <x-manzer.form-group
                    label="Email"
                    name="contacto_email"
                    type="email"
                    :value="old('contacto_email', $contacto['contacto_financiero.email'] ?? '')"
                    :required="true"
                    icon="envelope"
                    placeholder="maria@clc.com"
                />

                <x-manzer.form-group
                    label="Teléfono"
                    name="contacto_telefono"
                    type="text"
                    :value="old('contacto_telefono', $contacto['contacto_financiero.telefono'] ?? '')"
                    :required="true"
                    icon="telephone"
                    placeholder="+57 300 000 0000"
                />
            </div>
        </div>

        {{-- Footer acciones --}}
        <div class="card flex flex-wrap items-center justify-end gap-2">
            <x-manzer.button variant="ghost" href="{{ route('admin.index') }}">
                Volver
            </x-manzer.button>
            <x-manzer.button type="submit" variant="primary" icon="check-lg">
                Guardar cambios
            </x-manzer.button>
        </div>
    </form>
@endsection
