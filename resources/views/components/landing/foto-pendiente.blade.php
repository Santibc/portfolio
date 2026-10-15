@props([
    'texto' => 'Foto del local',
    'detalle' => 'Próximamente',
])

{{-- Espacio reservado para una foto real que el cliente aún no ha enviado --}}
<div role="img" aria-label="{{ $texto }}: {{ $detalle }}"
    {{ $attributes->merge(['class' => 'relative flex flex-col items-center justify-center gap-3 overflow-hidden border-2 border-dashed border-primary-300/80 bg-gradient-to-br from-cream-100 to-primary-50 p-6 text-center dark:border-primary-700/70 dark:from-cream-900 dark:to-primary-950/70']) }}>
    <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-white/80 text-primary-600 shadow-soft dark:bg-cream-950/70 dark:text-primary-300">
        <x-icon name="camera" class="h-6 w-6" />
    </span>
    <span class="font-serif text-xl text-cream-900 dark:text-cream-100">{{ $texto }}</span>
    <span class="text-[11px] uppercase tracking-[0.25em] text-cream-600 dark:text-cream-400">{{ $detalle }}</span>
</div>
