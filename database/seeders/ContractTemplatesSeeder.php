<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlantillaContrato;
use App\Models\CategoriaProyecto;

class ContractTemplatesSeeder extends Seeder
{
    public function run()
    {
        // Obtener todas las categorías
        $categorias = CategoriaProyecto::all();

        $tipoContratoMap = [
            'STAKING' => 'inversion_staking',
            'TRADING' => 'inversion_staking', // Trading usa el mismo contrato base
            'EAR' => 'inversion_ear',
            'FUTUROS' => 'inversion_futuros',
            'CROSS_FUND' => 'inversion_cross_fund'
        ];

        foreach ($categorias as $categoria) {
            $contenidoBase = $this->getContenidoContrato($categoria->codigo);
            $tipoContrato = $tipoContratoMap[$categoria->codigo] ?? 'inversion_staking';

            PlantillaContrato::create([
                'codigo' => 'CONT_' . $categoria->codigo . '_V1',
                'nombre' => 'Contrato de Inversión ' . $categoria->nombre,
                'contenido' => $contenidoBase,
                'tipo_contrato' => $tipoContrato,
                'version' => '1.0',
                'activo' => true,
                'fecha_vigencia' => now(),
                'variables_requeridas' => json_encode([
                    '{{NOMBRE_INVERSIONISTA}}',
                    '{{DOCUMENTO_INVERSIONISTA}}',
                    '{{CODIGO_INVERSION}}',
                    '{{MONTO_INVERSION}}',
                    '{{ROI_ANUAL}}',
                    '{{FECHA_INICIO}}',
                    '{{FECHA_VENCIMIENTO}}',
                    '{{NOMBRE_PROYECTO}}',
                    '{{CODIGO_PROYECTO}}',
                    '{{FECHA_FIRMA}}'
                ])
            ]);
        }
    }

    private function getContenidoContrato($codigoCategoria)
    {
        $contenidoComun = "
        CONTRATO DE INVERSIÓN AGRÍCOLA - AGROMARKET

        Entre los suscritos a saber: AGROMARKET, sociedad legalmente constituida e identificada con NIT XXX,
        representada legalmente por su Gerente, quien en adelante se denominará LA PLATAFORMA, de una parte,
        y de la otra {{NOMBRE_INVERSIONISTA}}, identificado con documento {{DOCUMENTO_INVERSIONISTA}},
        quien en adelante se denominará EL INVERSIONISTA, hemos acordado celebrar el presente contrato de
        inversión agrícola, que se regirá por las siguientes cláusulas:

        PRIMERA - OBJETO: El objeto del presente contrato es la inversión del INVERSIONISTA en el proyecto
        agrícola identificado como {{CODIGO_PROYECTO}} - {{NOMBRE_PROYECTO}}, a través de la plataforma AGROMARKET.

        SEGUNDA - MONTO DE LA INVERSIÓN: El INVERSIONISTA realizará una inversión por un monto total de
        {{MONTO_INVERSION}} COP (Pesos Colombianos), identificada con el código {{CODIGO_INVERSION}}.

        TERCERA - RENTABILIDAD: LA PLATAFORMA se compromete a generar una rentabilidad anual proyectada
        del {{ROI_ANUAL}}% sobre el capital invertido.

        CUARTA - PLAZO: El presente contrato tendrá una vigencia desde el {{FECHA_INICIO}} hasta el
        {{FECHA_VENCIMIENTO}}.
        ";

        $contenidoEspecifico = match($codigoCategoria) {
            'STAKING' => "
        QUINTA - CONDICIONES ESPECIALES DE STAKING:
        - El capital invertido permanecerá bloqueado durante todo el período del contrato.
        - No se permite retiro anticipado bajo ninguna circunstancia.
        - Los rendimientos se pagarán según la frecuencia establecida en el proyecto.
        - El capital e intereses finales serán devueltos al vencimiento del contrato.
            ",
            'TRADING' => "
        QUINTA - CONDICIONES ESPECIALES DE TRADING:
        - El INVERSIONISTA podrá vender su posición en el mercado secundario en cualquier momento.
        - Las transacciones de compra-venta estarán sujetas a una comisión del 3%.
        - El precio de venta será determinado por el mercado y puede variar del valor nominal.
        - No aplican penalizaciones por venta anticipada.
            ",
            'EAR' => "
        QUINTA - CONDICIONES ESPECIALES DE RETIRO ANTICIPADO (EAR):
        - El INVERSIONISTA podrá solicitar retiro anticipado aplicando las penalizaciones vigentes.
        - Retiro antes de 90 días: Penalización del 50% sobre rendimientos.
        - Retiro entre 91-180 días: Penalización del 30% sobre rendimientos.
        - Retiro después de 180 días: Penalización del 10% sobre rendimientos.
        - El capital principal siempre será devuelto en su totalidad.
            ",
            'FUTUROS' => "
        QUINTA - CONDICIONES ESPECIALES DE CONTRATOS A FUTURO:
        - La inversión está ligada al ciclo agrícola del proyecto específico.
        - El retorno se realizará al finalizar la cosecha y comercialización.
        - Posibilidad de venta en mercado secundario después de 60 días.
        - Los riesgos agrícolas están cubiertos por pólizas de la plataforma.
            ",
            'CROSS_FUND' => "
        QUINTA - CONDICIONES ESPECIALES DE FONDO DIVERSIFICADO (CROSS FUND):
        - La inversión se distribuye automáticamente en múltiples proyectos.
        - Retiro anticipado permitido con penalización del 15% sobre rendimientos.
        - Mayor estabilidad por diversificación del riesgo.
        - Rendimientos calculados como promedio ponderado de todos los proyectos del fondo.
            ",
            default => ""
        };

        $contenidoFinal = "
        SEXTA - DECLARACIONES: EL INVERSIONISTA declara:
        1. Conocer y aceptar los términos y condiciones de la plataforma AGROMARKET.
        2. Entender los riesgos asociados a la inversión agrícola.
        3. Haber completado satisfactoriamente el proceso KYC (Conozca a su Cliente).
        4. Que los recursos invertidos provienen de fuentes lícitas.

        SÉPTIMA - ACEPTACIÓN DIGITAL: El presente contrato se firma digitalmente el día {{FECHA_FIRMA}}
        mediante aceptación electrónica en la plataforma AGROMARKET.

        Acepto los términos y condiciones del presente contrato.

        _______________________________
        {{NOMBRE_INVERSIONISTA}}
        {{DOCUMENTO_INVERSIONISTA}}
        Firma Digital
        ";

        return $contenidoComun . $contenidoEspecifico . $contenidoFinal;
    }
}
