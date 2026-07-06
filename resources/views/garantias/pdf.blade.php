<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; margin: 0; }
    .header { border-bottom: 2px solid #b8006b; padding-bottom: 10px; margin-bottom: 16px; }
    .header table { width: 100%; }
    .header .logo { height: 46px; }
    .title { text-align: right; }
    .title h1 { font-size: 20px; margin: 0; color: #b8006b; }
    .title .num { font-size: 14px; color: #555; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
    .badge-pend { background: #fff3cd; color: #664d03; }
    .badge-lib { background: #d1e7dd; color: #0f5132; }
    .section { margin-bottom: 14px; }
    .section h2 { font-size: 13px; color: #b8006b; border-bottom: 1px solid #eee; padding-bottom: 3px; margin: 0 0 6px; }
    .grid { width: 100%; }
    .grid td { vertical-align: top; padding: 2px 4px; }
    .label { color: #888; font-size: 11px; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th { background: #f5eaf1; color: #6a2c53; text-align: left; padding: 6px 8px; font-size: 11px; border-bottom: 1px solid #e3c9d9; }
    table.data td { padding: 6px 8px; border-bottom: 1px solid #eee; }
    table.data td.center, table.data th.center { text-align: center; }
    .note { background: #f8f9fa; border-left: 3px solid #b8006b; padding: 6px 10px; margin-top: 4px; }
    .muted { color: #999; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #eee; padding-top: 4px; }
</style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 50%;">
                    @if($logo)
                        <img src="{{ $logo }}" class="logo" alt="Miracle">
                    @else
                        <strong style="font-size:18px;color:#b8006b;">Miracle</strong>
                    @endif
                </td>
                <td class="title" style="width: 50%;">
                    <h1>Garantía</h1>
                    <div class="num">#{{ $garantia->id }}</div>
                    @if($garantia->estado === 'liberado')
                        <span class="badge badge-lib">Liberado</span>
                    @else
                        <span class="badge badge-pend">Pendiente</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="grid">
            <tr>
                <td style="width:50%;">
                    <div class="label">Cliente</div>
                    <div>{{ $garantia->cliente?->nombre_completo ?? '—' }}</div>
                </td>
                <td style="width:25%;">
                    <div class="label">Tipo de garantía</div>
                    <div>{{ $garantia->tipoLegible() }}</div>
                </td>
                <td style="width:25%;">
                    <div class="label">Fecha de registro</div>
                    <div>{{ $garantia->created_at?->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Productos reclamados</h2>
        @if($garantia->items->isEmpty())
            <div class="muted">Sin productos asociados a esta garantía.</div>
        @else
            <table class="data">
                <thead>
                    <tr>
                        <th style="width:10%;">#</th>
                        <th>Producto</th>
                        <th class="center" style="width:18%;">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($garantia->items as $i => $it)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                {{ $it->producto?->nombre ?? 'Sin producto' }}@if($it->variante && $it->variante->nombre_variante) — {{ $it->variante->nombre_variante }}@endif
                            </td>
                            <td class="center">{{ (int) $it->cantidad }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($garantia->observacion_creacion)
    <div class="section">
        <h2>Observación de creación</h2>
        <div class="note">{{ $garantia->observacion_creacion }}</div>
    </div>
    @endif

    <div class="section">
        <table class="grid">
            <tr>
                <td style="width:50%;">
                    <div class="label">Registrada por</div>
                    <div>{{ $garantia->usuarioCreador?->name ?? '—' }}</div>
                </td>
                <td style="width:50%;">
                    <div class="label">Cotización vinculada</div>
                    <div>{{ $garantia->solicitud?->numero_solicitud ?? '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if($garantia->estado === 'liberado')
    <div class="section">
        <h2>Liberación</h2>
        <table class="grid">
            <tr>
                <td style="width:50%;">
                    <div class="label">Liberada por</div>
                    <div>{{ $garantia->usuarioLiberador?->name ?? '—' }}</div>
                </td>
                <td style="width:50%;">
                    <div class="label">Fecha de liberación</div>
                    <div>{{ $garantia->liberado_en?->format('d/m/Y H:i') ?? '—' }}</div>
                </td>
            </tr>
        </table>
        @if($garantia->observacion_liberacion)
            <div class="note" style="margin-top:6px;">{{ $garantia->observacion_liberacion }}</div>
        @endif

        @if($garantia->productosLiberacion->isNotEmpty())
            <div style="margin-top:8px;">
                <div class="label" style="margin-bottom:4px;">Productos entregados (descontados de stock)</div>
                <table class="data">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Ubicación</th>
                            <th class="center" style="width:18%;">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($garantia->productosLiberacion as $p)
                            <tr>
                                <td>{{ $p->producto?->nombre ?? '—' }}@if($p->variante && $p->variante->nombre_variante) — {{ $p->variante->nombre_variante }}@endif</td>
                                <td>{{ $p->ubicacionRelacion?->nombre ?? '—' }}</td>
                                <td class="center">{{ (int) $p->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif

    @if($garantia->documentos->isNotEmpty())
    <div class="section">
        <h2>Documentos adjuntos</h2>
        <ul style="margin:0; padding-left:16px;">
            @foreach($garantia->documentos as $doc)
                <li>{{ $doc->nombre_original }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="footer">
        Miracle · Garantía #{{ $garantia->id }} · Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
