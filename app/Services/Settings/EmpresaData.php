<?php

namespace App\Services\Settings;

use Illuminate\Support\Facades\File;

/**
 * Construye el bloque `empresa` que consumen las plantillas de factura
 * ({{empresa.*}}). Toda la información proviene de la base de datos
 * (tabla `configuraciones`), sin valores hardcodeados.
 */
class EmpresaData
{
    public function __construct(private readonly ConfigService $config) {}

    /**
     * Datos de la empresa emisora para el renderizado de plantillas.
     *
     * @return array<string, string>
     */
    public function paraPlantilla(): array
    {
        return [
            'razon_social' => (string) $this->config->get('empresa.razon_social', ''),
            'nit' => (string) $this->config->get('empresa.nit', ''),
            'direccion' => (string) $this->config->get('empresa.direccion', ''),
            'telefono' => (string) $this->config->get('empresa.telefono', ''),
            'email' => (string) $this->config->get('empresa.email', ''),
            'sitio_web' => (string) $this->config->get('empresa.sitio_web', ''),
            'logo' => $this->logoBase64(),
            'regimen' => (string) $this->config->get('empresa.regimen_tributario', ''),
            'resolucion_clc' => (string) $this->config->get('dian.resolucion_texto_clc', ''),
            'resolucion_fv' => (string) $this->config->get('dian.resolucion_texto_fv', ''),
        ];
    }

    /**
     * Convierte el logo configurado a data URI base64 — compatible con DomPDF
     * (sin enable_remote) y con el preview del editor. Lee la ruta guardada en
     * `empresa.logo_path`; si no existe, deja la imagen vacía.
     */
    private function logoBase64(): string
    {
        $logoPath = (string) $this->config->get('empresa.logo_path', '');

        if ($logoPath === '') {
            return '';
        }

        $absoluto = public_path($logoPath);

        if (! File::exists($absoluto)) {
            return '';
        }

        $contenido = @file_get_contents($absoluto);
        if ($contenido === false) {
            return '';
        }

        $extension = strtolower(pathinfo($absoluto, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };

        return 'data:'.$mime.';base64,'.base64_encode($contenido);
    }
}
