<?php

namespace Database\Seeders;

use App\Models\PlantillaFactura;
use Illuminate\Database\Seeder;

class PlantillaFacturaSeeder extends Seeder
{
    public function run(): void
    {
        $plantillas = [
            [
                'nombre' => 'Plantilla genérica',
                'descripcion' => 'Factura estándar para cualquier cliente. Marcada como predeterminada.',
                'es_default' => true,
                'html_content' => PlantillaFactura::HTML_BASE,
                'css_content' => PlantillaFactura::CSS_BASE,
            ],
            [
                'nombre' => 'Mytheresa (EUR, internacional)',
                'descripcion' => 'Plantilla para clientes tipo Mytheresa, con separación SOLD TO / SHIP TO, EUR, taxes/insurance/freight separados y bloque bancario.',
                'es_default' => false,
                'html_content' => $this->htmlMytheresa(),
                'css_content' => PlantillaFactura::CSS_BASE,
            ],
            [
                'nombre' => 'Consumidor final internacional (USD + TRM)',
                'descripcion' => 'Factura para consumidor final fuera de Colombia con tasa representativa y total COP.',
                'es_default' => false,
                'html_content' => $this->htmlConsumidorFinal(),
                'css_content' => PlantillaFactura::CSS_BASE,
            ],
        ];

        foreach ($plantillas as $p) {
            PlantillaFactura::updateOrCreate(['nombre' => $p['nombre']], $p);
        }
    }

    private function htmlMytheresa(): string
    {
        return <<<'HTML'
<div class="factura">
    <table class="header">
        <tr>
            <td>
                <h1>{{empresa.razon_social}}</h1>
                <p>NIT {{empresa.nit}} · {{empresa.direccion}}</p>
                <p>Phone: {{empresa.telefono}} · {{empresa.sitio_web}}</p>
            </td>
            <td class="right">
                <h2>INVOICE: {{factura.numero}}</h2>
                <p>DATE: {{factura.fecha}}</p>
                <p>EXPIRES: {{factura.vencimiento}}</p>
            </td>
        </tr>
    </table>

    <p class="cufe">CUFE: {{factura.cufe}}</p>

    <table class="grid-2">
        <tr>
            <td>
                <div class="section">
                    <p><strong>SOLD TO</strong><br>{{cliente.nombre}}</p>
                    <p><strong>ADDRESS</strong><br>{{cliente.direccion_facturacion}}</p>
                    <p><strong>MAIL CONTACT</strong>: {{cliente.email}}</p>
                    <p><strong>PHONE</strong>: {{cliente.telefono}}</p>
                </div>
            </td>
            <td>
                <div class="section">
                    <p><strong>SHIP TO</strong><br>{{cliente.direccion_envio}}</p>
                    <p><strong>INCOTERMS</strong>: {{cliente.incoterm}}</p>
                    <p><strong>SHIPPING PORT</strong>: {{cliente.puerto}}</p>
                    <p><strong>CURRENCY</strong>: {{factura.moneda}}</p>
                    <p><strong>ORIGIN</strong>: COLOMBIA</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>REFERENCE</th>
                <th>DESCRIPTION</th>
                <th>COLOR</th>
                <th>QTY</th>
                <th>UNIT PRICE</th>
                <th>TOTAL AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            <tr data-loop="items">
                <td>{{referencia}}</td>
                <td>{{descripcion}}</td>
                <td>{{color}}</td>
                <td>{{cantidad}}</td>
                <td>{{factura.simbolo}}{{precio_unitario}}</td>
                <td>{{factura.simbolo}}{{total}}</td>
            </tr>
        </tbody>
    </table>

    <table class="totales">
        <tr><td>SUBTOTAL</td><td class="right">{{factura.simbolo}}{{totales.subtotal}}</td></tr>
        <tr><td>TAXES</td><td class="right">{{factura.simbolo}}{{totales.iva}}</td></tr>
        <tr><td>INSURANCE</td><td class="right">{{factura.simbolo}}{{totales.seguro}}</td></tr>
        <tr><td>FREIGHT</td><td class="right">{{factura.simbolo}}{{totales.flete}}</td></tr>
        <tr><td>DISCOUNT</td><td class="right">{{factura.simbolo}}{{totales.descuento}}</td></tr>
        <tr class="total-final"><td>TOTAL</td><td class="right">{{factura.simbolo}}{{totales.total}}</td></tr>
    </table>

    <div class="banco">
        <strong>Beneficiary Bank Name:</strong> {{banco.nombre}}<br>
        <strong>Beneficiary Bank Country:</strong> {{banco.pais}}<br>
        <strong>Beneficiary Bank Address:</strong> {{banco.direccion}}<br>
        <strong>Beneficiary Bank Account Name:</strong> {{banco.titular}}<br>
        <strong>Beneficiary Bank Account Currency:</strong> {{banco.moneda}}<br>
        <strong>Beneficiary Bank SWIFT/BIC:</strong> {{banco.swift}}<br>
        <strong>Beneficiary Bank Account Number:</strong> {{banco.numero_cuenta}}<br><br>
        <strong>Finance Contact Name:</strong> {{contacto.nombre}}<br>
        <strong>Finance Contact Email:</strong> {{contacto.email}}<br>
        <strong>Finance Contact Phone:</strong> {{contacto.telefono}}
    </div>
</div>
HTML;
    }

