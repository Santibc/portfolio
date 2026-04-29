<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Abrir Caja</title>
    <style>
        @page { size: 72mm 8mm; margin: 0; padding: 0; }
        html, body {
            margin: 0;
            padding: 0;
            width: 72mm;
            height: 8mm;
            background: white;
            font-family: monospace;
        }
        .dot {
            font-size: 6px;
            color: #000;
            text-align: center;
            padding-top: 1mm;
        }
    </style>
</head>
<body>
    <div class="dot">.</div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 200);
        });
        window.addEventListener('afterprint', function () {
            window.close();
        });
    </script>
</body>
</html>
