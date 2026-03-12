<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @include('ordenes.pdf._styles')
    </style>
</head>
<body>
    {{-- Pagina 1: Informacion de la orden --}}
    @include('ordenes.pdf._page-info')

    {{-- Pagina 2: Piezas (solo si existen) --}}
    @if($orden->piezas->count() > 0)
        @include('ordenes.pdf._page-piezas')
    @endif

    {{-- Pagina final: Bosquejos (solo si existen) --}}
    @if($bosquejosData->count() > 0)
        @include('ordenes.pdf._page-bosquejos')
    @endif
</body>
</html>
