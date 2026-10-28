<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LandingAjuste;
use Illuminate\Support\Facades\File;

/**
 * Genera el instalador (.bat) que deja un equipo Windows listo como caja: impresora de recibos
 * como predeterminada y un acceso directo que abre Chrome con impresión directa
 * (--kiosk-printing), también al prender el equipo. Plantilla: resources/pos/instalar-caja.bat.
 */
class InstaladorCajaService
{
    private const PLANTILLA = 'pos/instalar-caja.bat';

    public function generar(string $urlInicio, string $urlIcono): string
    {
        $script = strtr(File::get(resource_path(self::PLANTILLA)), [
            '__URL_CAJA__'      => $this->literal($urlInicio),
            '__URL_ICONO__'     => $this->literal($urlIcono),
            '__NOMBRE_ACCESO__' => $this->literal($this->nombreAcceso()),
        ]);

        // cmd.exe necesita CRLF, sin importar cómo haya quedado la plantilla en git.
        return str_replace(["\r\n", "\n"], ["\n", "\r\n"], $script);
    }

    /** Nombre del acceso directo (es un archivo .lnk: sin los caracteres que Windows no admite). */
    public function nombreAcceso(): string
    {
        $negocio = LandingAjuste::query()->value('nombre') ?: config('app.name');

        $nombre = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', 'Caja '.$negocio);

        return trim((string) preg_replace('/\s+/', ' ', (string) $nombre));
    }

    /** El valor va dentro de un string de PowerShell entre comillas simples. */
    private function literal(string $valor): string
    {
        return str_replace("'", "''", $valor);
    }
}
