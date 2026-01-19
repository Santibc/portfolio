<?php

namespace App\Helpers;

/**
 * Clase para convertir números a texto en español
 * Útil para facturas y documentos legales
 */
class NumeroALetras
{
    private static $unidades = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete',
        'dieciocho', 'diecinueve', 'veinte', 'veintiuno', 'veintidós', 'veintitrés',
        'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 'veintinueve'
    ];

    private static $decenas = [
        '', '', '', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'
    ];

    private static $centenas = [
        '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos',
        'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    ];

    /**
     * Convierte un número a su representación en letras
     *
     * @param float $numero El número a convertir
     * @param string $moneda La moneda (pesos, dólares, etc.)
     * @param bool $incluirCentavos Si incluir los centavos en texto
     * @return string
     */
    public static function convertir(float $numero, string $moneda = 'pesos', bool $incluirCentavos = true): string
    {
        $numero = round($numero, 2);
        $entero = (int) floor($numero);
        $decimales = (int) round(($numero - $entero) * 100);

        $resultado = self::convertirEntero($entero);

        // Capitalizar primera letra
        $resultado = ucfirst($resultado);

        // Agregar moneda
        $resultado .= ' ' . $moneda;

        // Agregar centavos
        if ($incluirCentavos && $decimales > 0) {
            $resultado .= ' con ' . self::convertirEntero($decimales) . ' centavos';
        } elseif ($incluirCentavos) {
            $resultado .= ' m/cte';
        }

        return $resultado;
    }

    /**
     * Convierte solo la parte entera
     */
    private static function convertirEntero(int $numero): string
    {
        if ($numero === 0) {
            return 'cero';
        }

        if ($numero < 0) {
            return 'menos ' . self::convertirEntero(abs($numero));
        }

        $texto = '';

        // Miles de millón
        if ($numero >= 1000000000) {
            $milMillones = (int) floor($numero / 1000000000);
            if ($milMillones === 1) {
                $texto .= 'mil ';
            } else {
                $texto .= self::convertirEntero($milMillones) . ' mil ';
            }
            $numero %= 1000000000;
        }

        // Millones
        if ($numero >= 1000000) {
            $millones = (int) floor($numero / 1000000);
            if ($millones === 1) {
                $texto .= 'un millón ';
            } else {
                $texto .= self::convertirEntero($millones) . ' millones ';
            }
            $numero %= 1000000;
        }

        // Miles
        if ($numero >= 1000) {
            $miles = (int) floor($numero / 1000);
            if ($miles === 1) {
                $texto .= 'mil ';
            } else {
                $texto .= self::convertirEntero($miles) . ' mil ';
            }
            $numero %= 1000;
        }

        // Centenas
        if ($numero >= 100) {
            $centena = (int) floor($numero / 100);
            if ($numero === 100) {
                $texto .= 'cien ';
            } else {
                $texto .= self::$centenas[$centena] . ' ';
            }
            $numero %= 100;
        }

        // Decenas y unidades
        if ($numero > 0) {
            if ($numero < 30) {
                $texto .= self::$unidades[$numero];
            } else {
                $decena = (int) floor($numero / 10);
                $unidad = $numero % 10;
                $texto .= self::$decenas[$decena];
                if ($unidad > 0) {
                    $texto .= ' y ' . self::$unidades[$unidad];
                }
            }
        }

        return trim($texto);
    }

    /**
     * Formato corto: "Seiscientos treinta y ocho mil seiscientos pesos m/cte"
     */
    public static function formatoFactura(float $numero): string
    {
        return self::convertir($numero, 'pesos', true);
    }
}
