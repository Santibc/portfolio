<?php

namespace App\Services\Facturacion;

use App\Models\Factura;
use App\Models\SiigoConfig;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Generador de códigos QR para facturas electrónicas DIAN.
 *
 * Formato del contenido del QR: Anexo Técnico Factura Electrónica de Venta
 * versión 1.9 (DIAN, Colombia). Campos obligatorios según especificación oficial:
 * NumFac, FecFac, HorFac, NitFac, DocAdq, ValFac, ValIva, ValOtroIm, ValTolFac, CUFE
 * + URL de consulta en el catálogo de la DIAN.
 *
 * @see https://www.dian.gov.co/impuestos/factura-electronica/Documents/Anexo-Tecnico-Factura-Electronica-de-Venta-vr-1-9.pdf
 */
class QrGeneratorService
{
    /**
     * URL oficial de consulta de factura electrónica en el catálogo VPFE de la DIAN.
     * Se incluye dentro del QR y apunta a una página pública donde cualquiera puede
     * verificar la validez del documento con el CUFE.
     */
    private const URL_CONSULTA_DIAN = 'https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey=';

    /**
     * Genera el HTML del QR (tag `<img>` con data URI base64) a partir de una factura ya timbrada.
     * Requiere que la factura tenga CUFE. Si no lo tiene, retorna string vacío.
     */
    public function generarParaFactura(Factura $factura, int $tamano = 110): string
    {
        if (empty($factura->cufe)) {
            return '';
        }

        $contenido = $this->construirContenidoQR($factura);
        $base64 = $this->renderizarComoBase64($contenido);

        if ($base64 === '') {
            return '';
        }

        return sprintf(
            '<img src="%s" width="%d" height="%d" alt="QR DIAN">',
            $base64,
            $tamano,
            $tamano
        );
    }

    /**
     * Genera solo la URL de consulta DIAN (útil para guardar en la BD por separado).
     */
    public function urlConsultaDian(string $cufe): string
    {
        return self::URL_CONSULTA_DIAN.$cufe;
    }

    /**
     * Construye el contenido textual del QR siguiendo el formato del Anexo Técnico 1.9.
     * Cada campo en una línea separada con el patrón "Label: valor".
     */
    private function construirContenidoQR(Factura $factura): string
    {
        $numero = (string) ($factura->numero_siigo ?? $factura->numero_interno);
        $fecha = $factura->fecha->format('Y-m-d');
        $horaFuente = $factura->emitida_at ?? $factura->created_at ?? now();
        $hora = $horaFuente->format('H:i:sP');
        $nitEmisor = $this->nitEmisor();
        $docCliente = (string) ($factura->cliente?->identificacion ?? '');
        $subtotal = $this->formatearMonto($factura->subtotal);
        $iva = $this->formatearMonto($factura->iva_total);
        $otrosImpuestos = $this->formatearMonto($factura->flete + $factura->seguro);
        $total = $this->formatearMonto($factura->total);
        $cufe = (string) $factura->cufe;

        return implode("\n", [
            'NumFac: '.$numero,
            'FecFac: '.$fecha,
            'HorFac: '.$hora,
            'NitFac: '.$nitEmisor,
            'DocAdq: '.$docCliente,
            'ValFac: '.$subtotal,
            'ValIva: '.$iva,
            'ValOtroIm: '.$otrosImpuestos,
            'ValTolFac: '.$total,
            'CUFE: '.$cufe,
            self::URL_CONSULTA_DIAN.$cufe,
        ]);
    }

    /**
     * Genera un PNG del QR con chillerlan y lo devuelve como data URI base64.
     */
    private function renderizarComoBase64(string $contenido): string
    {
        try {
            $options = new QROptions([
                'outputInterface' => QRGdImagePNG::class,
                'outputBase64' => true,
                'eccLevel' => EccLevel::M,
                'scale' => 4,
                'quietzoneSize' => 2,
            ]);

            return (string) (new QRCode($options))->render($contenido);
        } catch (\Throwable $e) {
            report($e);

            return '';
        }
    }

    /**
     * Formatea un monto decimal como string con 2 decimales y punto como separador
     * (formato exigido por la DIAN en el contenido del QR: "1500000.00").
     *
     * @param  mixed  $valor
     */
    private function formatearMonto($valor): string
    {
        return number_format((float) ($valor ?? 0), 2, '.', '');
    }

    /**
     * NIT del emisor sin guion ni dígito de verificación, como exige la DIAN en el QR.
     * Se lee de la configuración de Siigo (tabla siigo_config).
     */
    private function nitEmisor(): string
    {
        $nit = (string) (SiigoConfig::current()->nit_emisor ?? '901249576-9');

        $posGuion = strpos($nit, '-');
        $soloDigitos = $posGuion !== false ? substr($nit, 0, $posGuion) : $nit;

        return (string) preg_replace('/[^0-9]/', '', $soloDigitos);
    }
}
