<?php

namespace App\Console\Commands;

use App\Models\ImagenProducto;
use Illuminate\Console\Command;

/**
 * Pre-genera (y cachea en disco) las miniaturas de las imágenes principales
 * que usan los PDF de cotización. Ejecutar una vez tras el deploy evita que la
 * primera exportación de cada cotización pague el costo de generar decenas de
 * miniaturas en caliente (causa de la lentitud / 504 al exportar).
 *
 *   php artisan pdf:miniaturas           # genera las que falten
 *   php artisan pdf:miniaturas --force   # regenera todas
 */
class GenerarMiniaturasPdf extends Command
{
    protected $signature = 'pdf:miniaturas {--force : Regenerar aunque la miniatura ya exista}';

    protected $description = 'Pre-genera las miniaturas de imágenes principales usadas en los PDF de cotización';

    public function handle(): int
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        $force = (bool) $this->option('force');
        $thumbDir = public_path('imagenes/thumbs/pdf');

        $query = ImagenProducto::where('es_principal', true);
        $total = $query->count();

        if ($total === 0) {
            $this->info('No hay imágenes principales que procesar.');
            return self::SUCCESS;
        }

        $this->info("Imágenes principales a procesar: {$total}" . ($force ? ' (--force)' : ''));
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $ok = 0; $sinArchivo = 0; $fallo = 0;

        $query->chunkById(100, function ($imgs) use (&$ok, &$sinArchivo, &$fallo, $bar, $force, $thumbDir) {
            foreach ($imgs as $img) {
                $orig = public_path($img->ruta_imagen);

                if (!is_file($orig)) {
                    $sinArchivo++;
                    $bar->advance();
                    continue;
                }

                if ($force) {
                    @unlink($thumbDir . DIRECTORY_SEPARATOR . $img->id . '.jpg');
                }

                $thumb = $img->rutaImagenPdf();

                if (is_file($thumb) && strpos($thumb, 'thumbs') !== false) {
                    $ok++;
                } else {
                    $fallo++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Miniaturas generadas/cacheadas: {$ok}");
        if ($sinArchivo) {
            $this->warn("Imágenes sin archivo en disco (se omiten): {$sinArchivo}");
        }
        if ($fallo) {
            $this->error("No se pudieron generar: {$fallo} (revisar permisos de escritura en public/imagenes/thumbs/pdf)");
        }

        return self::SUCCESS;
    }
}
