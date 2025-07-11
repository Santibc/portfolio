<x-app-layout>
    <x-slot name="header">

            {{ __('Inicio') }}

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 ">
                    @if (Auth::user()->hasRole('admin'))
                        {{ __("Rol admin") }}
                    @else
                        <p>Closer.</p>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
