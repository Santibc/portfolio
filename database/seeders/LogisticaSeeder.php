<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ZonaCobertura;
use App\Models\TarifaZona;
use App\Models\HorarioEntrega;
use App\Models\ReglaCapacidad;
use App\Models\Empresa;

class LogisticaSeeder extends Seeder
{
    public function run()
    {
        $empresa = Empresa::first();

        if (!$empresa) {
            $this->command->error('No hay empresa registrada. Ejecuta primero el seeder de empresas.');
            return;
        }

        $this->command->info('Creando datos de logistica para: ' . $empresa->nombre);

        // ============================================
        // ZONAS DE COBERTURA
        // ============================================

        // Zona 1: Bogota
        $zonaBogota = ZonaCobertura::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Bogota y alrededores',
            'descripcion' => 'Zona metropolitana de Bogota, incluye Chia, Soacha y municipios cercanos',
            'ciudades_ids' => [495], // Bogota ID
            'barrios' => ['Chapinero', 'Usaquen', 'Suba', 'Kennedy', 'Fontibon'],
            'activo' => true,
            'orden' => 1,
        ]);

        // Zona 2: Medellin
        $zonaMedellin = ZonaCobertura::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Medellin',
            'descripcion' => 'Area metropolitana del Valle de Aburra',
            'ciudades_ids' => [73], // Medellin ID
            'barrios' => ['El Poblado', 'Laureles', 'Envigado', 'Bello'],
            'activo' => true,
            'orden' => 2,
        ]);

        // Zona 3: Cali
        $zonaCali = ZonaCobertura::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Cali',
            'descripcion' => 'Cali y municipios del sur del Valle',
            'ciudades_ids' => [1064], // Cali ID
            'barrios' => ['Granada', 'Ciudad Jardin', 'San Fernando'],
            'activo' => true,
            'orden' => 3,
        ]);

        $this->command->info('- 3 zonas de cobertura creadas');

        // ============================================
        // TARIFAS POR ZONA
        // ============================================

        // Tarifas Bogota
        TarifaZona::create([
            'zona_cobertura_id' => $zonaBogota->id,
            'nombre' => 'Envio Estandar',
            'costo_base' => 8000,
            'minimo_compra' => 50000,
            'costo_si_no_alcanza_minimo' => 5000,
            'envio_gratis_desde' => true,
            'monto_envio_gratis' => 150000,
            'tiempo_entrega_horas' => 24,
            'activo' => true,
        ]);

        TarifaZona::create([
            'zona_cobertura_id' => $zonaBogota->id,
            'nombre' => 'Envio Express (Mismo dia)',
            'costo_base' => 15000,
            'minimo_compra' => 80000,
            'envio_gratis_desde' => false,
            'tiempo_entrega_horas' => 4,
            'activo' => true,
        ]);

        // Tarifas Medellin
        TarifaZona::create([
            'zona_cobertura_id' => $zonaMedellin->id,
            'nombre' => 'Envio Estandar',
            'costo_base' => 10000,
            'minimo_compra' => 60000,
            'envio_gratis_desde' => true,
            'monto_envio_gratis' => 180000,
            'tiempo_entrega_horas' => 24,
            'activo' => true,
        ]);

        // Tarifas Cali
        TarifaZona::create([
            'zona_cobertura_id' => $zonaCali->id,
            'nombre' => 'Envio Estandar',
            'costo_base' => 12000,
            'minimo_compra' => 70000,
            'envio_gratis_desde' => true,
            'monto_envio_gratis' => 200000,
            'tiempo_entrega_horas' => 48,
            'activo' => true,
        ]);

        $this->command->info('- 4 tarifas de envio creadas');

        // ============================================
        // HORARIOS DE ENTREGA
        // ============================================

        $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

        foreach ([$zonaBogota, $zonaMedellin, $zonaCali] as $zona) {
            foreach ($diasSemana as $dia) {
                // Horario Manana
                HorarioEntrega::create([
                    'zona_cobertura_id' => $zona->id,
                    'dia_semana' => $dia,
                    'nombre' => 'Manana',
                    'hora_inicio' => '08:00',
                    'hora_fin' => '12:00',
                    'capacidad_pedidos' => 15,
                    'activo' => true,
                ]);

                // Horario Tarde
                HorarioEntrega::create([
                    'zona_cobertura_id' => $zona->id,
                    'dia_semana' => $dia,
                    'nombre' => 'Tarde',
                    'hora_inicio' => '14:00',
                    'hora_fin' => '18:00',
                    'capacidad_pedidos' => 15,
                    'activo' => true,
                ]);
            }

            // Horario especial domingo (solo Bogota)
            if ($zona->id === $zonaBogota->id) {
                HorarioEntrega::create([
                    'zona_cobertura_id' => $zona->id,
                    'dia_semana' => 'domingo',
                    'nombre' => 'Manana Dominical',
                    'hora_inicio' => '09:00',
                    'hora_fin' => '13:00',
                    'capacidad_pedidos' => 10,
                    'activo' => true,
                ]);
            }
        }

        $this->command->info('- Horarios de entrega creados para todas las zonas');

        // ============================================
        // REGLAS DE CAPACIDAD (Fechas Especiales)
        // ============================================

        // San Valentin 2025
        ReglaCapacidad::create([
            'empresa_id' => $empresa->id,
            'fecha' => '2025-02-14',
            'capacidad_total_dia' => 100,
            'capacidad_por_hora' => 15,
            'notas' => 'San Valentin - Alta demanda de arreglos florales',
            'activo' => true,
        ]);

        // Dia de la Madre 2025 (segundo domingo de mayo = 11 de mayo)
        ReglaCapacidad::create([
            'empresa_id' => $empresa->id,
            'fecha' => '2025-05-11',
            'capacidad_total_dia' => 150,
            'capacidad_por_hora' => 20,
            'notas' => 'Dia de la Madre - Maxima capacidad del año',
            'activo' => true,
        ]);

        // Navidad 2025
        ReglaCapacidad::create([
            'empresa_id' => $empresa->id,
            'fecha' => '2025-12-24',
            'capacidad_total_dia' => 80,
            'capacidad_por_hora' => 10,
            'notas' => 'Nochebuena - Entregas hasta las 6pm',
            'activo' => true,
        ]);

        // Año Nuevo 2025
        ReglaCapacidad::create([
            'empresa_id' => $empresa->id,
            'fecha' => '2025-12-31',
            'capacidad_total_dia' => 60,
            'capacidad_por_hora' => 8,
            'notas' => 'Fin de Año - Jornada reducida',
            'activo' => true,
        ]);

        $this->command->info('- 4 reglas de capacidad para fechas especiales creadas');

        $this->command->info('');
        $this->command->info('Datos de logistica creados exitosamente!');
    }
}
