<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Parametros;

/**
 * Siembra los datos corporativos usados en el encabezado del PDF de cotización.
 * Editables luego desde la tabla `parametros`. Idempotente: no duplica si ya existen.
 */
return new class extends Migration
{
    /**
     * @var array<string, array{0:string,1:string}>  nombre => [valor_inicial, ayuda]
     */
    // Se siembran vacíos; los valores reales se cargan desde el módulo "Empresa" (solo admin).
    private array $parametros = [
        'empresa_razon_social' => ['', 'Razón social que aparece en el encabezado del PDF.'],
        'empresa_ruc'          => ['', 'RUC / NIT / Cédula de la empresa.'],
        'empresa_direccion'    => ['', 'Dirección que aparece en el PDF.'],
        'empresa_telefonos'    => ['', 'Teléfono(s) de contacto en el PDF.'],
        'empresa_email'        => ['', 'Correo de contacto en el PDF.'],
        'empresa_sitio_web'    => ['', 'Sitio web mostrado en el PDF (opcional).'],
    ];

    public function up(): void
    {
        foreach ($this->parametros as $nombre => [$valor, $ayuda]) {
            Parametros::firstOrCreate(
                ['nombre_parametro' => $nombre],
                ['valor_parametro' => $valor, 'comentario' => $ayuda, 'estado' => true, 'reservado' => false]
            );
        }
    }

    public function down(): void
    {
        Parametros::whereIn('nombre_parametro', array_keys($this->parametros))->delete();
    }
};
