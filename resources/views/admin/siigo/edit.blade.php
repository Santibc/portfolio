@extends('layouts.app')

@section('title', 'Integración Siigo')

@section('content')
    <x-manzer.page-header
        title="Integración Siigo"
        description="Configura las credenciales de la API oficial de Siigo y sincroniza catálogos.">
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.index') }}">
                Volver
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">
                        <i class="bi bi-cloud-arrow-up-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Credenciales API</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Se obtienen en <a class="text-blue-600 underline" href="https://developers.siigo.com/" target="_blank" rel="noopener">developers.siigo.com</a>.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.siigo.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 md:grid-cols-2">
                        <x-manzer.form-group
                            label="Usuario (email)"
                            icon="envelope"
                            name="username"
                            type="email"
                            :value="old('username', $config->username)"
                            placeholder="usuario@empresa.com"
                            required />

                        <x-manzer.form-group
                            label="Ambiente"
                            icon="toggles"
                            name="ambiente"
                            type="select"
                            :value="old('ambiente', $config->ambiente)"
                            :options="['sandbox' => 'Sandbox (pruebas)', 'produccion' => 'Producción']"
                            required />
                    </div>

                    <x-manzer.form-group
                        label="Access Key"
                        icon="key"
                        name="access_key"
                        type="password"
                        :value="old('access_key')"
                        :placeholder="$config->access_key ? '•••••••••• (ya configurado, deja vacío para mantener)' : 'Pega el access key de Siigo'"
                        help="Se guarda cifrado con Crypt::encryptString. Si dejas vacío, se conserva el actual." />

                    <x-manzer.form-group
                        label="Partner ID (opcional)"
                        icon="person-badge"
                        name="partner_id"
                        type="text"
                        :value="old('partner_id', $config->partner_id)"
                        placeholder="Solo si Siigo te lo asignó como partner"
                        help="Header Partner-Id enviado en cada request." />

                    {{-- Datos de facturación electrónica DIAN --}}
                    <div class="border-t border-zinc-100 pt-5 dark:border-zinc-800">
                        <div class="mb-3 flex items-center gap-2">
                            <i class="bi bi-file-earmark-ruled text-amber-600"></i>
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Datos de facturación electrónica</h4>
                        </div>
                        <p class="mb-4 text-xs text-zinc-500 dark:text-zinc-400">
                            Valores obligatorios al emitir una factura electrónica ante la DIAN.
                            Los IDs los obtienes sincronizando catálogos (botón "Sincronizar catálogos") o consultándolos en Siigo Nube.
                        </p>

                        <div class="grid gap-4 md:grid-cols-2">
                            <x-manzer.form-group
                                label="NIT del emisor"
                                icon="building"
                                name="nit_emisor"
                                type="text"
                                :value="old('nit_emisor', $config->nit_emisor)"
                                placeholder="901249576-9"
                                help="NIT de la empresa (con o sin dígito de verificación)." />

                            <x-manzer.form-group
                                label="Tipo documento ID (nacional)"
                                icon="hash"
                                name="tipo_documento_id"
                                type="number"
                                :value="old('tipo_documento_id', $config->tipo_documento_id)"
                                placeholder="Ej: 24446"
                                help="ID del tipo 'Factura de venta electrónica' en Siigo." />

                            <x-manzer.form-group
                                label="Tipo documento exportación ID"
                                icon="globe"
                                name="tipo_documento_export_id"
                                type="number"
                                :value="old('tipo_documento_export_id', $config->tipo_documento_export_id)"
                                placeholder="Ej: 24447"
                                help="ID del tipo 'Factura electrónica de venta – exportación' (catálogo document-types)." />

                            <x-manzer.form-group
                                label="Tax ID (IVA nacional)"
                                icon="percent"
                                name="tax_id"
                                type="number"
                                :value="old('tax_id', $config->tax_id)"
                                placeholder="Ej: 13156"
                                help="ID del impuesto IVA en Siigo (catálogo taxes). Se aplica a ítems con impuesto en facturas nacionales. La exportación va exenta." />

                            <x-manzer.form-group
                                label="Vendedor (Seller) ID"
                                icon="person"
                                name="seller_id"
                                type="number"
                                :value="old('seller_id', $config->seller_id)"
                                placeholder="Ej: 123"
                                help="ID del usuario vendedor por defecto." />

                            <x-manzer.form-group
                                label="Método de pago ID"
                                icon="credit-card"
                                name="payment_type_id"
                                type="number"
                                :value="old('payment_type_id', $config->payment_type_id)"
                                placeholder="Ej: 456"
                                help="ID del método de pago por defecto." />
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" id="activo" name="activo" value="1" @checked(old('activo', $config->activo))
                            class="h-4 w-4 rounded border-zinc-300 text-primary-600 focus:ring-primary-500 dark:border-zinc-600 dark:bg-zinc-800">
                        <label for="activo" class="text-sm text-zinc-700 dark:text-zinc-300">Integración activa — se enviará factura electrónica cuando se marque en cada factura.</label>
                    </div>

                    <div class="flex items-center justify-between border-t border-zinc-100 pt-4 dark:border-zinc-800">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            @if ($config->sync_catalogos_at)
                                Última sincronización: {{ $config->sync_catalogos_at->diffForHumans() }}
                            @else
                                Aún sin sincronizar catálogos.
                            @endif
                        </p>
                        <x-manzer.button type="submit" variant="primary" icon="check-lg">
                            Guardar configuración
                        </x-manzer.button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="mb-4 text-base font-semibold text-zinc-900 dark:text-zinc-100">Acciones</h3>
                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        id="btn-probar"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="bi bi-plug-fill"></i>
                        <span>Probar conexión</span>
                    </button>

                    <form method="POST" action="{{ route('admin.siigo.sincronizar') }}" class="inline" id="form-sync">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Sincronizar catálogos</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <x-manzer.stat-card icon="file-earmark-text" :value="$catalogos['document-types']" label="Tipos de documento" variant="primary" />
            <x-manzer.stat-card icon="percent" :value="$catalogos['taxes']" label="Impuestos (Siigo)" variant="success" />
            <x-manzer.stat-card icon="cash-coin" :value="$catalogos['payment-types']" label="Tipos de pago (Siigo)" variant="info" />

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-900/20 dark:text-amber-200">
                <div class="flex items-start gap-2">
                    <i class="bi bi-info-circle mt-0.5"></i>
                    <div>
                        <strong class="block">¿Cómo funciona?</strong>
                        <p class="mt-1 leading-relaxed">
                            Cada factura tiene un toggle "Generar factura electrónica". Si está activo y la integración está activa, la factura se envía a Siigo al emitirla y Siigo devuelve CUFE + número DIAN oficial.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('btn-probar')?.addEventListener('click', async function () {
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Probando...';

                try {
                    const res = await fetch('{{ route('admin.siigo.probar') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();

                    await window.Swal.fire({
                        icon: data.ok ? 'success' : 'error',
                        title: data.ok ? 'Conexión exitosa' : 'Error de conexión',
                        text: data.mensaje,
                    });
                } catch (e) {
                    await window.Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo contactar al servidor.' });
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-plug-fill"></i> <span>Probar conexión</span>';
                }
            });
        </script>
    @endpush
@endsection
