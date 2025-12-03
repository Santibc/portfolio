<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracionSistema;

class SystemSettingsSeeder extends Seeder
{
    public function run()
    {
        $configuraciones = [
            // Comisiones
            [
                'clave' => 'comision_retiro',
                'nombre' => 'Comisión por Retiro',
                'valor' => '2.5',
                'tipo' => 'decimal',
                'grupo' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión aplicado a los retiros',
                'editable' => true
            ],
            [
                'clave' => 'comision_trading',
                'nombre' => 'Comisión por Trading',
                'valor' => '3.0',
                'tipo' => 'decimal',
                'grupo' => 'comisiones',
                'descripcion' => 'Porcentaje de comisión en transacciones de trading',
                'editable' => true
            ],
            [
                'clave' => 'comision_plataforma',
                'nombre' => 'Comisión General de Plataforma',
                'valor' => '1.5',
                'tipo' => 'decimal',
                'grupo' => 'comisiones',
                'descripcion' => 'Comisión general que cobra la plataforma',
                'editable' => true
            ],

            // Límites
            [
                'clave' => 'monto_minimo_inversion',
                'nombre' => 'Monto Mínimo de Inversión',
                'valor' => '100000',
                'tipo' => 'numero',
                'grupo' => 'limites',
                'descripcion' => 'Monto mínimo en COP para realizar una inversión',
                'editable' => true
            ],
            [
                'clave' => 'monto_maximo_retiro_diario',
                'nombre' => 'Monto Máximo de Retiro Diario',
                'valor' => '10000000',
                'tipo' => 'numero',
                'grupo' => 'limites',
                'descripcion' => 'Monto máximo en COP que se puede retirar por día',
                'editable' => true
            ],
            [
                'clave' => 'monto_minimo_retiro',
                'nombre' => 'Monto Mínimo de Retiro',
                'valor' => '50000',
                'tipo' => 'numero',
                'grupo' => 'limites',
                'descripcion' => 'Monto mínimo en COP para solicitar un retiro',
                'editable' => true
            ],

            // KYC
            [
                'clave' => 'kyc_obligatorio',
                'nombre' => 'KYC Obligatorio',
                'valor' => 'true',
                'tipo' => 'booleano',
                'grupo' => 'kyc',
                'descripcion' => 'Indica si el proceso KYC es obligatorio para invertir',
                'editable' => true
            ],
            [
                'clave' => 'kyc_monto_limite',
                'nombre' => 'Monto Límite sin KYC',
                'valor' => '1000000',
                'tipo' => 'numero',
                'grupo' => 'kyc',
                'descripcion' => 'Monto máximo de inversión sin completar KYC (en COP)',
                'editable' => true
            ],

            // Email
            [
                'clave' => 'email_soporte',
                'nombre' => 'Email de Soporte',
                'valor' => 'soporte@agromarket.com',
                'tipo' => 'texto',
                'grupo' => 'general',
                'descripcion' => 'Email de contacto para soporte',
                'editable' => true
            ],
            [
                'clave' => 'email_notificaciones',
                'nombre' => 'Email de Notificaciones',
                'valor' => 'notificaciones@agromarket.com',
                'tipo' => 'texto',
                'grupo' => 'general',
                'descripcion' => 'Email desde el cual se envían las notificaciones',
                'editable' => true
            ],

            // Plataforma
            [
                'clave' => 'nombre_plataforma',
                'nombre' => 'Nombre de la Plataforma',
                'valor' => 'AGROMARKET',
                'tipo' => 'texto',
                'grupo' => 'general',
                'descripcion' => 'Nombre oficial de la plataforma',
                'editable' => false
            ],
            [
                'clave' => 'moneda',
                'nombre' => 'Moneda',
                'valor' => 'COP',
                'tipo' => 'texto',
                'grupo' => 'general',
                'descripcion' => 'Moneda utilizada en la plataforma',
                'editable' => false
            ],

            // Dividendos
            [
                'clave' => 'frecuencia_calculo_dividendos',
                'nombre' => 'Frecuencia de Cálculo de Dividendos',
                'valor' => 'mensual',
                'tipo' => 'texto',
                'grupo' => 'dividendos',
                'descripcion' => 'Frecuencia con la que se calculan los dividendos',
                'editable' => true
            ]
        ];

        foreach ($configuraciones as $config) {
            ConfiguracionSistema::create($config);
        }
    }
}
