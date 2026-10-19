{{-- Sticker tipo estrella del empaque con la calificación real de Google --}}
<a href="{{ $enlaces['google'] }}" target="_blank" rel="noopener"
   class="group grid h-[8.5rem] w-[8.5rem] shrink-0 place-items-center text-primary-950 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] hover:rotate-[8deg] hover:scale-105 {{ ($clases ?? '') ?: 'relative' }}">
    <svg class="absolute inset-0 h-full w-full -rotate-6 drop-shadow-[0_10px_14px_rgb(var(--sombra-honda)/0.35)]" viewBox="0 0 150 150" aria-hidden="true" focusable="false">
        <polygon class="fill-accent-300 stroke-flame-500" stroke-width="5" stroke-linejoin="round"
                 points="75,5 89,38 123,20 112,57 146,75 112,93 123,130 89,112 75,145 61,112 27,130 38,93 4,75 38,57 27,20 61,38" />
    </svg>
    <span class="relative flex -rotate-6 flex-col items-center leading-none">
        <span class="flex items-center gap-1 font-brand text-[2.1rem]">
            {{ $negocio['calificacion_formateada'] }}
            <x-icon name="star" class="h-5 w-5 fill-current" />
        </span>
        <span class="rotulo mt-1 text-[0.7rem]">en Google</span>
        <span class="mt-0.5 text-[0.68rem] font-semibold">{{ $negocio['google_opiniones'] }} opiniones</span>
        <span class="sr-only">(de 5 estrellas; abre Google Maps)</span>
    </span>
</a>
