<x-mail::message>
# ¡Tu pedido ha sido enviado! 📦

Hola **{{ $compra->nombre_cliente }}**,

Nos complace informarte que tu pedido **#{{ $compra->numero_compra }}** ha sido enviado y está en camino.

## Detalles del Envío
- **Transportadora:** {{ $compra->envio->transportadora }}
- **Número de guía:** {{ $compra->envio->numero_guia }}
- **Fecha de envío:** {{ $compra->envio->fecha_envio ? $compra->envio->fecha_envio->format('d/m/Y') : 'Hoy' }}
@if($compra->envio->fecha_entrega_estimada)
- **Fecha estimada de entrega:** {{ \Carbon\Carbon::parse($compra->envio->fecha_entrega_estimada)->format('d/m/Y') }}
@endif

@if($compra->envio->url_seguimiento)
<x-mail::button :url="$compra->envio->url_seguimiento">
Rastrear mi pedido
</x-mail::button>
@endif

## Resumen de tu pedido
- **Total de productos:** {{ $compra->total_items }} items
- **Total:** ${{ number_format($compra->total, 0, ',', '.') }}

### Productos incluidos:
@foreach($compra->items as $item)
- {{ $item->producto ? $item->producto->nombre : $item->nombre_producto }}@if($item->variante) - {{ $item->variante->nombre }}@endif ({{ $item->cantidad }} unidad{{ $item->cantidad > 1 ? 'es' : '' }})
@endforeach

<x-mail::panel>
**Información de entrega:**
{{ $compra->direccion_entrega }}, {{ $compra->ciudad->nombre }}, {{ $compra->ciudad->departamento->nombre }}
</x-mail::panel>

Si tienes alguna pregunta sobre tu pedido, no dudes en contactarnos.

Gracias por tu compra,<br>
**{{ $compra->empresa->nombre }}**
</x-mail::message>
