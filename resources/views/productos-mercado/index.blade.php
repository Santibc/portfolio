@extends('layouts.app')

@section('header', 'Productos Mercado')

@section('content')
    <x-page-header
        title="Productos Mercado"
        subtitle="Catálogo de productos del mercado por tipo"
        icon="shopping-basket"
    >
        <x-slot:actions>
            <x-button
                variant="ghost"
                icon="settings"
                :href="route('productos-mercado.tipos.index')"
            >
                Tipos
            </x-button>
            <x-button
                variant="primary"
                icon="plus"
                :href="route('productos-mercado.create')"
            >
                Nuevo producto
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table
        :columns="$columns"
        :rows="$rows"
        :searchable="true"
        :paginate="true"
        :perPage="5"
        :filters="[['key' => 'tipo', 'label' => 'Tipo'], ['key' => 'activo', 'label' => 'Estado']]"
        empty="Aún no hay productos. Crea el primero con el botón “Nuevo producto”."
    />
@endsection

@push('scripts')
<script>
    window.previewProductoImagen = function (url, nombre) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: nombre,
            imageUrl: url,
            imageAlt: nombre,
            showConfirmButton: false,
            showCloseButton: true,
            background: isDark ? '#1a1610' : '#fffdfa',
            color: isDark ? '#fbf5e9' : '#3e2723',
            customClass: {
                popup: 'rounded-2xl',
                image: 'rounded-xl max-h-[70vh] w-auto',
            },
        });
    };
</script>
@endpush
