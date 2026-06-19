@php($empleado = $empleado ?? null)

<x-card>
    <div class="space-y-6">
        {{-- Datos personales --}}
        <div>
            <h3 class="text-sm font-semibold text-cream-800 dark:text-cream-200 mb-3 flex items-center gap-2">
                <x-icon name="user" class="w-4 h-4 text-primary-600" /> Datos del empleado
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="Nombre completo" name="nombre" :value="old('nombre', $empleado?->nombre)" placeholder="Ej. Luz Yamile Chaparro" required />
                <x-input label="Documento (cédula)" name="documento" :value="old('documento', $empleado?->documento)" placeholder="Ej. 1010101010" required />
                <x-input label="Cargo" name="cargo" :value="old('cargo', $empleado?->cargo)" placeholder="Ej. Auxiliar de cocina" />
                <x-input label="Fecha de ingreso" name="fecha_ingreso" type="date" :value="old('fecha_ingreso', $empleado?->fecha_ingreso?->format('Y-m-d'))" required />
            </div>
        </div>

        {{-- Salario y deducciones --}}
        <div>
            <h3 class="text-sm font-semibold text-cream-800 dark:text-cream-200 mb-3 flex items-center gap-2">
                <x-icon name="dollar-sign" class="w-4 h-4 text-primary-600" /> Salario y deducciones
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input-currency label="Salario mensual (COP)" name="salario_base" :value="old('salario_base', $empleado?->salario_base)" hint="Sueldo mensual completo. El básico de la quincena es la mitad." required />
                <x-input-currency label="Auxilio de transporte (mensual)" name="auxilio_transporte" :value="old('auxilio_transporte', $empleado?->auxilio_transporte ?? config('nomina.auxilio_transporte'))" hint="Se prorratea por días en cada quincena." />
                <x-input-currency label="Bono por defecto" name="bono_default" :value="old('bono_default', $empleado?->bono_default ?? 0)" hint="Sugerencia de bono al liquidar. Editable por período." />
                <x-select label="Método de pago preferido" name="metodo_pago_id" :options="$metodosOptions" :value="old('metodo_pago_id', $empleado?->metodo_pago_id)" placeholder="— Sin preferencia —" />
                <x-input label="% Salud" name="porcentaje_salud" type="number" min="0" max="100" step="1" :value="old('porcentaje_salud', $empleado?->porcentaje_salud ?? config('nomina.porcentaje_salud'))" hint="Sobre el básico (legal: 4%)." required />
                <x-input label="% Pensión" name="porcentaje_pension" type="number" min="0" max="100" step="1" :value="old('porcentaje_pension', $empleado?->porcentaje_pension ?? config('nomina.porcentaje_pension'))" hint="Sobre el básico (legal: 4%)." required />
            </div>
            <div class="mt-4">
                <input type="hidden" name="tiene_auxilio" value="0" />
                <x-toggle name="tiene_auxilio" label="Recibe auxilio de transporte"
                          description="Aplica a quien devenga hasta 2 SMMLV."
                          :checked="old('tiene_auxilio', $empleado?->tiene_auxilio ?? true)" />
            </div>
        </div>

        {{-- Seguridad social y banco --}}
        <div>
            <h3 class="text-sm font-semibold text-cream-800 dark:text-cream-200 mb-3 flex items-center gap-2">
                <x-icon name="shield-check" class="w-4 h-4 text-primary-600" /> Seguridad social y pago
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="EPS (salud)" name="eps" :value="old('eps', $empleado?->eps)" placeholder="Ej. Sura" />
                <x-input label="Fondo de pensión" name="fondo_pension" :value="old('fondo_pension', $empleado?->fondo_pension)" placeholder="Ej. Porvenir" />
                <x-input label="Fondo de cesantías" name="fondo_cesantias" :value="old('fondo_cesantias', $empleado?->fondo_cesantias)" placeholder="Ej. Protección" />
                <x-input label="Banco" name="banco" :value="old('banco', $empleado?->banco)" placeholder="Ej. Bancolombia" />
                <x-input label="Número de cuenta" name="numero_cuenta" :value="old('numero_cuenta', $empleado?->numero_cuenta)" placeholder="Opcional" />
            </div>
        </div>

        <div>
            <input type="hidden" name="activo" value="0" />
            <x-toggle name="activo" label="Empleado activo"
                      description="Si está apagado, no entra en las próximas liquidaciones de nómina."
                      :checked="old('activo', $empleado?->activo ?? true)" />
        </div>
    </div>

    <x-slot:footer>
        <div class="flex items-center justify-end gap-2">
            <x-button variant="ghost" :href="route('empleados.index')">Cancelar</x-button>
            <x-button type="submit" variant="primary" icon="save">Guardar empleado</x-button>
        </div>
    </x-slot:footer>
</x-card>
