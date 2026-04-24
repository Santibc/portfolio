<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $configuraciones = [
            ['clave' => 'empresa.razon_social', 'valor' => 'CLC & CIA S.A.S.', 'tipo' => 'string', 'grupo' => 'empresa', 'descripcion' => 'Razón social de la empresa emisora'],
            ['clave' => 'empresa.nit', 'valor' => '901249576-9', 'tipo' => 'string', 'grupo' => 'empresa', 'descripcion' => 'NIT de la empresa emisora'],
            ['clave' => 'empresa.direccion', 'valor' => 'Cr 4 No. 4-43 oficina 302', 'tipo' => 'string', 'grupo' => 'empresa', 'descripcion' => 'Dirección fiscal de la empresa'],
            ['clave' => 'empresa.telefono', 'valor' => '89 36 527', 'tipo' => 'string', 'grupo' => 'empresa', 'descripcion' => 'Teléfono de la empresa'],
            ['clave' => 'empresa.email', 'valor' => 'jsrojas@caladelacruz', 'tipo' => 'string', 'grupo' => 'empresa', 'descripcion' => 'Email de contacto financiero'],
            ['clave' => 'empresa.sitio_web', 'valor' => 'www.caladelacruz.com', 'tipo' => 'string', 'grupo' => 'empresa', 'descripcion' => 'Sitio web público'],
            ['clave' => 'empresa.logo_path', 'valor' => 'uploads/empresa/logo.png', 'tipo' => 'string', 'grupo' => 'empresa', 'descripcion' => 'Ruta pública del logo (asset())'],
            ['clave' => 'empresa.regimen_tributario', 'valor' => 'IVA RÉGIMEN COMÚN – NO SOMOS RETENEDORES DE IVA – NO SOMOS GRANDES CONTRIBUYENTES', 'tipo' => 'text', 'grupo' => 'empresa', 'descripcion' => 'Leyenda de régimen tributario para PDFs'],

            ['clave' => 'dian.resolucion_texto_clc', 'valor' => 'Factura Electrónica de Venta código 4, prefijo CLC desde 1 hasta 3000 vigencia 24 tipo de solicitud autorización código 1 Resolución 18764084396801 de 2024/11/29', 'tipo' => 'text', 'grupo' => 'dian', 'descripcion' => 'Leyenda DIAN para prefijo CLC'],
            ['clave' => 'dian.resolucion_texto_fv', 'valor' => 'Factura Electrónica de Venta código 4, prefijo FV desde 1 hasta 1500 vigencia 24 tipo de solicitud autorización código 1 Resolución 18764088482186 de 2025/02/06', 'tipo' => 'text', 'grupo' => 'dian', 'descripcion' => 'Leyenda DIAN para prefijo FV'],

            ['clave' => 'banco.nombre', 'valor' => 'BANCOLOMBIA', 'tipo' => 'string', 'grupo' => 'banco', 'descripcion' => 'Banco beneficiario'],
            ['clave' => 'banco.pais', 'valor' => 'COLOMBIA', 'tipo' => 'string', 'grupo' => 'banco', 'descripcion' => 'País del banco'],
            ['clave' => 'banco.direccion', 'valor' => 'Avenida 8 Norte # 12 - 43', 'tipo' => 'string', 'grupo' => 'banco', 'descripcion' => 'Dirección del banco'],
            ['clave' => 'banco.titular', 'valor' => 'CLC Y CIA SAS', 'tipo' => 'string', 'grupo' => 'banco', 'descripcion' => 'Titular de la cuenta'],
            ['clave' => 'banco.moneda', 'valor' => 'PESOS COLOMBIANOS', 'tipo' => 'string', 'grupo' => 'banco', 'descripcion' => 'Moneda de la cuenta'],
            ['clave' => 'banco.swift', 'valor' => 'COLOCOBM, COLOCOBMXXX', 'tipo' => 'string', 'grupo' => 'banco', 'descripcion' => 'Código SWIFT/BIC'],
            ['clave' => 'banco.numero_cuenta', 'valor' => '96700000418', 'tipo' => 'string', 'grupo' => 'banco', 'descripcion' => 'Número de cuenta'],

            ['clave' => 'contacto_financiero.nombre', 'valor' => 'Juan Sebastián Rojas', 'tipo' => 'string', 'grupo' => 'contacto', 'descripcion' => 'Nombre del contacto financiero'],
            ['clave' => 'contacto_financiero.email', 'valor' => 'jsrojas@caladelacruz', 'tipo' => 'string', 'grupo' => 'contacto', 'descripcion' => 'Email del contacto financiero'],
            ['clave' => 'contacto_financiero.telefono', 'valor' => '+57 302 2285789', 'tipo' => 'string', 'grupo' => 'contacto', 'descripcion' => 'Teléfono del contacto financiero'],

            ['clave' => 'facturacion.prefijo_interno', 'valor' => 'REM', 'tipo' => 'string', 'grupo' => 'facturacion', 'descripcion' => 'Prefijo del consecutivo interno para remisiones (facturas no electrónicas)'],
            ['clave' => 'facturacion.consecutivo_interno', 'valor' => '0', 'tipo' => 'integer', 'grupo' => 'facturacion', 'descripcion' => 'Último consecutivo interno asignado'],
        ];

        foreach ($configuraciones as $config) {
            Configuracion::updateOrCreate(['clave' => $config['clave']], $config);
        }
    }
}
