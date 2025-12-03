<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verificación KYC') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Proceso de Verificación KYC</h3>
                    <p>Esta página será implementada en el Módulo 7: Sistema KYC</p>
                    <p class="mt-2 text-sm text-gray-600">Estado actual: {{ Auth::user()->kyc_status }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
