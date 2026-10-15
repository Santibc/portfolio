@props([
    'label' => 'Foto',
    'name' => 'imagen',
    'id' => null,
    'actual' => null,          // URL de la foto guardada (o null)
    'proporcion' => 'aspect-[4/3]',
    'hint' => 'JPG, PNG, WEBP o AVIF, hasta 8 MB. Se optimiza sola para la web.',
    'quitable' => false,       // muestra "Quitar la foto actual" (envía quitar_imagen=1)
    'error' => null,
])

@php
    $id = $id ?? $name.'-'.substr(md5(uniqid('', true)), 0, 8);
    $err = $error ?? $errors->first($name);
@endphp

{{-- Selector de foto con vista previa inmediata (antes de guardar) --}}
<div class="w-full" x-data="{
        vista: @js($actual),
        quitar: false,
        nueva: false,
        elegir(evento) {
            const archivo = evento.target.files[0];
            if (!archivo) return;
            this.vista = URL.createObjectURL(archivo);
            this.nueva = true;
            this.quitar = false;
        },
    }">
    @if ($label)
        <span class="block text-sm font-medium text-cream-800 dark:text-cream-200 mb-1.5">{{ $label }}</span>
    @endif

    <label for="{{ $id }}"
           class="group relative block {{ $proporcion }} cursor-pointer overflow-hidden rounded-xl border-2 border-dashed transition-colors
                  {{ $err ? 'border-red-400' : 'border-cream-300 hover:border-primary-400 dark:border-cream-700 dark:hover:border-primary-500' }}
                  bg-cream-50 dark:bg-cream-900/40">
        <img x-show="vista && !quitar" x-bind:src="vista" alt="" class="absolute inset-0 h-full w-full object-cover" x-cloak>

        <span x-show="!vista || quitar" class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-4 text-center text-cream-600 dark:text-cream-400">
            <x-icon name="image-plus" class="w-8 h-8 text-primary-500 dark:text-primary-300" />
            <span class="text-sm font-medium">Toca para elegir una foto</span>
        </span>

        <span x-show="vista && !quitar" x-cloak
              class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1.5 bg-gradient-to-t from-black/70 to-transparent px-3 pb-2.5 pt-8 text-xs font-semibold text-white opacity-0 transition-opacity group-hover:opacity-100">
            <x-icon name="upload" class="w-3.5 h-3.5" /> Cambiar foto
        </span>

        <span x-show="nueva" x-cloak class="absolute left-2 top-2 rounded-full bg-primary-500 px-2.5 py-1 text-[11px] font-semibold text-white shadow-soft">
            Sin guardar
        </span>
    </label>

    <input type="file" name="{{ $name }}" id="{{ $id }}" accept="image/*" class="sr-only" x-on:change="elegir($event)" {{ $attributes }}>

    @if ($quitable && $actual)
        <div class="mt-2.5" x-show="!nueva">
            <x-checkbox name="quitar_imagen" :id="$id.'-quitar'" label="Quitar la foto actual" x-model="quitar" />
        </div>
    @endif

    @if ($err)
        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
            <x-icon name="alert-circle" class="w-3.5 h-3.5" /> {{ $err }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-cream-600 dark:text-cream-400">{{ $hint }}</p>
    @endif
</div>
