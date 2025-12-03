<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Billetera;
use App\Models\CategoriaProyecto;
use App\Models\Proyecto;
use App\Models\Inversion;
use App\Models\Dividendo;
use App\Models\Deposito;
use App\Models\Retiro;
use App\Models\DocumentoKyc;
use App\Models\Prospecto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Poblar la base de datos con datos de prueba.
     */
    public function run()
    {
        $this->command->info('🌱 Iniciando TestDataSeeder...');

        // 1. Obtener usuarios existentes
        $admin = User::where('email', 'admin@agromarket.com')->first();
        $inversionista = User::where('email', 'inversionista@agromarket.com')->first();
        $agricultor = User::where('email', 'agricultor@agromarket.com')->first();
        $vendedor = User::where('email', 'vendedor@agromarket.com')->first();

        if (!$inversionista || !$agricultor) {
            $this->command->error('❌ Ejecuta primero TestUsersSeeder');
            return;
        }

        // 2. Crear categorías si no existen
        $this->command->info('📁 Creando categorías...');
        $categorias = [
            [
                'codigo' => 'STAKING',
                'nombre' => 'Staking',
                'descripcion' => 'Proyectos de inversión a largo plazo',
                'duracion_minima_meses' => 12,
                'duracion_maxima_meses' => 18,
                'roi_minimo' => 21.00,
                'roi_maximo' => 35.00,
                'inversion_minima' => 100000,
                'inversion_maxima' => 10000000,
                'permite_retiro_anticipado' => false,
                'permite_trading' => false,
                'activo' => true,
            ],
            [
                'codigo' => 'TRADING',
                'nombre' => 'Trading',
                'descripcion' => 'Inversiones con opción de venta',
                'duracion_minima_meses' => 12,
                'duracion_maxima_meses' => 18,
                'roi_minimo' => 21.00,
                'roi_maximo' => 35.00,
                'inversion_minima' => 100000,
                'inversion_maxima' => 10000000,
                'permite_retiro_anticipado' => false,
                'permite_trading' => true,
                'activo' => true,
            ],
            [
                'codigo' => 'EAR',
                'nombre' => 'Early Anticipated Retirement',
                'descripcion' => 'Permite retiro anticipado con penalización',
                'duracion_minima_meses' => 12,
                'duracion_maxima_meses' => 24,
                'roi_minimo' => 18.00,
                'roi_maximo' => 30.00,
                'inversion_minima' => 50000,
                'inversion_maxima' => 5000000,
                'permite_retiro_anticipado' => true,
                'permite_trading' => false,
                'activo' => true,
            ],
        ];

        foreach ($categorias as $catData) {
            CategoriaProyecto::firstOrCreate(
                ['codigo' => $catData['codigo']],
                $catData
            );
        }

        // 3. Crear billeteras para inversionistas
        $this->command->info('💰 Creando billeteras...');
        Billetera::firstOrCreate(
            ['usuario_id' => $inversionista->id],
            [
                'saldo_disponible' => 5000000,
                'saldo_bloqueado' => 0,
                'saldo_invertido' => 3000000,
                'retornos_acumulados' => 450000,
                'dividendos_pendientes' => 150000,
            ]
        );

        // 4. Crear proyectos
        $this->command->info('🌾 Creando proyectos...');
        $staking = CategoriaProyecto::where('codigo', 'STAKING')->first();
        $trading = CategoriaProyecto::where('codigo', 'TRADING')->first();
        $ear = CategoriaProyecto::where('codigo', 'EAR')->first();

        $proyectos = [
            [
                'codigo' => 'STK-2025-001',
                'categoria_id' => $staking->id,
                'agricultor_id' => $agricultor->id,
                'nombre' => 'Cultivo de Aguacate Hass - Valle del Cauca',
                'descripcion' => 'Proyecto de cultivo de aguacate Hass en 10 hectáreas con tecnología de riego por goteo.',
                'ubicacion' => 'Valle del Cauca, Colombia',
                'coordenadas' => '3.4516,-76.5320',
                'monto_objetivo' => 50000000,
                'monto_recaudado' => 35000000,
                'inversion_minima' => 500000,
                'inversion_maxima' => 5000000,
                'roi_anual' => 28.00,
                'duracion_meses' => 18,
                'periodo_dividendos_dias' => 90,
                'fecha_inicio_recaudacion' => now()->subDays(30),
                'fecha_cierre_recaudacion' => now()->addDays(30),
                'estado' => 'en_recaudacion',
                'aprobado_por' => $admin->id,
                'aprobado_at' => now()->subDays(35),
                'nivel_riesgo' => 'medio',
                'verificado' => true,
                'destacado' => true,
                'activo' => true,
            ],
            [
                'codigo' => 'TRA-2025-002',
                'categoria_id' => $trading->id,
                'agricultor_id' => $agricultor->id,
                'nombre' => 'Producción de Café Especial - Antioquia',
                'descripcion' => 'Cultivo de café especial de altura en 8 hectáreas con certificación orgánica.',
                'ubicacion' => 'Antioquia, Colombia',
                'coordenadas' => '6.2442,-75.5812',
                'monto_objetivo' => 30000000,
                'monto_recaudado' => 30000000,
                'inversion_minima' => 300000,
                'inversion_maxima' => 3000000,
                'roi_anual' => 32.00,
                'duracion_meses' => 16,
                'periodo_dividendos_dias' => 120,
                'fecha_inicio_recaudacion' => now()->subDays(60),
                'fecha_cierre_recaudacion' => now()->subDays(10),
                'estado' => 'fondeado',
                'aprobado_por' => $admin->id,
                'aprobado_at' => now()->subDays(65),
                'nivel_riesgo' => 'bajo',
                'verificado' => true,
                'destacado' => true,
                'activo' => true,
            ],
            [
                'codigo' => 'EAR-2025-003',
                'categoria_id' => $ear->id,
                'agricultor_id' => $agricultor->id,
                'nombre' => 'Cultivo de Limón Tahití - Santander',
                'descripcion' => 'Proyecto de limón tahití para exportación, 12 hectáreas.',
                'ubicacion' => 'Santander, Colombia',
                'coordenadas' => '7.1301,-73.1197',
                'monto_objetivo' => 40000000,
                'monto_recaudado' => 15000000,
                'inversion_minima' => 400000,
                'inversion_maxima' => 4000000,
                'roi_anual' => 25.00,
                'duracion_meses' => 20,
                'periodo_dividendos_dias' => 90,
                'fecha_inicio_recaudacion' => now()->subDays(15),
                'fecha_cierre_recaudacion' => now()->addDays(45),
                'estado' => 'en_recaudacion',
                'aprobado_por' => $admin->id,
                'aprobado_at' => now()->subDays(20),
                'nivel_riesgo' => 'medio',
                'verificado' => true,
                'destacado' => false,
                'activo' => true,
            ],
            [
                'codigo' => 'STK-2025-004',
                'categoria_id' => $staking->id,
                'agricultor_id' => $agricultor->id,
                'nombre' => 'Producción de Plátano - Quindío',
                'descripcion' => 'Proyecto de producción de plátano tradicional en 15 hectáreas con sistema de riego automatizado.',
                'ubicacion' => 'Quindío, Colombia',
                'coordenadas' => '4.4611,-75.6679',
                'monto_objetivo' => 45000000,
                'monto_recaudado' => 0,
                'inversion_minima' => 450000,
                'inversion_maxima' => 4500000,
                'roi_anual' => 24.00,
                'duracion_meses' => 14,
                'periodo_dividendos_dias' => 90,
                'fecha_inicio_recaudacion' => now()->addDays(90),
                'fecha_cierre_recaudacion' => now()->addDays(150),
                'estado' => 'en_revision',
                'aprobado_por' => null,
                'aprobado_at' => null,
                'nivel_riesgo' => 'medio',
                'verificado' => false,
                'destacado' => false,
                'activo' => false,
            ],
        ];

        foreach ($proyectos as $proyData) {
            Proyecto::firstOrCreate(
                ['codigo' => $proyData['codigo']],
                $proyData
            );
        }

        // 5. Crear inversiones
        $this->command->info('💵 Creando inversiones...');
        $proyecto1 = Proyecto::where('codigo', 'STK-2025-001')->first();
        $proyecto2 = Proyecto::where('codigo', 'TRA-2025-002')->first();

        $inversiones = [
            [
                'codigo_inversion' => 'INV-2025-00001',
                'usuario_id' => $inversionista->id,
                'proyecto_id' => $proyecto1->id,
                'monto_invertido' => 2000000,
                'valor_actual' => 2000000,
                'ganancia_acumulada' => 280000,
                'dividendos_acumulados' => 280000,
                'fecha_inversion' => now()->subDays(25),
                'fecha_vencimiento' => now()->addMonths(18),
                'estado' => 'activa',
                'disponible_trading' => false,
            ],
            [
                'codigo_inversion' => 'INV-2025-00002',
                'usuario_id' => $inversionista->id,
                'proyecto_id' => $proyecto2->id,
                'monto_invertido' => 1000000,
                'valor_actual' => 1000000,
                'ganancia_acumulada' => 170000,
                'dividendos_acumulados' => 170000,
                'fecha_inversion' => now()->subDays(55),
                'fecha_vencimiento' => now()->addMonths(16),
                'estado' => 'activa',
                'disponible_trading' => true,
            ],
        ];

        foreach ($inversiones as $invData) {
            Inversion::firstOrCreate(
                ['codigo_inversion' => $invData['codigo_inversion']],
                $invData
            );
        }

        // 6. Crear dividendos
        $this->command->info('💰 Creando dividendos...');
        $inversion1 = Inversion::where('codigo_inversion', 'INV-2025-00001')->first();

        $dividendos = [
            [
                'codigo_dividendo' => 'DIV-2025-00001',
                'inversion_id' => $inversion1->id,
                'proyecto_id' => $proyecto1->id,
                'usuario_id' => $inversionista->id,
                'numero_periodo' => 1,
                'monto' => 140000,
                'fecha_programada' => now()->subDays(5),
                'fecha_pagada' => now()->subDays(5),
                'estado' => 'pagado',
                'pagado_por' => $admin->id,
            ],
            [
                'codigo_dividendo' => 'DIV-2025-00002',
                'inversion_id' => $inversion1->id,
                'proyecto_id' => $proyecto1->id,
                'usuario_id' => $inversionista->id,
                'numero_periodo' => 2,
                'monto' => 140000,
                'fecha_programada' => now()->addDays(85),
                'estado' => 'programado',
            ],
        ];

        foreach ($dividendos as $divData) {
            Dividendo::firstOrCreate(
                ['codigo_dividendo' => $divData['codigo_dividendo']],
                $divData
            );
        }

        // 7. Crear depósitos
        $this->command->info('📥 Creando depósitos...');
        $depositos = [
            [
                'codigo_deposito' => 'DEP-2025-00001',
                'usuario_id' => $inversionista->id,
                'monto' => 5000000,
                'metodo_pago' => 'transferencia_bancaria',
                'referencia_pago' => '123456789',
                'fecha_deposito' => now()->subDays(60),
                'estado' => 'verificado',
                'verificado_por' => $admin->id,
                'verificado_at' => now()->subDays(60),
            ],
            [
                'codigo_deposito' => 'DEP-2025-00002',
                'usuario_id' => $inversionista->id,
                'monto' => 3000000,
                'metodo_pago' => 'pse',
                'referencia_pago' => 'PSE-987654321',
                'fecha_deposito' => now()->subDays(30),
                'estado' => 'verificado',
                'verificado_por' => $admin->id,
                'verificado_at' => now()->subDays(30),
            ],
        ];

        foreach ($depositos as $depData) {
            Deposito::firstOrCreate(
                ['codigo_deposito' => $depData['codigo_deposito']],
                $depData
            );
        }

        // 8. Crear retiros pendientes
        $this->command->info('📤 Creando retiros...');
        $retiros = [
            [
                'codigo_retiro' => 'RET-2025-00001',
                'usuario_id' => $inversionista->id,
                'monto_solicitado' => 500000,
                'monto_aprobado' => 500000,
                'comision' => 0,
                'monto_neto' => 500000,
                'metodo_pago' => 'transferencia_bancaria',
                'datos_pago' => json_encode([
                    'banco' => 'Bancolombia',
                    'tipo_cuenta' => 'ahorros',
                    'numero_cuenta' => '****5678',
                    'titular' => 'Inversionista Test'
                ]),
                'fecha_solicitud' => now()->subDays(2),
                'estado' => 'pendiente',
            ],
            [
                'codigo_retiro' => 'RET-2025-00002',
                'usuario_id' => $inversionista->id,
                'monto_solicitado' => 300000,
                'monto_aprobado' => 300000,
                'comision' => 0,
                'monto_neto' => 300000,
                'metodo_pago' => 'transferencia_bancaria',
                'datos_pago' => json_encode([
                    'banco' => 'Bancolombia',
                    'tipo_cuenta' => 'ahorros',
                    'numero_cuenta' => '****5678',
                    'titular' => 'Inversionista Test'
                ]),
                'fecha_solicitud' => now()->subDays(5),
                'fecha_aprobacion' => now()->subDays(4),
                'estado' => 'aprobado',
                'aprobado_por' => $admin->id,
            ],
        ];

        foreach ($retiros as $retData) {
            Retiro::firstOrCreate(
                ['codigo_retiro' => $retData['codigo_retiro']],
                $retData
            );
        }

        // 9. Crear documentos KYC pendientes
        $this->command->info('📄 Creando documentos KYC...');
        $kycDocs = [
            [
                'usuario_id' => $inversionista->id,
                'tipo_documento' => 'cedula_frontal',
                'nombre_archivo' => 'cedula_frontal.jpg',
                'ruta_archivo' => 'kyc/inversionista/cedula_frontal.jpg',
                'mime_type' => 'image/jpeg',
                'tamanio_kb' => 245,
                'fecha_subida' => now()->subDays(3),
                'estado' => 'pendiente_revision',
            ],
        ];

        foreach ($kycDocs as $kycData) {
            DocumentoKyc::firstOrCreate(
                [
                    'usuario_id' => $kycData['usuario_id'],
                    'tipo_documento' => $kycData['tipo_documento']
                ],
                $kycData
            );
        }

        // 10. Crear prospectos
        if ($vendedor) {
            $this->command->info('👥 Creando prospectos...');
            $prospectos = [
                [
                    'codigo_prospecto' => 'PROS-2025-00001',
                    'nombre' => 'María García',
                    'email' => 'maria@example.com',
                    'telefono' => '3001234567',
                    'tipo' => 'inversionista',
                    'estado' => 'interesado',
                    'origen' => 'redes_sociales',
                    'asignado_a' => $vendedor->id,
                    'fecha_contacto' => now()->subDays(5),
                ],
                [
                    'codigo_prospecto' => 'PROS-2025-00002',
                    'nombre' => 'Carlos Rodríguez',
                    'email' => 'carlos@example.com',
                    'telefono' => '3107654321',
                    'tipo' => 'inversionista',
                    'estado' => 'contactado',
                    'origen' => 'web',
                    'asignado_a' => $vendedor->id,
                    'fecha_contacto' => now()->subDays(10),
                ],
                [
                    'codigo_prospecto' => 'PROS-2025-00003',
                    'nombre' => 'Ana Martínez',
                    'email' => 'ana.martinez@example.com',
                    'telefono' => '3159876543',
                    'tipo' => 'inversionista',
                    'estado' => 'convertido',
                    'origen' => 'referido',
                    'asignado_a' => $vendedor->id,
                    'fecha_contacto' => now()->subDays(45),
                    'fecha_conversion' => now()->subDays(30),
                    'usuario_convertido_id' => $inversionista->id,
                ],
                [
                    'codigo_prospecto' => 'PROS-2025-00004',
                    'nombre' => 'Luis Fernández',
                    'email' => 'luis.f@example.com',
                    'telefono' => '3124567890',
                    'tipo' => 'inversionista',
                    'estado' => 'negociacion',
                    'origen' => 'evento',
                    'asignado_a' => $vendedor->id,
                    'fecha_contacto' => now()->subDays(8),
                ],
                [
                    'codigo_prospecto' => 'PROS-2025-00005',
                    'nombre' => 'Patricia Gómez',
                    'email' => 'patricia.gomez@example.com',
                    'telefono' => '3187654321',
                    'tipo' => 'inversionista',
                    'estado' => 'nuevo',
                    'origen' => 'web',
                    'asignado_a' => $vendedor->id,
                    'fecha_contacto' => now()->subDays(2),
                ],
                [
                    'codigo_prospecto' => 'PROS-2025-00006',
                    'nombre' => 'Roberto Silva',
                    'email' => 'roberto.silva@example.com',
                    'telefono' => '3145678901',
                    'tipo' => 'agricultor',
                    'estado' => 'interesado',
                    'origen' => 'telemarketing',
                    'asignado_a' => $vendedor->id,
                    'fecha_contacto' => now()->subDays(12),
                ],
                [
                    'codigo_prospecto' => 'PROS-2025-00007',
                    'nombre' => 'Sandra López',
                    'email' => 'sandra.lopez@example.com',
                    'telefono' => '3165432109',
                    'tipo' => 'inversionista',
                    'estado' => 'descartado',
                    'origen' => 'redes_sociales',
                    'asignado_a' => $vendedor->id,
                    'fecha_contacto' => now()->subDays(20),
                ],
                [
                    'codigo_prospecto' => 'PROS-2025-00008',
                    'nombre' => 'Diego Ramírez',
                    'email' => 'diego.ramirez@example.com',
                    'telefono' => '3198765432',
                    'tipo' => 'inversionista',
                    'estado' => 'negociacion',
                    'origen' => 'referido',
                    'asignado_a' => $vendedor->id,
                    'fecha_contacto' => now()->subDays(6),
                ],
            ];

            foreach ($prospectos as $prosData) {
                Prospecto::firstOrCreate(
                    ['codigo_prospecto' => $prosData['codigo_prospecto']],
                    $prosData
                );
            }
        }

        $this->command->info('✅ TestDataSeeder completado exitosamente!');
        $this->command->info('📊 Datos creados:');
        $this->command->info('  - ' . CategoriaProyecto::count() . ' categorías');
        $this->command->info('  - ' . Proyecto::count() . ' proyectos');
        $this->command->info('  - ' . Inversion::count() . ' inversiones');
        $this->command->info('  - ' . Dividendo::count() . ' dividendos');
        $this->command->info('  - ' . Deposito::count() . ' depósitos');
        $this->command->info('  - ' . Retiro::count() . ' retiros');
        $this->command->info('  - ' . DocumentoKyc::count() . ' documentos KYC');
        $this->command->info('  - ' . Prospecto::count() . ' prospectos');
    }
}