    private function htmlConsumidorFinal(): string
    {
        return <<<'HTML'
<style>
/* Estilos específicos para la plantilla Siigo-style (no afectan otras plantillas) */
.siigo { font-family: Arial, sans-serif; font-size: 9px; color: #000; }
.siigo .header-top { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
.siigo .header-top td { vertical-align: top; padding: 0; border: 0; }
.siigo .col-qr { width: 15%; }
.siigo .col-logo { width: 50%; text-align: center; }
.siigo .col-logo img { max-width: 140px; height: auto; }
.siigo .col-invoice { width: 35%; text-align: right; font-size: 10px; }
.siigo .col-invoice h2 { margin: 0 0 6px 0; font-size: 13px; font-weight: bold; }
.siigo .invoice-box { border: 1px solid #000; width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 9px; }
.siigo .invoice-box td { border: 1px solid #000; padding: 3px 6px; text-align: left; }
.siigo .invoice-box td:first-child { font-weight: normal; width: 45%; }
.siigo .invoice-box td:last-child { text-align: right; }
.siigo .empresa-info { text-align: center; font-size: 10px; margin: 6px 0; line-height: 1.35; }
.siigo .empresa-info strong { font-size: 11px; }
.siigo .legal { font-size: 7.5px; color: #000; margin: 4px 0; text-align: center; line-height: 1.3; position: relative; padding-right: 30px; }
.siigo .legal .version { position: absolute; right: 0; top: 50%; transform: translateY(-50%); font-size: 8px; font-weight: bold; }
.siigo .cufe { font-size: 7.5px; color: #000; margin: 8px 0; word-break: break-all; }
.siigo .cajas { width: 100%; border-collapse: collapse; margin: 6px 0; }
.siigo .cajas > tbody > tr > td { vertical-align: top; padding: 0; width: 50%; border: 0; }
.siigo .cajas .caja { border: 1px solid #000; padding: 6px 8px; font-size: 9px; }
.siigo .cajas .caja table { width: 100%; border-collapse: collapse; }
.siigo .cajas .caja table td { padding: 2px 4px; border: 0; vertical-align: top; }
.siigo .cajas .caja table td.label { width: 40%; font-weight: normal; }
.siigo .cajas td:first-child .caja { margin-right: 4px; }
.siigo .cajas td:last-child .caja { margin-left: 4px; }
.siigo table.items-siigo { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; }
.siigo table.items-siigo th, .siigo table.items-siigo td { border: 1px solid #000; padding: 5px 6px; text-align: center; }
.siigo table.items-siigo th { background: #fff; font-weight: normal; }
.siigo .footer-siigo { width: 100%; border-collapse: collapse; margin-top: 0; }
.siigo .footer-siigo > tbody > tr > td { vertical-align: top; padding: 0; border: 0; }
.siigo .obs-col { width: 60%; border: 1px solid #000; border-top: 0; padding: 8px; }
.siigo .obs-col .obs-title { font-weight: bold; margin-bottom: 20px; }
.siigo .obs-col .trm { margin-top: 20px; }
.siigo .obs-col .trm strong { display: block; }
.siigo .totales-col { width: 40%; }
.siigo table.totales-siigo { width: 100%; border-collapse: collapse; font-size: 9px; }
.siigo table.totales-siigo td { border: 1px solid #000; padding: 5px 8px; }
.siigo table.totales-siigo td.label { text-align: center; font-weight: bold; }
.siigo table.totales-siigo td.valor { text-align: right; }
.siigo table.totales-siigo tr.final td { font-weight: bold; }
.siigo .qr img { max-width: 100px; height: auto; }
.siigo .qr-placeholder { width: 100px; height: 100px; border: 1px dashed #999; font-size: 7px; color: #999; text-align: center; padding: 40px 4px; box-sizing: border-box; }
</style>

<div class="factura siigo">
    <table class="header-top">
        <tr>
            <td class="col-qr">
                <div class="qr">
                    {{factura.qr_html}}
                </div>
            </td>
            <td class="col-logo">
                <img src="{{empresa.logo}}" alt="Logo">
            </td>
            <td class="col-invoice">
                <h2>COMMERCIAL INVOICE</h2>
                <table class="invoice-box">
                    <tr><td>INVOICE:</td><td>{{factura.numero}}</td></tr>
                    <tr><td>DATE</td><td>{{factura.fecha}}</td></tr>
                    <tr><td>EXPIRES</td><td>{{factura.vencimiento}}</td></tr>
                    <tr><td>PO#</td><td>{{factura.po}}</td></tr>
                    <tr><td>AWB</td><td>{{factura.awb}}</td></tr>
                    <tr><td>SHIPPER</td><td>{{factura.shipper}}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="empresa-info">
        <strong>{{empresa.razon_social}}. NIT {{empresa.nit}}</strong><br>
        Address: {{empresa.direccion}}<br>
        Phone: {{empresa.telefono}}<br>
        {{empresa.sitio_web}}
    </div>

    <div class="legal">
        {{empresa.regimen}}<br>
        {{empresa.resolucion_clc}}<br>
        {{empresa.resolucion_fv}}
        <span class="version">{{factura.version}}</span>
    </div>

    <p class="cufe">CUFE: {{factura.cufe}}</p>

    <table class="cajas">
        <tr>
            <td>
                <div class="caja">
                    <table>
                        <tr><td class="label">SOLD TO</td><td>{{cliente.nombre}}</td></tr>
                        <tr><td class="label">SHIP TO</td><td>{{cliente.direccion_envio}}</td></tr>
                        <tr><td class="label">ID</td><td>{{cliente.identificacion}}</td></tr>
                        <tr><td class="label">MAIL CONTACT</td><td>{{cliente.email}}</td></tr>
                        <tr><td class="label">PHONE</td><td>{{cliente.telefono}}</td></tr>
                        <tr><td class="label">ADDRESS</td><td>{{cliente.direccion_facturacion}}</td></tr>
                        <tr><td class="label">INCOTERMS</td><td>{{cliente.incoterm}}</td></tr>
                        <tr><td class="label">SHIPPING PORT</td><td>{{cliente.puerto}}</td></tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="caja">
                    <table>
                        <tr><td class="label">ORIGIN</td><td>{{cliente.origen}}</td><td class="label">Cod</td><td>{{factura.cod}}</td></tr>
                        <tr><td class="label">CURRENCY</td><td>{{factura.moneda}}</td><td></td><td></td></tr>
                        <tr><td class="label">DESTINATION</td><td>{{cliente.destino}}</td><td></td><td></td></tr>
                        <tr><td class="label">PAYMENT TERMS</td><td colspan="3">{{factura.payment_terms}}</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-siigo">
        <thead>
            <tr>
                <th>REFERENCE</th>
                <th>DESCRIPTION</th>
                <th>COLOR</th>
                <th>SIZE</th>
                <th>COMPOSITION</th>
                <th>QTY</th>
                <th>#PA</th>
                <th>COUNTRY OF ORIGIN</th>
                <th>UNIT PRICE</th>
                <th>TOTAL AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            <tr data-loop="items">
                <td>{{referencia}}</td>
                <td>{{descripcion}}</td>
                <td>{{color}}</td>
                <td>{{size}}</td>
                <td>{{composition}}</td>
                <td>{{cantidad}}</td>
                <td>{{codigo_pa}}</td>
                <td>{{pais_origen}}</td>
                <td>${{precio_unitario}}</td>
                <td>${{total}}</td>
            </tr>
        </tbody>
    </table>

    <table class="footer-siigo">
        <tr>
            <td class="obs-col">
                <div class="obs-title">OBSERVATIONS:</div>
                <div>{{factura.observaciones}}</div>
                <div class="trm">
                    <strong>Tasa Representativa</strong>
                    {{factura.tasa_cambio}}
                    <br><br>
                    <strong>Total COP</strong>
                    ${{totales.total_cop}}
                </div>
            </td>
            <td class="totales-col">
                <table class="totales-siigo">
                    <tr><td class="label">SUBTOTAL</td><td class="valor">${{totales.subtotal}}</td></tr>
                    <tr><td class="label">TAXES</td><td class="valor">${{totales.iva}}</td></tr>
                    <tr><td class="label">FREIGHT</td><td class="valor">${{totales.flete}}</td></tr>
                    <tr><td class="label">INSURANCE</td><td class="valor">${{totales.seguro}}</td></tr>
                    <tr><td class="label">DISCOUNT</td><td class="valor">${{totales.descuento}}</td></tr>
                    <tr class="final"><td class="label">TOTAL</td><td class="valor">${{totales.total}}</td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>
HTML;
    }
}
