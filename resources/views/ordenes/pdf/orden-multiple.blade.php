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
    @foreach($ordenesData as $index => $data)
        @if($index > 0)
            <div class="page-break"></div>
        @endif

        @include('ordenes.pdf._page-info', $data)

        @if($data['orden']->piezas->count() > 0)
            @include('ordenes.pdf._page-piezas', $data)
        @endif

        @if($data['bosquejosData']->count() > 0)
            @include('ordenes.pdf._page-bosquejos', $data)
        @endif
    @endforeach
</body>
</html>
