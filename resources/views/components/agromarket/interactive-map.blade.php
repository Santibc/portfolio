@props([
    'mapId' => 'projectMap'
])

{{-- Este componente crea un modal con mapa interactivo completo --}}
{{-- Se abre con: openProjectMap() --}}
{{-- Se cierra con: closeProjectMap() --}}

@push('scripts')
<script>
// Asegurar que el mapa se inicializa solo una vez
if (!window.projectMapInitialized) {
    document.addEventListener('DOMContentLoaded', function() {
        // El mapa se inicializará cuando se abra el modal
        console.log('Interactive map listo para inicializarse');
    });
    window.projectMapInitialized = true;
}
</script>
@endpush
