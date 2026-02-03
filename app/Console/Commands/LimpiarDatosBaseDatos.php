<?php

namespace App\Console\Commands;

use App\Models\User;
use DirectoryIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class LimpiarDatosBaseDatos extends Command
{
    protected $signature = 'db:limpiar-datos
                            {--force : Ejecutar sin confirmacion}
                            {--solo-bd : Solo limpiar base de datos, no archivos}
                            {--solo-archivos : Solo limpiar archivos, no base de datos}
                            {--dry-run : Mostrar que se haria sin ejecutar}';

    protected $description = 'Limpia todos los datos de prueba manteniendo configuracion, roles y catalogos. Crea usuarios por rol.';

    private array $tablasALimpiar = [
        // NIVEL 1 - Tablas mas dependientes
        'parte_diario_producciones',
        'parte_diario_herbicidas',
        'parte_diario_lineas',
        'parte_diario_trabajadores',
        'primas_trabajador',
        'maquinaria_inspeccion_items',

        // NIVEL 2
        'partes_diarios',
        'fichajes',
        'maquinaria_inspecciones',
        'maquinaria_mantenimientos',
        'maquinaria_asignaciones',

        // NIVEL 3
        'obra_discrepancias_valoracion',
        'obra_conceptos_produccion',
        'obra_historial',
        'obra_documentos',
        'obra_hitos',
        'subcontrata_documentos_obra',

        // NIVEL 4 - Pivots de obra
        'obra_trabajadores',
        'obra_cuadrillas',
        'obra_subcontratas',

        // NIVEL 5 - Facturacion
        'factura_lineas',
        'ingresos',
        'gastos',
        'facturas',

        // NIVEL 6 - EPIs y bonos
        'trabajador_bonos',
        'epi_entregas',
        'epi_revisiones',
        'epi_inventario_documentos',
        'epi_inventario',

        // NIVEL 7
        'obras',

        // NIVEL 8 - Cuadrillas/Trabajadores
        'cuadrilla_trabajadores',
        'cuadrillas',
        'documento_lecturas',
        'trabajador_formaciones',
        'trabajador_documentos',
        'trabajador_historial_disciplinario',
        'trabajadores',

        // NIVEL 9 - Maquinaria/Vehiculos
        'maquinaria_documentos',
        'maquinaria',
        'vehiculo_documentos',
        'vehiculos',

        // NIVEL 10 - Contratos
        'contrato_liberaciones',
        'contratos',

        // NIVEL 11 - Subcontratas
        'subcontrata_documentos_cae',
        'subcontratas',

        // NIVEL 12 - Leads
        'lead_interacciones',
        'leads',

        // NIVEL 13 - Clientes
        'cliente_emails_adicionales',
        'clientes',

        // NIVEL 14 - Sistema
        'alertas',
        'auditoria',
        'email_logs',
        'documentos_empresa',
    ];

    private array $directoriosALimpiar = [
        'contratos',
        'facturas',
        'gastos',
        'obras',
        'trabajadores',
        'epis',
        'maquinaria',
        'profile-photos',
        'documentos-empresa',
        'caducidades',
        'actualizaciones_precios',
    ];

    public function handle(): int
    {
        $this->info('');
        $this->info('========================================');
        $this->info('  LIMPIEZA DE BASE DE DATOS - MANZER');
        $this->info('========================================');
        $this->info('');

        if ($this->option('dry-run')) {
            $this->warn('[MODO DRY-RUN] No se ejecutaran cambios reales');
            $this->info('');
        }

        // Verificar entorno
        if (app()->environment('production')) {
            $this->error('ADVERTENCIA: Estas en entorno de PRODUCCION!');
            if (!$this->option('force')) {
                $this->error('Usa --force para ejecutar en produccion (NO RECOMENDADO)');
                return 1;
            }
        }

        // Solicitar confirmacion
        if (!$this->option('force') && !$this->option('dry-run')) {
            $this->warn('Esta accion BORRARA todos los datos transaccionales:');
            $this->line('  - Obras, partes diarios, fichajes');
            $this->line('  - Facturas, gastos, ingresos');
            $this->line('  - Trabajadores, cuadrillas');
            $this->line('  - Clientes, contratos, leads');
            $this->line('  - Maquinaria, vehiculos, EPIs');
            $this->line('  - Archivos en public/uploads/');
            $this->info('');
            $this->info('Se PRESERVARAN:');
            $this->line('  - Roles y permisos');
            $this->line('  - Catalogos (tipos, categorias)');
            $this->line('  - Usuario admin@manzer.com');
            $this->info('');
            $this->info('Se CREARAN usuarios para cada rol con password: 12345678');
            $this->info('');

            if (!$this->confirm('Deseas continuar?', false)) {
                $this->info('Operacion cancelada.');
                return 0;
            }
        }

        $isDryRun = $this->option('dry-run');

        try {
            // Limpiar base de datos
            if (!$this->option('solo-archivos')) {
                $this->limpiarBaseDatos($isDryRun);
            }

            // Limpiar archivos
            if (!$this->option('solo-bd')) {
                $this->limpiarArchivos($isDryRun);
            }

            // Limpiar cache
            if (!$isDryRun) {
                $this->info('');
                $this->info('Limpiando cache...');
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');
                $this->line('  Cache limpiada');
            }

            $this->info('');
            $this->info('========================================');
            if ($isDryRun) {
                $this->warn('  DRY-RUN COMPLETADO (sin cambios)');
            } else {
                $this->info('  LIMPIEZA COMPLETADA EXITOSAMENTE');
            }
            $this->info('========================================');
            $this->info('');

            if (!$isDryRun) {
                $this->info('Usuarios disponibles (password: 12345678):');
                $this->table(
                    ['Email', 'Rol'],
                    [
                        ['admin@manzer.com', 'Administrador'],
                        ['contabilidad@manzer.com', 'Contabilidad'],
                        ['encargado@manzer.com', 'Encargado'],
                        ['rrhh@manzer.com', 'RRHH'],
                        ['auditor@manzer.com', 'Auditor'],
                        ['trabajador@manzer.com', 'Trabajador'],
                    ]
                );
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('Error durante la limpieza: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    private function limpiarBaseDatos(bool $isDryRun): void
    {
        $this->info('');
        $this->info('LIMPIANDO BASE DE DATOS...');
        $this->info('');

        // Deshabilitar FK checks
        if (!$isDryRun) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        try {
            // 1. Truncar tablas transaccionales
            $this->info('Truncando tablas transaccionales...');
            foreach ($this->tablasALimpiar as $tabla) {
                if (!Schema::hasTable($tabla)) {
                    $this->line("  [SKIP] Tabla no existe: {$tabla}");
                    continue;
                }

                if ($isDryRun) {
                    $count = DB::table($tabla)->count();
                    $this->line("  [DRY-RUN] Truncaria: {$tabla} ({$count} registros)");
                } else {
                    $count = DB::table($tabla)->count();
                    DB::table($tabla)->truncate();
                    $this->line("  Truncada: {$tabla} ({$count} registros eliminados)");
                }
            }

            // 2. Limpiar usuarios
            $this->info('');
            $this->info('Limpiando usuarios...');
            $this->limpiarUsuarios($isDryRun);

            // 3. Crear usuarios por rol
            $this->info('');
            $this->info('Creando usuarios por rol...');
            $this->crearUsuariosPorRol($isDryRun);

        } finally {
            // Rehabilitar FK checks
            if (!$isDryRun) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }

    private function limpiarUsuarios(bool $isDryRun): void
    {
        // Obtener usuario admin a preservar
        $adminEmail = 'admin@manzer.com';
        $admin = User::where('email', $adminEmail)->first();

        if ($admin) {
            // Contar usuarios a eliminar
            $countToDelete = User::where('id', '!=', $admin->id)->count();

            if ($isDryRun) {
                $this->line("  [DRY-RUN] Eliminaria {$countToDelete} usuarios (preservando: {$adminEmail})");
            } else {
                // Eliminar asignaciones de roles de otros usuarios
                DB::table('model_has_roles')
                    ->where('model_type', User::class)
                    ->where('model_id', '!=', $admin->id)
                    ->delete();

                DB::table('model_has_permissions')
                    ->where('model_type', User::class)
                    ->where('model_id', '!=', $admin->id)
                    ->delete();

                // Eliminar usuarios excepto admin
                User::where('id', '!=', $admin->id)->forceDelete();

                $this->line("  Eliminados {$countToDelete} usuarios (preservado: {$adminEmail})");
            }
        } else {
            $this->warn("  Usuario {$adminEmail} no encontrado - se creara nuevo");

            if (!$isDryRun) {
                // Limpiar todos los usuarios
                $countToDelete = User::count();
                DB::table('model_has_roles')->where('model_type', User::class)->delete();
                DB::table('model_has_permissions')->where('model_type', User::class)->delete();
                User::query()->forceDelete();
                $this->line("  Eliminados {$countToDelete} usuarios");
            }
        }
    }

    private function crearUsuariosPorRol(bool $isDryRun): void
    {
        $password = Hash::make('12345678');

        $usuarios = [
            ['name' => 'Administrador', 'email' => 'admin@manzer.com', 'role' => 'Administrador'],
            ['name' => 'Usuario Contabilidad', 'email' => 'contabilidad@manzer.com', 'role' => 'Contabilidad'],
            ['name' => 'Usuario Encargado', 'email' => 'encargado@manzer.com', 'role' => 'Encargado'],
            ['name' => 'Usuario RRHH', 'email' => 'rrhh@manzer.com', 'role' => 'RRHH'],
            ['name' => 'Usuario Auditor', 'email' => 'auditor@manzer.com', 'role' => 'Auditor'],
            ['name' => 'Usuario Trabajador', 'email' => 'trabajador@manzer.com', 'role' => 'Trabajador'],
        ];

        foreach ($usuarios as $userData) {
            if ($isDryRun) {
                $this->line("  [DRY-RUN] Crearia/actualizaria: {$userData['email']} ({$userData['role']})");
                continue;
            }

            $user = User::where('email', $userData['email'])->first();

            if ($user) {
                // Actualizar password del usuario existente
                $user->update([
                    'password' => $password,
                    'email_verified_at' => now(),
                ]);
                $action = 'Actualizado';
            } else {
                // Crear nuevo usuario
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => $password,
                    'email_verified_at' => now(),
                ]);
                $action = 'Creado';
            }

            // Sincronizar rol
            $user->syncRoles([$userData['role']]);

            $this->line("  {$action}: {$userData['email']} ({$userData['role']})");
        }
    }

    private function limpiarArchivos(bool $isDryRun): void
    {
        $this->info('');
        $this->info('LIMPIANDO ARCHIVOS...');
        $this->info('');

        $uploadsPath = public_path('uploads');

        if (!is_dir($uploadsPath)) {
            $this->warn("  Directorio uploads/ no existe");
            return;
        }

        foreach ($this->directoriosALimpiar as $dir) {
            $fullPath = $uploadsPath . DIRECTORY_SEPARATOR . $dir;

            if (!is_dir($fullPath)) {
                $this->line("  [SKIP] No existe: uploads/{$dir}");
                continue;
            }

            $fileCount = $this->contarArchivosRecursivo($fullPath);

            if ($isDryRun) {
                $this->line("  [DRY-RUN] Limpiaria: uploads/{$dir} ({$fileCount} archivos)");
            } else {
                $this->limpiarDirectorioRecursivo($fullPath);
                $this->line("  Limpiado: uploads/{$dir} ({$fileCount} archivos eliminados)");
            }
        }
    }

    private function contarArchivosRecursivo(string $path): int
    {
        $count = 0;

        if (!is_dir($path)) {
            return 0;
        }

        $items = new DirectoryIterator($path);

        foreach ($items as $item) {
            if ($item->isDot()) {
                continue;
            }

            if ($item->isDir()) {
                $count += $this->contarArchivosRecursivo($item->getPathname());
            } else {
                $count++;
            }
        }

        return $count;
    }

    private function limpiarDirectorioRecursivo(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $items = new DirectoryIterator($path);

        foreach ($items as $item) {
            if ($item->isDot()) {
                continue;
            }

            if ($item->isDir()) {
                $this->limpiarDirectorioRecursivo($item->getPathname());
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }
    }
}
