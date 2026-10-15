@extends('layouts.app')

@section('title', 'Página web · Negocio')
@section('header', 'Página web')

@section('content')
    @include('pagina-web._encabezado', ['subtitulo' => 'Datos del negocio, contacto y horario que ven tus clientes'])

    <form method="POST" action="{{ route('pagina-web.negocio.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid items-start gap-6 lg:grid-cols-2">
            <x-card>
                <x-slot:header>
                    <h2 class="font-semibold text-cream-900 dark:text-cream-50">Identidad</h2>
                </x-slot:header>
                <div class="space-y-4">
                    <x-input label="Nombre corto" name="nombre" :value="old('nombre', $ajuste->nombre)" required maxlength="80"
                             hint="Aparece en el título de la pestaña y en los mensajes." />
                    <x-input label="Nombre completo" name="nombre_largo" :value="old('nombre_largo', $ajuste->nombre_largo)" required maxlength="120"
                             hint="Se usa en el pie de página y para Google." />
                    <x-input label="Lema" name="lema" :value="old('lema', $ajuste->lema)" required maxlength="60" />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-input label="Propietaria" name="propietaria" :value="old('propietaria', $ajuste->propietaria)" maxlength="80"
                                 hint="Firma de «Nuestra historia». Vacío = no se muestra." />
                        <x-input label="Cargo" name="propietaria_cargo" :value="old('propietaria_cargo', $ajuste->propietaria_cargo)" maxlength="40"
                                 placeholder="Propietaria, Fundadora…" />
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot:header>
                    <h2 class="font-semibold text-cream-900 dark:text-cream-50">Contacto</h2>
                </x-slot:header>
                <div class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-input label="Teléfono" name="telefono" icon="phone" inputmode="tel" :value="old('telefono', $ajuste->telefono)" required
                                 hint="Ej: 3188655792" />
                        <x-input label="WhatsApp" name="whatsapp" icon="message-circle" inputmode="tel" :value="old('whatsapp', $ajuste->whatsapp)" required
                                 hint="Con el 57 adelante. Ej: 573188655792" />
                    </div>
                    <x-textarea label="Mensaje inicial de WhatsApp" name="whatsapp_mensaje" rows="2" required maxlength="200"
                                :value="old('whatsapp_mensaje', $ajuste->whatsapp_mensaje)"
                                hint="Lo que aparece ya escrito cuando el cliente toca el botón de WhatsApp." />
                </div>
            </x-card>

            <x-card>
                <x-slot:header>
                    <h2 class="font-semibold text-cream-900 dark:text-cream-50">Ubicación</h2>
                </x-slot:header>
                <div class="space-y-4">
                    <x-input label="Dirección" name="direccion" icon="map-pin" :value="old('direccion', $ajuste->direccion)" required maxlength="120"
                             hint="El mapa ubica el local con la dirección y la ciudad." />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-input label="Barrio" name="barrio" :value="old('barrio', $ajuste->barrio)" maxlength="60" />
                        <x-input label="Ciudad" name="ciudad" :value="old('ciudad', $ajuste->ciudad)" required maxlength="60" />
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot:header>
                    <h2 class="font-semibold text-cream-900 dark:text-cream-50">Redes sociales</h2>
                    <p class="text-xs text-cream-600 dark:text-cream-400 mt-0.5">Pega el enlace completo. Las vacías no se muestran.</p>
                </x-slot:header>
                <div class="space-y-4">
                    <x-input label="Instagram" name="instagram_url" type="url" :value="old('instagram_url', $ajuste->instagram_url)" placeholder="https://instagram.com/…" />
                    <x-input label="Facebook" name="facebook_url" type="url" :value="old('facebook_url', $ajuste->facebook_url)" placeholder="https://facebook.com/…" />
                    <x-input label="TikTok" name="tiktok_url" type="url" :value="old('tiktok_url', $ajuste->tiktok_url)" placeholder="https://tiktok.com/@…" />
                </div>
            </x-card>
        </div>

        {{-- Horario: filas que se agregan/quitan en el navegador y se guardan juntas --}}
        <x-card>
            <x-slot:header>
                <h2 class="font-semibold text-cream-900 dark:text-cream-50">Horario</h2>
                <p class="text-xs text-cream-600 dark:text-cream-400 mt-0.5">Se muestra en "Visítanos" y en el pie de página.</p>
            </x-slot:header>

            <div x-data="{ filas: @js(array_values(old('horarios', $horarios))) }" class="space-y-3">
                @if ($errors->has('horarios.*'))
                    <x-alert variant="danger">Revisa el horario: cada franja necesita días y horas.</x-alert>
                @endif

                <template x-for="(fila, i) in filas" :key="i">
                    <div class="grid grid-cols-1 gap-3 rounded-xl border border-cream-200 p-3 sm:grid-cols-[1fr_1fr_auto] sm:items-end sm:border-0 sm:p-0 dark:border-cream-800">
                        <x-input label="Días" x-bind:name="`horarios[${i}][dias]`" x-model="fila.dias" placeholder="Lunes a sábado" required maxlength="60" />
                        <x-input label="Horas" x-bind:name="`horarios[${i}][horas]`" x-model="fila.horas" placeholder="6:00 a.m. – 8:00 p.m." required maxlength="60" />
                        <x-button variant="ghost" icon="trash-2" x-on:click="filas.splice(i, 1)" aria-label="Quitar franja">
                            <span class="sm:hidden">Quitar</span>
                        </x-button>
                    </div>
                </template>

                <p x-show="filas.length === 0" class="text-sm text-cream-600 dark:text-cream-400">
                    Sin franjas: la página no mostrará el horario.
                </p>

                <x-button variant="secondary" size="sm" icon="plus" x-show="filas.length < 7" x-on:click="filas.push({ dias: '', horas: '' })">
                    Agregar franja
                </x-button>
            </div>
        </x-card>

        <div class="flex justify-end">
            <x-button type="submit" icon="save">Guardar cambios</x-button>
        </div>
    </form>
@endsection
