<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Servicio {{ $orden->numero_orden }}</title>
    <style>
        @page { margin: 18mm 14mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #111; }

        /* ====== HEADER ====== */
        .header { width: 100%; border-bottom: 2px solid #b30000; padding-bottom: 8px; margin-bottom: 12px; }
        .header td { vertical-align: middle; }
        .logo-img { max-height: 60px; max-width: 220px; }
        .header-right { text-align: right; }
        .titulo-orden { font-size: 18pt; font-weight: bold; color: #b30000; letter-spacing: 1px; }
        .numero-orden { font-size: 11pt; color: #222; margin-top: 2px; }
        .numero-orden strong { color: #b30000; }

        /* ====== EMPRESA ====== */
        .empresa { font-size: 8pt; color: #444; margin-bottom: 14px; line-height: 1.35; }
        .empresa b { color: #111; }

        /* ====== SECCIONES ====== */
        .seccion { width: 100%; margin-bottom: 12px; border: 1px solid #ccc; }
        .seccion-titulo {
            background: #b30000; color: #fff; padding: 5px 9px;
            font-weight: bold; font-size: 9.5pt; letter-spacing: 0.5px;
        }
        .seccion-body { padding: 8px 10px; }
        .row { width: 100%; }
        .row td { padding: 3px 6px; vertical-align: top; font-size: 9.5pt; }
        .label { color: #555; font-weight: bold; width: 28%; }
        .valor { color: #111; }

        /* ====== TEXTO LARGO ====== */
        .bloque-texto {
            border: 1px solid #bbb; min-height: 60px; padding: 6px 8px;
            background: #fafafa; font-size: 9.5pt; line-height: 1.45;
        }

        /* ====== TABLAS ====== */
        table.items { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.items th, table.items td { border: 1px solid #999; padding: 4px 6px; font-size: 9pt; }
        table.items th { background: #eee; text-align: left; }

        /* ====== FIRMAS ====== */
        .firmas { width: 100%; margin-top: 30px; }
        .firmas td { width: 50%; text-align: center; padding: 0 12px; }
        .linea-firma { border-top: 1px solid #444; margin-top: 50px; padding-top: 4px; font-size: 9pt; color: #444; }
        .firma-rol { font-weight: bold; color: #b30000; margin-top: 2px; font-size: 9pt; }
        .firma-nombre { font-size: 9pt; color: #111; }

        /* ====== PIE ====== */
        .pie {
            position: fixed; bottom: -10mm; left: 0; right: 0;
            text-align: center; font-size: 7.5pt; color: #666;
            border-top: 1px solid #ddd; padding-top: 4px;
        }
        .badge {
            display: inline-block; padding: 2px 8px; border-radius: 10px;
            font-size: 8.5pt; color: #fff; font-weight: bold;
        }
        .b-pendiente   { background: #6c757d; }
        .b-asignada    { background: #0dcaf0; color: #003; }
        .b-en_proceso  { background: #0d6efd; }
        .b-completada  { background: #198754; }
        .b-entregada   { background: #198754; }
        .b-cancelada   { background: #dc3545; }
        .b-recibida    { background: #6c757d; }
        .b-pendiente_repuestos { background: #ffc107; color: #000; }

        .prioridad-baja     { color: #555; }
        .prioridad-media    { color: #0d6efd; }
        .prioridad-alta     { color: #fd7e14; font-weight: bold; }
        .prioridad-urgente  { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header">
        <tr>
            <td style="width: 60%;">
                <img src="{{ public_path('images/logo.png') }}" alt="INNOVATECH Global" class="logo-img">
            </td>
            <td class="header-right">
                <div class="titulo-orden">ORDEN DE SERVICIO</div>
                <div class="numero-orden">N° <strong>{{ $orden->numero_orden }}</strong></div>
                <div style="font-size: 9pt; color: #555; margin-top: 4px;">
                    Estado:
                    <span class="badge b-{{ $orden->estado }}">{{ ucfirst(str_replace('_',' ', $orden->estado)) }}</span>
                </div>
                <div style="font-size: 9pt; color: #555; margin-top: 4px;">
                    Prioridad:
                    <span class="prioridad-{{ $orden->prioridad }}">{{ ucfirst($orden->prioridad) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="empresa">
        <b>INNOVATECH GLOBAL SAS</b> &nbsp; NIT: 901543944-6 <br>
        <b>Cali:</b> Calle 24AN #5N 58 Local 12 CC Astrocentro &nbsp;|&nbsp;
        <b>Pereira:</b> Calle 16 # 4-44 centro <br>
        <b>Tel Cali:</b> 3174422343 / 3043097203 &nbsp;|&nbsp;
        <b>Tel Pereira:</b> 3002942179 / 3002942155 <br>
        <b>Correo:</b> ventas@innovatechglobal.com.co &nbsp;|&nbsp;
        <b>Web:</b> www.innovatechglobal.com.co
    </div>

    {{-- CLIENTE --}}
    <div class="seccion">
        <div class="seccion-titulo">DATOS DEL CLIENTE</div>
        <div class="seccion-body">
            <table class="row">
                <tr>
                    <td class="label">Cliente:</td>
                    <td class="valor">{{ optional($orden->cliente)->nombre_contacto ?? '—' }}</td>
                    <td class="label">Documento:</td>
                    <td class="valor">
                        {{ optional($orden->cliente)->tipo_documento ?? '' }}
                        {{ optional($orden->cliente)->numero_identificacion ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Teléfono:</td>
                    <td class="valor">
                        {{ optional($orden->cliente)->telefono ?? optional($orden->cliente)->celular ?? '—' }}
                    </td>
                    <td class="label">Email:</td>
                    <td class="valor">{{ optional($orden->cliente)->email ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Dirección:</td>
                    <td class="valor" colspan="3">
                        {{ optional($orden->cliente)->direccion ?? '—' }}
                        @if(optional($orden->cliente)->ciudad_texto)
                            , {{ $orden->cliente->ciudad_texto }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- EQUIPO --}}
    @if($orden->equipo)
    <div class="seccion">
        <div class="seccion-titulo">DATOS DEL EQUIPO</div>
        <div class="seccion-body">
            <table class="row">
                <tr>
                    <td class="label">Tipo:</td>
                    <td class="valor">{{ $orden->equipo->tipo_equipo ?? '—' }}</td>
                    <td class="label">Marca / Modelo:</td>
                    <td class="valor">{{ trim(($orden->equipo->marca ?? '') . ' ' . ($orden->equipo->modelo ?? '')) ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Número de serie:</td>
                    <td class="valor">{{ $orden->equipo->numero_serie ?? '—' }}</td>
                    <td class="label">MAC / IP:</td>
                    <td class="valor">
                        {{ $orden->equipo->mac_address ?? '—' }}
                        @if($orden->equipo->ip_address) / {{ $orden->equipo->ip_address }} @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">En garantía:</td>
                    <td class="valor">
                        {{ $orden->equipo->en_garantia ? 'Sí' : 'No' }}
                        @if($orden->equipo->vencimiento_garantia)
                            (vence {{ $orden->equipo->vencimiento_garantia->format('d/m/Y') }})
                        @endif
                    </td>
                    <td class="label">Ubicación:</td>
                    <td class="valor">{{ $orden->equipo->ubicacion_instalacion ?? '—' }}</td>
                </tr>
            </table>
        </div>
    </div>
    @endif

    {{-- ORDEN --}}
    <div class="seccion">
        <div class="seccion-titulo">DATOS DEL SERVICIO</div>
        <div class="seccion-body">
            <table class="row">
                <tr>
                    <td class="label">Tipo de servicio:</td>
                    <td class="valor">{{ $orden->tipo_servicio }}</td>
                    <td class="label">Técnico asignado:</td>
                    <td class="valor">{{ optional($orden->tecnico)->nombre_completo ?? 'Sin asignar' }}</td>
                </tr>
                <tr>
                    <td class="label">Fecha de recepción:</td>
                    <td class="valor">{{ optional($orden->fecha_recepcion)->format('d/m/Y') ?? '—' }}</td>
                    <td class="label">Promesa de entrega:</td>
                    <td class="valor">{{ optional($orden->fecha_promesa_entrega)->format('d/m/Y') ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Inicio:</td>
                    <td class="valor">{{ optional($orden->fecha_inicio_trabajo)->format('d/m/Y') ?? '—' }}</td>
                    <td class="label">Finalización:</td>
                    <td class="valor">{{ optional($orden->fecha_finalizacion)->format('d/m/Y') ?? '—' }}</td>
                </tr>
            </table>

            <div style="margin-top: 8px;">
                <div class="label" style="width:auto;">Descripción del problema reportado:</div>
                <div class="bloque-texto">{{ $orden->descripcion_problema ?: '—' }}</div>
            </div>

            @if($orden->accesorios_entregados)
            <div style="margin-top: 8px;">
                <div class="label" style="width:auto;">Accesorios entregados:</div>
                <div class="bloque-texto">{{ $orden->accesorios_entregados }}</div>
            </div>
            @endif

            @if($orden->observaciones)
            <div style="margin-top: 8px;">
                <div class="label" style="width:auto;">Observaciones:</div>
                <div class="bloque-texto">{{ $orden->observaciones }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- DIAGNÓSTICOS --}}
    @if($orden->diagnosticos->count() > 0)
    <div class="seccion">
        <div class="seccion-titulo">DIAGNÓSTICOS</div>
        <div class="seccion-body">
            <table class="items">
                <thead>
                    <tr>
                        <th style="width:80px;">Fecha</th>
                        <th>Hallazgo / Diagnóstico</th>
                        <th style="width:120px;">Técnico</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orden->diagnosticos as $diag)
                    <tr>
                        <td>{{ optional($diag->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $diag->descripcion ?? $diag->diagnostico ?? '' }}</td>
                        <td>{{ optional($diag->tecnico)->nombre_completo ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- REPUESTOS USADOS --}}
    @if($orden->repuestosUsados->count() > 0)
    <div class="seccion">
        <div class="seccion-titulo">REPUESTOS UTILIZADOS</div>
        <div class="seccion-body">
            <table class="items">
                <thead>
                    <tr>
                        <th>Repuesto</th>
                        <th style="width:60px; text-align:center;">Cant.</th>
                        <th style="width:90px; text-align:right;">Precio</th>
                        <th style="width:90px; text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orden->repuestosUsados as $ru)
                    <tr>
                        <td>{{ optional($ru->repuesto)->nombre ?? '—' }}</td>
                        <td style="text-align:center;">{{ $ru->cantidad ?? 0 }}</td>
                        <td style="text-align:right;">$ {{ number_format($ru->precio_unitario ?? 0, 0) }}</td>
                        <td style="text-align:right;">$ {{ number_format($ru->subtotal ?? 0, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- COSTOS --}}
    <div class="seccion">
        <div class="seccion-titulo">COSTOS</div>
        <div class="seccion-body">
            <table class="row">
                <tr>
                    <td class="label">Mano de obra:</td>
                    <td class="valor" style="text-align:right;">$ {{ number_format($orden->costo_mano_obra ?? 0, 0) }}</td>
                    <td class="label">Repuestos:</td>
                    <td class="valor" style="text-align:right;">$ {{ number_format($orden->costo_repuestos ?? 0, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="label" style="font-size:11pt;">TOTAL:</td>
                    <td class="valor" style="text-align:right; font-size:12pt; font-weight:bold; color:#b30000;">
                        $ {{ number_format($orden->costo_total ?? 0, 0) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- FIRMAS --}}
    <table class="firmas">
        <tr>
            <td>
                <div class="linea-firma">Firma del cliente</div>
                <div class="firma-nombre">{{ optional($orden->cliente)->nombre_contacto }}</div>
                <div class="firma-nombre">
                    C.C. {{ optional($orden->cliente)->numero_identificacion ?? '________________' }}
                </div>
                <div class="firma-rol">RECIBIDO CONFORME</div>
            </td>
            <td>
                <div class="linea-firma">Firma del técnico</div>
                <div class="firma-nombre">{{ optional($orden->tecnico)->nombre_completo ?? '________________' }}</div>
                <div class="firma-nombre">INNOVATECH GLOBAL S.A.S.</div>
                <div class="firma-rol">SERVICIO TÉCNICO</div>
            </td>
        </tr>
    </table>

    <div style="margin-top: 25px; font-size: 7.5pt; color: #666; text-align: justify; border-top: 1px dashed #bbb; padding-top: 6px;">
        Al firmar este documento el cliente acepta haber recibido conforme el equipo y/o el servicio descrito.
        INNOVATECH GLOBAL S.A.S. cumple con la Ley 1581 de 2012 y Decreto 1377 de 2013 para el tratamiento de datos personales.
        Los equipos no reclamados en un periodo superior a 60 días podrán ser dispuestos según la política interna de la empresa.
    </div>

    <div class="pie">
        Orden de Servicio {{ $orden->numero_orden }} &nbsp;|&nbsp;
        Generada el {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp;
        INNOVATECH GLOBAL S.A.S.
    </div>

</body>
</html>
