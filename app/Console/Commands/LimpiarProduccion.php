<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LimpiarProduccion extends Command
{
    protected $signature = 'sistema:limpiar-produccion
                            {--dry-run : Mostrar que se eliminaria sin ejecutar}
                            {--force : Ejecutar sin confirmacion}';

    protected $description = 'Vacia ordenes, entregas, pagos, items, logs y notificaciones para arrancar produccion con BD limpia. Preserva usuarios, clientes, catalogos y configuracion.';

    /**
     * Tablas a vaciar en orden (hojas -> raiz).
     * El orden importa aunque deshabilitemos FK checks: refleja la jerarquia real.
     */
    private array $tablas = [
        'entrega_piezas',
        'historial_avances',
        'orden_comentarios',
        'orden_documentos',
        'orden_fotos',
        'pagos',
        'asignaciones_piezas',
        'devoluciones_garantia',
        'entregas',
        'orden_items',
        'orden_bosquejos',
        'orden_piezas',
        'ordenes',
        'registro_actividades',
        'notificaciones',
    ];

    private string $fraseConfirmacion = 'CONFIRMAR LIMPIEZA';

    public function handle(): int
    {
        $this->warn('=== LIMPIEZA TOTAL DE PRODUCCION ===');
        $this->newLine();

        $conteos = $this->obtenerConteos();
        $total = array_sum($conteos);

        $this->info('Registros actuales en tablas a vaciar:');
        $this->tablaConteos($conteos);
        $this->info("TOTAL: {$total} registro(s)");
        $this->newLine();

        $carpetaOrdenes = public_path('uploads/ordenes');
        $archivosInfo = $this->analizarArchivos($carpetaOrdenes);
        $this->info("Archivos fisicos en public/uploads/ordenes/: {$archivosInfo['archivos']} archivo(s) en {$archivosInfo['carpetas']} carpeta(s)");
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('Modo --dry-run: No se elimino nada.');
            return self::SUCCESS;
        }

        if ($total === 0 && $archivosInfo['archivos'] === 0) {
            $this->info('Nada que limpiar. Todo ya esta vacio.');
            return self::SUCCESS;
        }

        if (!$this->option('force')) {
            $this->warn('ATENCION: Esta operacion es IRREVERSIBLE.');
            $this->warn('Se vaciaran ' . count($this->tablas) . ' tablas y todos los archivos en public/uploads/ordenes/.');
            $this->newLine();

            $respuesta = $this->ask("Para confirmar, escriba exactamente: {$this->fraseConfirmacion}");

            if ($respuesta !== $this->fraseConfirmacion) {
                $this->error('Confirmacion incorrecta. Operacion cancelada.');
                return self::FAILURE;
            }
        }

        // TRUNCATE es DDL en MySQL: hace commit implicito, no se puede envolver en transaction.
        // Si falla un TRUNCATE la excepcion detiene el proceso.
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            foreach ($this->tablas as $tabla) {
                DB::table($tabla)->truncate();
                $this->line("  Truncada: {$tabla}");
            }

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            $this->newLine();
            $this->info('Tablas vaciadas correctamente.');
        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->error("Error durante la limpieza: {$e->getMessage()}");
            return self::FAILURE;
        }

        $this->limpiarArchivos($carpetaOrdenes);

        $this->newLine();
        $this->info('=== LIMPIEZA COMPLETADA ===');
        $conteosFinales = $this->obtenerConteos();
        $this->tablaConteos($conteosFinales);

        return self::SUCCESS;
    }

    private function obtenerConteos(): array
    {
        $conteos = [];
        foreach ($this->tablas as $tabla) {
            $conteos[$tabla] = DB::table($tabla)->count();
        }
        return $conteos;
    }

    private function tablaConteos(array $conteos): void
    {
        $filas = [];
        foreach ($conteos as $tabla => $cantidad) {
            $filas[] = [$tabla, $cantidad];
        }
        $this->table(['Tabla', 'Registros'], $filas);
    }

    private function analizarArchivos(string $carpeta): array
    {
        if (!File::isDirectory($carpeta)) {
            return ['archivos' => 0, 'carpetas' => 0];
        }

        $archivos = count(File::allFiles($carpeta));
        $carpetas = count(File::directories($carpeta));

        return ['archivos' => $archivos, 'carpetas' => $carpetas];
    }

    private function limpiarArchivos(string $carpeta): void
    {
        if (!File::isDirectory($carpeta)) {
            $this->warn("Carpeta no existe: {$carpeta}");
            return;
        }

        File::cleanDirectory($carpeta);
        $this->info("Archivos fisicos eliminados de: {$carpeta}");
    }
}
