<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaFactura extends Model
{
    use HasFactory;

    protected $table = 'plantillas_factura';

    protected $fillable = [
        'nombre',
        'descripcion',
        'html_content',
        'css_content',
        'es_default',
        'activo',
    ];

    protected $casts = [
        'es_default' => 'bool',
        'activo' => 'bool',
    ];

    /**
     * CSS base para cualquier plantilla nueva. Compatible con DomPDF:
     * usa `<table>` en vez de flex/grid para el layout de 2 columnas
     * (DomPDF no soporta `display: flex` ni `display: grid`).
     */
    public const CSS_BASE = <<<'CSS'
body { font-family: Arial, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }
.factura { max-width: 900px; margin: 0 auto; padding: 24px; background: #ffffff; }

/* Header: tabla invisible con 2 celdas — empresa a la izquierda, factura a la derecha */
table.header { width: 100%; border-collapse: collapse; border-bottom: 2px solid #f97316; margin-bottom: 20px; }
table.header td { vertical-align: top; padding: 0 0 12px 0; border: 0; }
table.header td.right { text-align: right; }
table.header h1 { color: #f97316; font-size: 22px; margin: 0 0 4px 0; }
table.header h2 { font-size: 16px; margin: 0; color: #111827; }
table.header p { margin: 2px 0; font-size: 11px; color: #4b5563; }

/* Sección con título resaltado */
.section { margin-bottom: 16px; }
.section-title { background: #fef3c7; padding: 6px 10px; font-weight: bold; border-left: 3px solid #f97316; margin-bottom: 6px; }

/* Layout de 2 columnas (cliente / detalles) con tabla */
table.grid-2 { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
table.grid-2 td { vertical-align: top; padding: 0 8px; border: 0; width: 50%; }
table.grid-2 td:first-child { padding-left: 0; }
table.grid-2 td:last-child { padding-right: 0; }

/* Tabla de items (productos) */
table.items { width: 100%; border-collapse: collapse; margin-top: 8px; }
table.items th, table.items td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; font-size: 10px; }
table.items th { background: #f3f4f6; font-weight: bold; color: #374151; }

/* Totales alineados a la derecha */
table.totales { margin-top: 12px; margin-left: auto; width: 280px; border-collapse: collapse; }
table.totales td { padding: 4px 10px; font-size: 11px; border-bottom: 1px solid #e5e7eb; }
table.totales .total-final td { font-weight: bold; background: #fff7ed; color: #9a3412; }

.right { text-align: right; }
.banco { background: #fff7ed; padding: 12px; border: 1px dashed #fb923c; font-size: 10px; margin-top: 20px; line-height: 1.6; }
.banco strong { color: #9a3412; }
.cufe { font-size: 8px; color: #6b7280; margin: 6px 0 12px 0; word-break: break-all; }
.observaciones { margin-top: 24px; }
CSS;

    /**
     * HTML inicial para una plantilla nueva — factura lista para editar con
     * todos los bloques típicos (header, cliente, detalles, items, totales, observaciones).
     */
    public const HTML_BASE = <<<'HTML'
<div class="factura">
    <table class="header">
        <tr>
            <td>
                <h1>{{empresa.razon_social}}</h1>
                <p>NIT {{empresa.nit}} · {{empresa.direccion}}</p>
                <p>Tel. {{empresa.telefono}} · {{empresa.email}}</p>
            </td>
            <td class="right">
                <h2>FACTURA {{factura.numero}}</h2>
                <p>Fecha: {{factura.fecha}}</p>
                <p>Vencimiento: {{factura.vencimiento}}</p>
            </td>
        </tr>
    </table>

    <table class="grid-2">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">Cliente</div>
                    <p><strong>{{cliente.nombre}}</strong></p>
                    <p>ID: {{cliente.identificacion}}</p>
                    <p>{{cliente.direccion_facturacion}}</p>
                    <p>{{cliente.email}} · {{cliente.telefono}}</p>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-title">Detalles</div>
                    <p>Moneda: {{factura.moneda}}</p>
                    <p>CUFE:</p>
                    <p class="cufe">{{factura.cufe}}</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Referencia</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr data-loop="items">
                <td>{{@index}}</td>
                <td>{{referencia}}</td>
                <td>{{descripcion}}</td>
                <td>{{cantidad}}</td>
                <td>{{precio_unitario}}</td>
                <td>{{total}}</td>
            </tr>
        </tbody>
    </table>

    <table class="totales">
        <tr><td>Subtotal</td><td class="right">{{totales.subtotal}}</td></tr>
        <tr><td>IVA</td><td class="right">{{totales.iva}}</td></tr>
        <tr><td>Descuento</td><td class="right">{{totales.descuento}}</td></tr>
        <tr class="total-final"><td>TOTAL {{factura.moneda}}</td><td class="right">{{totales.total}}</td></tr>
    </table>

    <div class="observaciones">
        <div class="section-title">Observaciones</div>
        <p>{{factura.observaciones}}</p>
    </div>
</div>
HTML;

    /**
     * @param  Builder<PlantillaFactura>  $query
     * @return Builder<PlantillaFactura>
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * @param  Builder<PlantillaFactura>  $query
     * @return Builder<PlantillaFactura>
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('es_default', true);
    }
}
