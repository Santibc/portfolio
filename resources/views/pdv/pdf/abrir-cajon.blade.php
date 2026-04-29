<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Abrir Caja</title>
    <style>
        @page { size: 72mm auto; margin: 0; padding: 0; }
        html, body {
            margin: 0;
            padding: 0;
            background: white;
            font-family: monospace;
        }
        .dot {
            font-size: 6px;
            color: #000;
            line-height: 1;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body><span class="dot">.</span><script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 200);
        });
        window.addEventListener('afterprint', function () { window.close(); });
    </script></body>
</html>
