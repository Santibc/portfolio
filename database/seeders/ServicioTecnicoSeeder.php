<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\STTecnico;
use App\Models\STEquipo;
use App\Models\STOrdenServicio;
use App\Models\STRepuesto;
use App\Models\STDiagnostico;
use Illuminate\Support\Facades\DB;

class ServicioTecnicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            // 1. Crear Técnicos
            $tecnicos = [
                [
                    'codigo' => 'TEC-001',
                    'nombre_completo' => 'Carlos Rodríguez',
                    'documento' => '1234567890',
                    'email' => 'carlos.rodriguez@example.com',
                    'telefono' => '3001234567',
                    'celular' => '3001234567',
                    'especialidad' => 'CCTV',
                    'certificaciones' => 'Certificado en instalación de cámaras IP, Redes',
                    'activo' => true,
                    'fecha_ingreso' => '2020-01-15'
                ],
                [
                    'codigo' => 'TEC-002',
                    'nombre_completo' => 'Ana María López',
                    'documento' => '9876543210',
                    'email' => 'ana.lopez@example.com',
                    'telefono' => '3109876543',
                    'celular' => '3109876543',
                    'especialidad' => 'DVR/NVR',
                    'certificaciones' => 'Certificado en sistemas de grabación Hikvision, Dahua',
                    'activo' => true,
                    'fecha_ingreso' => '2019-06-20'
                ],
                [
                    'codigo' => 'TEC-003',
                    'nombre_completo' => 'Pedro Martínez',
                    'documento' => '5556667778',
                    'email' => 'pedro.martinez@example.com',
                    'telefono' => '3205556677',
                    'celular' => '3205556677',
                    'especialidad' => 'Control Acceso',
                    'certificaciones' => 'Certificado en sistemas biométricos',
                    'activo' => true,
                    'fecha_ingreso' => '2021-03-10'
                ]
            ];

            foreach ($tecnicos as $tecnico) {
                STTecnico::create($tecnico);
            }

            // 2. Crear Clientes
            $clientes = [
                [
                    'tipo_documento' => 'NIT',
                    'numero_documento' => '900123456-1',
                    'nombre_completo' => 'Juan Pérez',
                    'razon_social' => 'Comercial El Éxito SAS',
                    'email' => 'ventas@elexito.com',
                    'telefono' => '6011234567',
                    'celular' => '3001111111',
                    'direccion' => 'Calle 100 # 20-30',
                    'ciudad' => 'Bogotá',
                    'departamento' => 'Cundinamarca',
                    'tipo_cliente' => 'empresa',
                    'activo' => true
                ],
                [
                    'tipo_documento' => 'CC',
                    'numero_documento' => '1020304050',
                    'nombre_completo' => 'María González',
                    'razon_social' => null,
                    'email' => 'maria.gonzalez@gmail.com',
                    'telefono' => '6017654321',
                    'celular' => '3152222222',
                    'direccion' => 'Carrera 50 # 80-10 Apto 301',
                    'ciudad' => 'Medellín',
                    'departamento' => 'Antioquia',
                    'tipo_cliente' => 'particular',
                    'activo' => true
                ],
                [
                    'tipo_documento' => 'NIT',
                    'numero_documento' => '800987654-3',
                    'nombre_completo' => 'Luis Ramírez',
                    'razon_social' => 'Supermercado La Economía LTDA',
                    'email' => 'admin@laeconomia.com',
                    'telefono' => '6025555555',
                    'celular' => '3013333333',
                    'direccion' => 'Avenida 6ta # 15-45',
                    'ciudad' => 'Cali',
                    'departamento' => 'Valle del Cauca',
                    'tipo_cliente' => 'empresa',
                    'activo' => true
                ]
            ];

            foreach ($clientes as $cliente) {
                // Mapear nombres de st_clientes (legacy) a las columnas unificadas en clientes
                Cliente::create([
                    'numero_identificacion' => $cliente['numero_documento'],
                    'tipo_documento'        => $cliente['tipo_documento'] ?? null,
                    'nombre_contacto'       => $cliente['nombre_completo'],
                    'razon_social'          => $cliente['razon_social'] ?? null,
                    'email'                 => $cliente['email'] ?? null,
                    'telefono'              => $cliente['telefono'] ?? null,
                    'celular'               => $cliente['celular'] ?? null,
                    'direccion'             => $cliente['direccion'] ?? null,
                    'ciudad_texto'          => $cliente['ciudad'] ?? null,
                    'departamento_texto'    => $cliente['departamento'] ?? null,
                    'tipo_cliente'          => $cliente['tipo_cliente'] ?? 'particular',
                    'observaciones'         => $cliente['observaciones'] ?? null,
                    'activo'                => $cliente['activo'] ?? true,
                ]);
            }

            // 3. Crear Equipos
            $equipos = [
                [
                    'cliente_id' => 1,
                    'tipo_equipo' => 'Cámara IP',
                    'marca' => 'Hikvision',
                    'modelo' => 'DS-2CD2143G0-I',
                    'numero_serie' => 'HIK123456789',
                    'mac_address' => '00:1A:2B:3C:4D:5E',
                    'ip_address' => '192.168.1.100',
                    'especificaciones' => '4MP, visión nocturna 30m, PoE',
                    'fecha_compra' => '2023-01-15',
                    'fecha_instalacion' => '2023-01-20',
                    'en_garantia' => true,
                    'vencimiento_garantia' => '2025-01-15',
                    'ubicacion_instalacion' => 'Entrada principal',
                    'estado' => 'operativo',
                    'activo' => true
                ],
                [
                    'cliente_id' => 1,
                    'tipo_equipo' => 'NVR',
                    'marca' => 'Hikvision',
                    'modelo' => 'DS-7608NI-K2/8P',
                    'numero_serie' => 'NVR987654321',
                    'mac_address' => '00:1A:2B:3C:4D:5F',
                    'ip_address' => '192.168.1.200',
                    'especificaciones' => '8 canales PoE, 2 HDD, H.265+',
                    'fecha_compra' => '2023-01-15',
                    'fecha_instalacion' => '2023-01-20',
                    'en_garantia' => true,
                    'vencimiento_garantia' => '2025-01-15',
                    'ubicacion_instalacion' => 'Cuarto de seguridad',
                    'estado' => 'operativo',
                    'activo' => true
                ],
                [
                    'cliente_id' => 2,
                    'tipo_equipo' => 'Cámara Analógica',
                    'marca' => 'Dahua',
                    'modelo' => 'HAC-HFW1200R',
                    'numero_serie' => 'DAH456789123',
                    'mac_address' => null,
                    'ip_address' => null,
                    'especificaciones' => '2MP, visión nocturna 20m',
                    'fecha_compra' => '2022-06-10',
                    'fecha_instalacion' => '2022-06-15',
                    'en_garantia' => false,
                    'vencimiento_garantia' => '2023-06-10',
                    'ubicacion_instalacion' => 'Garaje',
                    'estado' => 'en_reparacion',
                    'activo' => true
                ],
                [
                    'cliente_id' => 3,
                    'tipo_equipo' => 'DVR',
                    'marca' => 'Dahua',
                    'modelo' => 'XVR5116HS-I2',
                    'numero_serie' => 'DVR741852963',
                    'mac_address' => '00:1A:2B:3C:4D:60',
                    'ip_address' => '192.168.2.100',
                    'especificaciones' => '16 canales, pentabrid, H.265',
                    'fecha_compra' => '2023-03-20',
                    'fecha_instalacion' => '2023-03-25',
                    'en_garantia' => true,
                    'vencimiento_garantia' => '2025-03-20',
                    'ubicacion_instalacion' => 'Oficina administrativa',
                    'estado' => 'operativo',
                    'activo' => true
                ]
            ];

            foreach ($equipos as $equipo) {
                STEquipo::create($equipo);
            }

            // 4. Crear Repuestos
            $repuestos = [
                [
                    'codigo' => 'REP-001',
                    'nombre' => 'Fuente de poder 12V 2A',
                    'descripcion' => 'Fuente de alimentación para cámaras',
                    'categoria' => 'Fuentes',
                    'marca' => 'Genérica',
                    'modelo_compatible' => 'Universal',
                    'precio_costo' => 15000,
                    'precio_venta' => 25000,
                    'stock_actual' => 20,
                    'stock_minimo' => 5,
                    'ubicacion_bodega' => 'Estante A1',
                    'activo' => true
                ],
                [
                    'codigo' => 'REP-002',
                    'nombre' => 'Cable UTP Cat 6 (Metro)',
                    'descripcion' => 'Cable de red categoría 6',
                    'categoria' => 'Cables',
                    'marca' => 'Furukawa',
                    'modelo_compatible' => 'Universal',
                    'precio_costo' => 2000,
                    'precio_venta' => 3500,
                    'stock_actual' => 500,
                    'stock_minimo' => 100,
                    'ubicacion_bodega' => 'Estante B2',
                    'activo' => true
                ],
                [
                    'codigo' => 'REP-003',
                    'nombre' => 'Lente 2.8mm para cámara',
                    'descripcion' => 'Lente angular de repuesto',
                    'categoria' => 'Lentes',
                    'marca' => 'Hikvision',
                    'modelo_compatible' => 'Series DS-2CD',
                    'precio_costo' => 35000,
                    'precio_venta' => 55000,
                    'stock_actual' => 3,
                    'stock_minimo' => 5,
                    'ubicacion_bodega' => 'Estante C3',
                    'activo' => true
                ],
                [
                    'codigo' => 'REP-004',
                    'nombre' => 'Disco duro 2TB Purple',
                    'descripcion' => 'Disco duro para videovigilancia',
                    'categoria' => 'Almacenamiento',
                    'marca' => 'Western Digital',
                    'modelo_compatible' => 'DVR/NVR',
                    'precio_costo' => 280000,
                    'precio_venta' => 380000,
                    'stock_actual' => 8,
                    'stock_minimo' => 3,
                    'ubicacion_bodega' => 'Estante D1',
                    'activo' => true
                ]
            ];

            foreach ($repuestos as $repuesto) {
                STRepuesto::create($repuesto);
            }

            // 5. Crear Órdenes de Servicio
            $ordenes = [
                [
                    'numero_orden' => 'ST-2025-000001',
                    'cliente_id' => 2,
                    'st_equipo_id' => 3,
                    'st_tecnico_id' => 1,
                    'tipo_servicio' => 'Reparación',
                    'prioridad' => 'alta',
                    'estado' => 'en_proceso',
                    'descripcion_problema' => 'Cámara no muestra imagen, posible daño en lente',
                    'accesorios_entregados' => 'Cable de video',
                    'fecha_recepcion' => now()->subDays(3),
                    'fecha_promesa_entrega' => now()->addDays(2),
                    'fecha_asignacion' => now()->subDays(2),
                    'fecha_inicio_trabajo' => now()->subDays(1),
                    'costo_mano_obra' => 80000,
                    'user_id' => 1
                ],
                [
                    'numero_orden' => 'ST-2025-000002',
                    'cliente_id' => 1,
                    'st_equipo_id' => 2,
                    'st_tecnico_id' => 2,
                    'tipo_servicio' => 'Mantenimiento Preventivo',
                    'prioridad' => 'media',
                    'estado' => 'completada',
                    'descripcion_problema' => 'Mantenimiento programado del NVR',
                    'accesorios_entregados' => 'Control remoto, cable de red',
                    'fecha_recepcion' => now()->subDays(7),
                    'fecha_promesa_entrega' => now()->subDays(2),
                    'fecha_asignacion' => now()->subDays(6),
                    'fecha_inicio_trabajo' => now()->subDays(5),
                    'fecha_finalizacion' => now()->subDays(2),
                    'costo_mano_obra' => 120000,
                    'user_id' => 1
                ],
                [
                    'numero_orden' => 'ST-2025-000003',
                    'cliente_id' => 3,
                    'st_equipo_id' => 4,
                    'st_tecnico_id' => null,
                    'tipo_servicio' => 'Diagnóstico',
                    'prioridad' => 'urgente',
                    'estado' => 'recibida',
                    'descripcion_problema' => 'DVR no enciende, posible falla en fuente de alimentación',
                    'accesorios_entregados' => 'Cable de poder, control remoto',
                    'fecha_recepcion' => now(),
                    'fecha_promesa_entrega' => now()->addDays(3),
                    'costo_mano_obra' => null,
                    'user_id' => 1
                ]
            ];

            foreach ($ordenes as $orden) {
                $ordenCreada = STOrdenServicio::create($orden);

                // Registrar historial inicial
                $ordenCreada->historialEstados()->create([
                    'estado_anterior' => null,
                    'estado_nuevo' => $orden['estado'],
                    'observaciones' => 'Orden creada desde seeder',
                    'user_id' => 1
                ]);
            }

            // 6. Crear Diagnósticos para órdenes en proceso/completadas
            $diagnosticos = [
                [
                    'st_orden_servicio_id' => 1,
                    'st_tecnico_id' => 1,
                    'diagnostico_tecnico' => 'Se detectó daño en el lente de la cámara. La óptica está rayada.',
                    'fallas_encontradas' => 'Lente rayado, sensor funcionando correctamente',
                    'reparaciones_realizadas' => 'Reemplazo de lente',
                    'recomendaciones' => 'Proteger la cámara de impactos directos',
                    'requiere_repuestos' => true,
                    'repuestos_necesarios' => 'Lente 2.8mm compatible',
                    'tiempo_estimado_horas' => 2,
                    'costo_estimado' => 135000,
                    'aprobado_por_cliente' => true,
                    'fecha_diagnostico' => now()->subDays(1)
                ],
                [
                    'st_orden_servicio_id' => 2,
                    'st_tecnico_id' => 2,
                    'diagnostico_tecnico' => 'Equipo en buen estado general. Se realizó limpieza y actualización de firmware.',
                    'fallas_encontradas' => 'Acumulación de polvo, firmware desactualizado',
                    'reparaciones_realizadas' => 'Limpieza profunda, actualización de firmware, verificación de discos duros',
                    'recomendaciones' => 'Realizar mantenimiento cada 6 meses',
                    'requiere_repuestos' => false,
                    'repuestos_necesarios' => null,
                    'tiempo_estimado_horas' => 3,
                    'costo_estimado' => 120000,
                    'aprobado_por_cliente' => true,
                    'fecha_diagnostico' => now()->subDays(5)
                ]
            ];

            foreach ($diagnosticos as $diagnostico) {
                STDiagnostico::create($diagnostico);
            }

            DB::commit();

            $this->command->info('✅ Datos de ejemplo de Servicio Técnico creados exitosamente!');
            $this->command->info('📊 Resumen:');
            $this->command->info('   - 3 Técnicos');
            $this->command->info('   - 3 Clientes');
            $this->command->info('   - 4 Equipos');
            $this->command->info('   - 4 Repuestos');
            $this->command->info('   - 3 Órdenes de Servicio');
            $this->command->info('   - 2 Diagnósticos');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error al crear datos: ' . $e->getMessage());
            throw $e;
        }
    }
}
