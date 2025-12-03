<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Crea usuarios de prueba para cada rol del sistema.
     * Password para todos: 12345678
     *
     * @return void
     */
    public function run()
    {
        // Verificar que los roles existan
        $roles = ['Administrador', 'Supervisor', 'Agricultor', 'Inversionista', 'Vendedor'];

        foreach ($roles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                $this->command->warn("⚠️  Rol '{$roleName}' no existe. Ejecuta RolesAndPermissionsSeeder primero.");
                return;
            }
        }

        // 1. Usuario Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@agromarket.com'],
            [
                'name' => 'Administrador Principal',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'telefono' => '3001234567',
                'activo' => true,
                'documento_identidad' => '1000000001',
                'tipo_documento' => 'CC',
                'pais' => 'CO',
                'ciudad' => 'Bogotá',
                'kyc_status' => 'aprobado',
                'kyc_aprobado_at' => now(),
            ]
        );

        if (!$admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
            $this->command->info('✅ Usuario Administrador creado: admin@agromarket.com');
        }

        // 2. Usuario Supervisor
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@agromarket.com'],
            [
                'name' => 'Supervisor de Proyectos',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'telefono' => '3001234568',
                'activo' => true,
                'documento_identidad' => '1000000002',
                'tipo_documento' => 'CC',
                'pais' => 'CO',
                'ciudad' => 'Medellín',
                'kyc_status' => 'aprobado',
                'kyc_aprobado_at' => now(),
            ]
        );

        if (!$supervisor->hasRole('Supervisor')) {
            $supervisor->assignRole('Supervisor');
            $this->command->info('✅ Usuario Supervisor creado: supervisor@agromarket.com');
        }

        // 3. Usuario Agricultor
        $agricultor = User::firstOrCreate(
            ['email' => 'agricultor@agromarket.com'],
            [
                'name' => 'Carlos Agricultor Ramírez',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'telefono' => '3001234569',
                'activo' => true,
                'documento_identidad' => '1000000003',
                'tipo_documento' => 'CC',
                'pais' => 'CO',
                'ciudad' => 'Cali',
                'kyc_status' => 'aprobado',
                'kyc_aprobado_at' => now(),
            ]
        );

        if (!$agricultor->hasRole('Agricultor')) {
            $agricultor->assignRole('Agricultor');
            $this->command->info('✅ Usuario Agricultor creado: agricultor@agromarket.com');
        }

        // 4. Usuario Inversionista (KYC aprobado)
        $inversionista = User::firstOrCreate(
            ['email' => 'inversionista@agromarket.com'],
            [
                'name' => 'María Inversionista López',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'telefono' => '3001234570',
                'activo' => true,
                'documento_identidad' => '1000000004',
                'tipo_documento' => 'CC',
                'fecha_nacimiento' => '1985-05-15',
                'pais' => 'CO',
                'ciudad' => 'Barranquilla',
                'direccion' => 'Calle 100 # 20-30',
                'kyc_status' => 'aprobado',
                'kyc_aprobado_at' => now(),
                'kyc_aprobado_por' => $admin->id,
            ]
        );

        if (!$inversionista->hasRole('Inversionista')) {
            $inversionista->assignRole('Inversionista');
            $this->command->info('✅ Usuario Inversionista creado: inversionista@agromarket.com');
        }

        // Crear billetera para el inversionista
        if (!$inversionista->billetera) {
            $inversionista->billetera()->create([
                'saldo_disponible' => 10000000, // 10 millones para testing
                'saldo_bloqueado' => 0,
                'saldo_invertido' => 0,
                'retornos_acumulados' => 0,
                'dividendos_pendientes' => 0,
            ]);
            $this->command->info('✅ Billetera creada para inversionista con saldo inicial de $10,000,000');
        }

        // 5. Usuario Inversionista (KYC pendiente)
        $inversionistaPendiente = User::firstOrCreate(
            ['email' => 'inversionista.pendiente@agromarket.com'],
            [
                'name' => 'Juan Inversionista Sin KYC',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'telefono' => '3001234571',
                'activo' => true,
                'documento_identidad' => '1000000005',
                'tipo_documento' => 'CC',
                'pais' => 'CO',
                'ciudad' => 'Cartagena',
                'kyc_status' => 'pendiente', // SIN KYC
            ]
        );

        if (!$inversionistaPendiente->hasRole('Inversionista')) {
            $inversionistaPendiente->assignRole('Inversionista');
            $this->command->info('✅ Usuario Inversionista (KYC pendiente) creado: inversionista.pendiente@agromarket.com');
        }

        // 6. Usuario Vendedor
        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor@agromarket.com'],
            [
                'name' => 'Pedro Vendedor García',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'telefono' => '3001234572',
                'activo' => true,
                'documento_identidad' => '1000000006',
                'tipo_documento' => 'CC',
                'pais' => 'CO',
                'ciudad' => 'Bucaramanga',
                'kyc_status' => 'aprobado',
                'kyc_aprobado_at' => now(),
            ]
        );

        if (!$vendedor->hasRole('Vendedor')) {
            $vendedor->assignRole('Vendedor');
            $this->command->info('✅ Usuario Vendedor creado: vendedor@agromarket.com');
        }

        $this->command->info('');
        $this->command->info('📋 RESUMEN DE USUARIOS DE PRUEBA:');
        $this->command->info('===============================================');
        $this->command->info('Email: admin@agromarket.com | Password: 12345678 | Rol: Administrador');
        $this->command->info('Email: supervisor@agromarket.com | Password: 12345678 | Rol: Supervisor');
        $this->command->info('Email: agricultor@agromarket.com | Password: 12345678 | Rol: Agricultor');
        $this->command->info('Email: inversionista@agromarket.com | Password: 12345678 | Rol: Inversionista (KYC ✅)');
        $this->command->info('Email: inversionista.pendiente@agromarket.com | Password: 12345678 | Rol: Inversionista (KYC ❌)');
        $this->command->info('Email: vendedor@agromarket.com | Password: 12345678 | Rol: Vendedor');
        $this->command->info('===============================================');
    }
}
