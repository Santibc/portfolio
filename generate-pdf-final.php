<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

echo "\n=============================================\n";
echo "  Generador Profesional de Documentación PDF  \n";
echo "=============================================\n\n";

try {
    $markdownPath = base_path('DOCUMENTACION_SISTEMA.md');

    if (!File::exists($markdownPath)) {
        throw new Exception("Archivo no encontrado");
    }

    echo "✓ Leyendo documentación Markdown...\n";
    $markdown = File::get($markdownPath);

    echo "✓ Convirtiendo Markdown a HTML con CommonMark...\n";

    // Configurar CommonMark con extensiones
    $environment = new Environment([
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new TableExtension());

    $converter = new MarkdownConverter($environment);
    $htmlContent = $converter->convert($markdown)->getContent();

    echo "✓ Aplicando estilos profesionales...\n";

    $html = buildPdfHtml($htmlContent);

    $outputDir = public_path('documentacion');
    if (!File::exists($outputDir)) {
        File::makeDirectory($outputDir, 0755, true);
    }

    echo "✓ Generando PDF profesional...\n";
    echo "  (Esto puede tardar 30-60 segundos)\n\n";

    $pdf = Pdf::loadHTML($html);
    $pdf->setPaper('a4', 'portrait');
    $pdf->setOption('isHtml5ParserEnabled', true);
    $pdf->setOption('isRemoteEnabled', false);

    $outputPath = $outputDir . '/DOCUMENTACION_SISTEMA.pdf';
    $pdf->save($outputPath);

    $fileSize = File::size($outputPath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);

    echo "============================================\n";
    echo "  ✓ DOCUMENTACIÓN GENERADA EXITOSAMENTE  \n";
    echo "============================================\n\n";
    echo "📄 Archivo: $outputPath\n";
    echo "📊 Tamaño: $fileSizeMB MB\n";
    echo "🌐 URL: " . url('documentacion/DOCUMENTACION_SISTEMA.pdf') . "\n\n";
    echo "✅ El PDF está listo para usar!\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n\n";
    exit(1);
}

function buildPdfHtml($content) {
    $styles = <<<'CSS'
    <style>
        @page {
            margin: 2.5cm 2cm 2cm 2cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #2c3e50;
        }

        /* Títulos */
        h1 {
            color: #2c3e50;
            font-size: 18pt;
            font-weight: bold;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 3px solid #3498db;
            page-break-before: always;
            page-break-after: avoid;
        }

        h1:first-of-type {
            page-break-before: auto;
        }

        h2 {
            color: #34495e;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0 12px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #95a5a6;
            page-break-after: avoid;
        }

        h3 {
            color: #34495e;
            font-size: 11pt;
            font-weight: bold;
            margin: 15px 0 10px 0;
            page-break-after: avoid;
        }

        h4 {
            color: #7f8c8d;
            font-size: 10pt;
            font-weight: bold;
            margin: 12px 0 8px 0;
            page-break-after: avoid;
        }

        /* Párrafos y texto */
        p {
            margin: 0 0 8px 0;
            text-align: justify;
            orphans: 3;
            widows: 3;
        }

        strong {
            color: #2c3e50;
            font-weight: bold;
        }

        em {
            font-style: italic;
            color: #7f8c8d;
        }

        /* Listas */
        ul, ol {
            margin: 8px 0 8px 20px;
        }

        li {
            margin: 4px 0;
            line-height: 1.5;
        }

        ul ul, ol ul {
            margin: 4px 0 4px 15px;
        }

        /* Código */
        code {
            background-color: #ecf0f1;
            border: 1px solid #bdc3c7;
            border-radius: 3px;
            padding: 2px 5px;
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            color: #c0392b;
        }

        pre {
            background-color: #2c3e50;
            color: #ecf0f1;
            border-left: 4px solid #3498db;
            padding: 10px;
            margin: 10px 0;
            overflow-x: auto;
            page-break-inside: avoid;
            font-size: 7.5pt;
            line-height: 1.3;
        }

        pre code {
            background: transparent;
            border: none;
            color: #ecf0f1;
            padding: 0;
        }

        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            page-break-inside: avoid;
            font-size: 8pt;
        }

        thead {
            display: table-header-group;
        }

        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #2980b9;
        }

        td {
            padding: 6px;
            border: 1px solid #bdc3c7;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #ecf0f1;
        }

        tr:hover {
            background-color: #d5dbdb;
        }

        /* Líneas horizontales */
        hr {
            border: none;
            border-top: 2px solid #3498db;
            margin: 20px 0;
        }

        /* Enlaces */
        a {
            color: #3498db;
            text-decoration: none;
        }

        /* Blockquotes */
        blockquote {
            border-left: 4px solid #3498db;
            padding-left: 15px;
            margin: 10px 0;
            font-style: italic;
            color: #7f8c8d;
            background-color: #ecf0f1;
            padding: 10px 10px 10px 15px;
        }

        /* Saltos de página */
        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* Portada */
        .cover {
            text-align: center;
            margin-top: 150px;
            page-break-after: always;
        }

        .cover h1 {
            font-size: 32pt;
            color: #2c3e50;
            border: none;
            margin-bottom: 30px;
            page-break-before: auto;
        }

        .cover h2 {
            font-size: 20pt;
            color: #7f8c8d;
            border: none;
            margin-bottom: 50px;
        }

        .cover .info {
            font-size: 11pt;
            margin: 10px 0;
            color: #34495e;
        }

        /* Header/Footer simulado */
        .header {
            position: fixed;
            top: -20px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            color: #7f8c8d;
            font-size: 8pt;
            border-bottom: 1px solid #ecf0f1;
            padding-top: 10px;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            color: #95a5a6;
            font-size: 8pt;
            border-top: 1px solid #ecf0f1;
            padding-top: 5px;
        }
    </style>
CSS;

    $fecha = date('d/m/Y');

    $portada = <<<HTML
    <div class="cover">
        <h1>DOCUMENTACIÓN COMPLETA<br>DEL SISTEMA</h1>
        <h2>Sistema de E-Commerce B2B<br>y Gestión de Servicio Técnico</h2>
        <div class="info"><strong>Framework:</strong> Laravel 9.52</div>
        <div class="info"><strong>Versión:</strong> 1.0</div>
        <div class="info"><strong>Fecha:</strong> {$fecha}</div>
        <div class="info"><strong>Entorno:</strong> PHP 8.0+ | MySQL via XAMPP</div>
    </div>
HTML;

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación del Sistema</title>
    {$styles}
</head>
<body>
    {$portada}
    {$content}
    <div class="page-break"></div>
    <div style="text-align: center; margin-top: 200px;">
        <h2 style="border: none; color: #2c3e50;">FIN DE LA DOCUMENTACIÓN</h2>
        <p style="margin-top: 40px; color: #7f8c8d;">
            <strong>Versión:</strong> 1.0<br>
            <strong>Fecha:</strong> {$fecha}<br>
            <strong>Sistema:</strong> Portfolio B2B + Servicio Técnico<br>
            <strong>Framework:</strong> Laravel 9.52
        </p>
    </div>
</body>
</html>
HTML;
}
