<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Ciudad;
use App\Models\ZonaCobertura;
use App\Models\TarifaZona;
use App\Models\HorarioEntrega;
use App\Models\ReglaCapacidad;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

class ChileSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Iniciando configuración de Chile...');

        // Crear país Chile si no existe
        $chile = Pais::firstOrCreate(
            ['nombre' => 'Chile'],
            []
        );

        $this->command->info('País Chile configurado (ID: ' . $chile->id . ')');

        // Regiones de Chile con sus comunas principales
        $regiones = [
            'Región de Arica y Parinacota' => ['Arica', 'Camarones', 'Putre', 'General Lagos'],
            'Región de Tarapacá' => ['Iquique', 'Alto Hospicio', 'Pozo Almonte', 'Camiña', 'Colchane', 'Huara', 'Pica'],
            'Región de Antofagasta' => ['Antofagasta', 'Mejillones', 'Sierra Gorda', 'Taltal', 'Calama', 'Ollagüe', 'San Pedro de Atacama', 'Tocopilla', 'María Elena'],
            'Región de Atacama' => ['Copiapó', 'Caldera', 'Tierra Amarilla', 'Chañaral', 'Diego de Almagro', 'Vallenar', 'Alto del Carmen', 'Freirina', 'Huasco'],
            'Región de Coquimbo' => ['La Serena', 'Coquimbo', 'Andacollo', 'La Higuera', 'Paiguano', 'Vicuña', 'Illapel', 'Canela', 'Los Vilos', 'Salamanca', 'Ovalle', 'Combarbalá', 'Monte Patria', 'Punitaqui', 'Río Hurtado'],
            'Región de Valparaíso' => ['Valparaíso', 'Viña del Mar', 'Concón', 'Quintero', 'Puchuncaví', 'Casablanca', 'Juan Fernández', 'Isla de Pascua', 'Los Andes', 'Calle Larga', 'Rinconada', 'San Esteban', 'La Ligua', 'Cabildo', 'Papudo', 'Petorca', 'Zapallar', 'Quillota', 'Calera', 'Hijuelas', 'La Cruz', 'Limache', 'Nogales', 'Olmué', 'San Antonio', 'Algarrobo', 'Cartagena', 'El Quisco', 'El Tabo', 'Santo Domingo', 'San Felipe', 'Catemu', 'Llay Llay', 'Panquehue', 'Putaendo', 'Santa María', 'Quilpué', 'Villa Alemana'],
            'Región Metropolitana de Santiago' => [
                'Santiago', 'Cerrillos', 'Cerro Navia', 'Conchalí', 'El Bosque', 'Estación Central',
                'Huechuraba', 'Independencia', 'La Cisterna', 'La Florida', 'La Granja', 'La Pintana',
                'La Reina', 'Las Condes', 'Lo Barnechea', 'Lo Espejo', 'Lo Prado', 'Macul', 'Maipú',
                'Ñuñoa', 'Pedro Aguirre Cerda', 'Peñalolén', 'Providencia', 'Pudahuel', 'Quilicura',
                'Quinta Normal', 'Recoleta', 'Renca', 'San Joaquín', 'San Miguel', 'San Ramón',
                'Vitacura', 'Puente Alto', 'Pirque', 'San José de Maipo', 'Colina', 'Lampa', 'Tiltil',
                'San Bernardo', 'Buin', 'Calera de Tango', 'Paine', 'Melipilla', 'Alhué', 'Curacaví',
                'María Pinto', 'San Pedro', 'Talagante', 'El Monte', 'Isla de Maipo', 'Padre Hurtado', 'Peñaflor'
            ],
            'Región del Libertador General Bernardo O\'Higgins' => ['Rancagua', 'Codegua', 'Coinco', 'Coltauco', 'Doñihue', 'Graneros', 'Las Cabras', 'Machalí', 'Malloa', 'Mostazal', 'Olivar', 'Peumo', 'Pichidegua', 'Quinta de Tilcoco', 'Rengo', 'Requínoa', 'San Vicente', 'Pichilemu', 'La Estrella', 'Litueche', 'Marchihue', 'Navidad', 'Paredones', 'San Fernando', 'Chépica', 'Chimbarongo', 'Lolol', 'Nancagua', 'Palmilla', 'Peralillo', 'Placilla', 'Pumanque', 'Santa Cruz'],
            'Región del Maule' => ['Talca', 'Constitución', 'Curepto', 'Empedrado', 'Maule', 'Pelarco', 'Pencahue', 'Río Claro', 'San Clemente', 'San Rafael', 'Cauquenes', 'Chanco', 'Pelluhue', 'Curicó', 'Hualañé', 'Licantén', 'Molina', 'Rauco', 'Romeral', 'Sagrada Familia', 'Teno', 'Vichuquén', 'Linares', 'Colbún', 'Longaví', 'Parral', 'Retiro', 'San Javier', 'Villa Alegre', 'Yerbas Buenas'],
            'Región de Ñuble' => ['Chillán', 'Bulnes', 'Cobquecura', 'Coelemu', 'Coihueco', 'Chillán Viejo', 'El Carmen', 'Ninhue', 'Ñiquén', 'Pemuco', 'Pinto', 'Portezuelo', 'Quillón', 'Quirihue', 'Ránquil', 'San Carlos', 'San Fabián', 'San Ignacio', 'San Nicolás', 'Treguaco', 'Yungay'],
            'Región del Biobío' => ['Concepción', 'Coronel', 'Chiguayante', 'Florida', 'Hualqui', 'Lota', 'Penco', 'San Pedro de la Paz', 'Santa Juana', 'Talcahuano', 'Tomé', 'Hualpén', 'Lebu', 'Arauco', 'Cañete', 'Contulmo', 'Curanilahue', 'Los Álamos', 'Tirúa', 'Los Ángeles', 'Antuco', 'Cabrero', 'Laja', 'Mulchén', 'Nacimiento', 'Negrete', 'Quilaco', 'Quilleco', 'San Rosendo', 'Santa Bárbara', 'Tucapel', 'Yumbel', 'Alto Biobío'],
            'Región de La Araucanía' => ['Temuco', 'Carahue', 'Cunco', 'Curarrehue', 'Freire', 'Galvarino', 'Gorbea', 'Lautaro', 'Loncoche', 'Melipeuco', 'Nueva Imperial', 'Padre Las Casas', 'Perquenco', 'Pitrufquén', 'Pucón', 'Saavedra', 'Teodoro Schmidt', 'Toltén', 'Vilcún', 'Villarrica', 'Cholchol', 'Angol', 'Collipulli', 'Curacautín', 'Ercilla', 'Lonquimay', 'Los Sauces', 'Lumaco', 'Purén', 'Renaico', 'Traiguén', 'Victoria'],
            'Región de Los Ríos' => ['Valdivia', 'Corral', 'Lanco', 'Los Lagos', 'Máfil', 'Mariquina', 'Paillaco', 'Panguipulli', 'La Unión', 'Futrono', 'Lago Ranco', 'Río Bueno'],
            'Región de Los Lagos' => ['Puerto Montt', 'Calbuco', 'Cochamó', 'Fresia', 'Frutillar', 'Los Muermos', 'Llanquihue', 'Maullín', 'Puerto Varas', 'Castro', 'Ancud', 'Chonchi', 'Curaco de Vélez', 'Dalcahue', 'Puqueldón', 'Queilén', 'Quellón', 'Quemchi', 'Quinchao', 'Osorno', 'Puerto Octay', 'Purranque', 'Puyehue', 'Río Negro', 'San Juan de la Costa', 'San Pablo', 'Chaitén', 'Futaleufú', 'Hualaihué', 'Palena'],
            'Región de Aysén del General Carlos Ibáñez del Campo' => ['Coyhaique', 'Lago Verde', 'Aysén', 'Cisnes', 'Guaitecas', 'Cochrane', 'O\'Higgins', 'Tortel', 'Chile Chico', 'Río Ibáñez'],
            'Región de Magallanes y de la Antártica Chilena' => ['Punta Arenas', 'Laguna Blanca', 'Río Verde', 'San Gregorio', 'Cabo de Hornos', 'Antártica', 'Porvenir', 'Primavera', 'Timaukel', 'Natales', 'Torres del Paine']
        ];

        $this->command->info('Creando regiones y comunas de Chile...');

        // Limpiar datos anteriores de Colombia si existen
        $this->command->info('Limpiando datos anteriores...');

        // Guardar IDs de ciudades que están en uso
        $ciudadesEnUso = DB::table('compras')->whereNotNull('ciudad_id')->pluck('ciudad_id')->unique()->toArray();

        foreach ($regiones as $nombreRegion => $comunas) {
            // Crear o encontrar la región (departamento)
            $region = Departamento::firstOrCreate(
                ['pais_id' => $chile->id, 'nombre' => $nombreRegion],
                []
            );

            // Crear comunas (ciudades)
            foreach ($comunas as $nombreComuna) {
                Ciudad::firstOrCreate(
                    ['departamento_id' => $region->id, 'nombre' => $nombreComuna],
                    []
                );
            }
        }

        $this->command->info('Regiones y comunas de Chile creadas exitosamente');

        // Obtener IDs de comunas para zonas de cobertura
        $this->crearZonasCobertura($chile);

        $this->command->info('');
        $this->command->info('Configuración de Chile completada exitosamente!');
    }

    private function crearZonasCobertura($chile)
    {
        $empresa = Empresa::first();

        if (!$empresa) {
            $this->command->warn('No hay empresa registrada. Las zonas de cobertura no se crearon.');
            return;
        }

        $this->command->info('Creando zonas de cobertura para Chile...');

        // Limpiar zonas anteriores
        $zonasAnteriores = ZonaCobertura::where('empresa_id', $empresa->id)->pluck('id');
        TarifaZona::whereIn('zona_cobertura_id', $zonasAnteriores)->delete();
        HorarioEntrega::whereIn('zona_cobertura_id', $zonasAnteriores)->delete();
        ZonaCobertura::where('empresa_id', $empresa->id)->delete();

        // Obtener la región metropolitana
        $regionMetropolitana = Departamento::where('nombre', 'Región Metropolitana de Santiago')
            ->where('pais_id', $chile->id)
            ->first();

        if (!$regionMetropolitana) {
            $this->command->error('No se encontró la Región Metropolitana');
            return;
        }

        // Obtener IDs de comunas de Santiago
        $comunasSantiago = Ciudad::where('departamento_id', $regionMetropolitana->id)->pluck('id')->toArray();

        // Zona 1: Santiago Centro y comunas cercanas
        $comunasCentro = Ciudad::where('departamento_id', $regionMetropolitana->id)
            ->whereIn('nombre', [
                'Santiago', 'Providencia', 'Las Condes', 'Vitacura', 'Lo Barnechea',
                'Ñuñoa', 'La Reina', 'Peñalolén', 'Macul', 'San Joaquín', 'La Florida',
                'La Granja', 'San Miguel', 'Pedro Aguirre Cerda', 'La Cisterna'
            ])
            ->pluck('id')->toArray();

        $zonaSantiagoCentro = ZonaCobertura::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Santiago Centro y Oriente',
            'descripcion' => 'Comunas centrales y del sector oriente de Santiago',
            'ciudades_ids' => $comunasCentro,
            'barrios' => ['Centro Histórico', 'Bellavista', 'Lastarria', 'Italia', 'Sanhattan'],
            'activo' => true,
            'orden' => 1,
        ]);

        // Zona 2: Santiago Sur y Poniente
        $comunasSurPoniente = Ciudad::where('departamento_id', $regionMetropolitana->id)
            ->whereIn('nombre', [
                'Maipú', 'Pudahuel', 'Cerro Navia', 'Lo Prado', 'Quinta Normal',
                'Estación Central', 'Cerrillos', 'El Bosque', 'La Pintana', 'San Ramón',
                'Lo Espejo', 'San Bernardo', 'Puente Alto', 'Buin', 'Paine'
            ])
            ->pluck('id')->toArray();

        $zonaSurPoniente = ZonaCobertura::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Santiago Sur y Poniente',
            'descripcion' => 'Comunas del sur y poniente de Santiago',
            'ciudades_ids' => $comunasSurPoniente,
            'activo' => true,
            'orden' => 2,
        ]);

        // Zona 3: Santiago Norte
        $comunasNorte = Ciudad::where('departamento_id', $regionMetropolitana->id)
            ->whereIn('nombre', [
                'Recoleta', 'Independencia', 'Conchalí', 'Huechuraba', 'Renca',
                'Quilicura', 'Colina', 'Lampa', 'Tiltil'
            ])
            ->pluck('id')->toArray();

        $zonaNorte = ZonaCobertura::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Santiago Norte',
            'descripcion' => 'Comunas del norte de Santiago',
            'ciudades_ids' => $comunasNorte,
            'activo' => true,
            'orden' => 3,
        ]);

        // Zona 4: Resto de la Región Metropolitana
        $comunasResto = Ciudad::where('departamento_id', $regionMetropolitana->id)
            ->whereNotIn('id', array_merge($comunasCentro, $comunasSurPoniente, $comunasNorte))
            ->pluck('id')->toArray();

        $zonaResto = ZonaCobertura::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Otras comunas RM',
            'descripcion' => 'Resto de comunas de la Región Metropolitana',
            'ciudades_ids' => $comunasResto,
            'activo' => true,
            'orden' => 4,
        ]);

        // Zona 5: Valparaíso y Viña del Mar
        $regionValparaiso = Departamento::where('nombre', 'Región de Valparaíso')
            ->where('pais_id', $chile->id)
            ->first();

        if ($regionValparaiso) {
            $comunasValparaiso = Ciudad::where('departamento_id', $regionValparaiso->id)
                ->whereIn('nombre', ['Valparaíso', 'Viña del Mar', 'Concón', 'Quilpué', 'Villa Alemana'])
                ->pluck('id')->toArray();

            $zonaValparaiso = ZonaCobertura::create([
                'empresa_id' => $empresa->id,
                'nombre' => 'Valparaíso y Viña del Mar',
                'descripcion' => 'Gran Valparaíso',
                'ciudades_ids' => $comunasValparaiso,
                'activo' => true,
                'orden' => 5,
            ]);
        }

        $this->command->info('- 5 zonas de cobertura creadas');

        // ============================================
        // TARIFAS POR ZONA (en pesos chilenos)
        // ============================================

        // Tarifas Santiago Centro
        TarifaZona::create([
            'zona_cobertura_id' => $zonaSantiagoCentro->id,
            'nombre' => 'Envío Estándar',
            'costo_base' => 3990,
            'minimo_compra' => 15000,
            'costo_si_no_alcanza_minimo' => 2000,
            'envio_gratis_desde' => true,
            'monto_envio_gratis' => 50000,
            'tiempo_entrega_horas' => 24,
            'activo' => true,
        ]);

        TarifaZona::create([
            'zona_cobertura_id' => $zonaSantiagoCentro->id,
            'nombre' => 'Envío Express (Mismo día)',
            'costo_base' => 6990,
            'minimo_compra' => 25000,
            'envio_gratis_desde' => false,
            'tiempo_entrega_horas' => 4,
            'activo' => true,
        ]);

        // Tarifas Santiago Sur y Poniente
        TarifaZona::create([
            'zona_cobertura_id' => $zonaSurPoniente->id,
            'nombre' => 'Envío Estándar',
            'costo_base' => 4990,
            'minimo_compra' => 15000,
            'envio_gratis_desde' => true,
            'monto_envio_gratis' => 60000,
            'tiempo_entrega_horas' => 24,
            'activo' => true,
        ]);

        // Tarifas Santiago Norte
        TarifaZona::create([
            'zona_cobertura_id' => $zonaNorte->id,
            'nombre' => 'Envío Estándar',
            'costo_base' => 4990,
            'minimo_compra' => 15000,
            'envio_gratis_desde' => true,
            'monto_envio_gratis' => 60000,
            'tiempo_entrega_horas' => 24,
            'activo' => true,
        ]);

        // Tarifas Otras comunas RM
        TarifaZona::create([
            'zona_cobertura_id' => $zonaResto->id,
            'nombre' => 'Envío Estándar',
            'costo_base' => 5990,
            'minimo_compra' => 20000,
            'envio_gratis_desde' => true,
            'monto_envio_gratis' => 80000,
            'tiempo_entrega_horas' => 48,
            'activo' => true,
        ]);

        // Tarifas Valparaíso (si existe)
        if (isset($zonaValparaiso)) {
            TarifaZona::create([
                'zona_cobertura_id' => $zonaValparaiso->id,
                'nombre' => 'Envío Estándar',
                'costo_base' => 7990,
                'minimo_compra' => 25000,
                'envio_gratis_desde' => true,
                'monto_envio_gratis' => 100000,
                'tiempo_entrega_horas' => 48,
                'activo' => true,
            ]);
        }

        $this->command->info('- Tarifas de envío creadas (en CLP)');

        // ============================================
        // HORARIOS DE ENTREGA
        // ============================================

        $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $zonas = [$zonaSantiagoCentro, $zonaSurPoniente, $zonaNorte, $zonaResto];
        if (isset($zonaValparaiso)) {
            $zonas[] = $zonaValparaiso;
        }

        foreach ($zonas as $zona) {
            foreach ($diasSemana as $dia) {
                HorarioEntrega::create([
                    'zona_cobertura_id' => $zona->id,
                    'dia_semana' => $dia,
                    'nombre' => 'Mañana',
                    'hora_inicio' => '09:00',
                    'hora_fin' => '13:00',
                    'capacidad_pedidos' => 15,
                    'activo' => true,
                ]);

                HorarioEntrega::create([
                    'zona_cobertura_id' => $zona->id,
                    'dia_semana' => $dia,
                    'nombre' => 'Tarde',
                    'hora_inicio' => '14:00',
                    'hora_fin' => '19:00',
                    'capacidad_pedidos' => 15,
                    'activo' => true,
                ]);
            }
        }

        $this->command->info('- Horarios de entrega creados');

        // ============================================
        // REGLAS DE CAPACIDAD (Fechas Especiales Chile)
        // ============================================

        // Limpiar reglas anteriores
        ReglaCapacidad::where('empresa_id', $empresa->id)->delete();

        // San Valentín
        ReglaCapacidad::create([
            'empresa_id' => $empresa->id,
            'fecha' => '2026-02-14',
            'capacidad_total_dia' => 100,
            'capacidad_por_hora' => 15,
            'notas' => 'San Valentín - Alta demanda de arreglos florales',
            'activo' => true,
        ]);

        // Día de la Madre en Chile (segundo domingo de mayo)
        ReglaCapacidad::create([
            'empresa_id' => $empresa->id,
            'fecha' => '2026-05-10',
            'capacidad_total_dia' => 150,
            'capacidad_por_hora' => 20,
            'notas' => 'Día de la Madre - Máxima capacidad del año',
            'activo' => true,
        ]);

        // Fiestas Patrias
        ReglaCapacidad::create([
            'empresa_id' => $empresa->id,
            'fecha' => '2026-09-18',
            'capacidad_total_dia' => 80,
            'capacidad_por_hora' => 12,
            'notas' => 'Fiestas Patrias - 18 de Septiembre',
            'activo' => true,
        ]);

        // Navidad
        ReglaCapacidad::create([
            'empresa_id' => $empresa->id,
            'fecha' => '2026-12-24',
            'capacidad_total_dia' => 80,
            'capacidad_por_hora' => 10,
            'notas' => 'Nochebuena - Entregas hasta las 18:00',
            'activo' => true,
        ]);

        $this->command->info('- Reglas de capacidad para fechas especiales de Chile creadas');
    }
}
