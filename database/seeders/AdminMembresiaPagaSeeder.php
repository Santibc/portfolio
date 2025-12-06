<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Membresia;
use App\Models\PlanMembresia;

class AdminMembresiaPagaSeeder extends Seeder
{
    /**
     * Asignar una membresía paga al usuario admin@admin.com para pruebas.
     *
     * Esto simula el proceso que ocurre cuando el webhook de Wompi
     * confirma un pago de membresía:
     * 1. Cancela membresías activas previas
     * 2. Crea una nueva membresía con estado 'activa'
     * 3. Actualiza el plan_membresia_id de la empresa
     */
    public function run()
    {
        // Buscar usuario admin
        $admin = User::where('email', 'admin@admin.com')->first();

        if (!$admin) {
            $this->command->error('Usuario admin@admin.com no encontrado');
            return;
        }

        // Obtener empresa del admin
        $empresa = $admin->empresa;

        if (!$empresa) {
            $this->command->error('El usuario admin@admin.com no tiene empresa asociada');
            return;
        }

        // Buscar un plan pago (sin marca de agua)
        // Prioridad: Emprendedor (ID 2) > cualquier plan pago sin marca de agua
        $planPago = PlanMembresia::where('id', 2)->first()
            ?? PlanMembresia::where('precio', '>', 0)
                            ->where('marca_de_agua', false)
                            ->first();

        if (!$planPago) {
            $this->command->error('No hay planes pagos disponibles');
            return;
        }

        \DB::beginTransaction();

        try {
            // 1. Cancelar membresías activas previas (como hace el webhook)
            $empresa->membresias()
                ->where('estado', 'activa')
                ->update(['estado' => 'cancelada']);

            // 2. Crear nueva membresía activa
            $membresia = Membresia::create([
                'empresa_id' => $empresa->id,
                'plan_membresia_id' => $planPago->id,
                'estado' => 'activa',
                'fecha_inicio' => now(),
                'fecha_fin' => now()->addYear(), // 1 año para pruebas
                'precio_pagado' => $planPago->precio,
                'transaccion_pago_id' => null, // Seeder, no hay transacción real
            ]);

            // 3. Actualizar empresa con el plan (como hace el webhook)
            $empresa->update(['plan_membresia_id' => $planPago->id]);

            \DB::commit();

            $this->command->info("Membresía asignada exitosamente:");
            $this->command->info("  - Usuario: {$admin->email}");
            $this->command->info("  - Empresa: {$empresa->nombre}");
            $this->command->info("  - Plan: {$planPago->nombre}");
            $this->command->info("  - Precio: $" . number_format($planPago->precio, 0, ',', '.'));
            $this->command->info("  - Válida hasta: {$membresia->fecha_fin->format('Y-m-d')}");
            $this->command->info("  - Marca de agua: " . ($planPago->marca_de_agua ? 'SÍ' : 'NO'));

        } catch (\Exception $e) {
            \DB::rollBack();
            $this->command->error('Error al crear membresía: ' . $e->getMessage());
        }
    }
}
