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
echo "  Documentación Técnica - Generador PDF  \n";
echo "=============================================\n\n";

try {
    $markdownPath = base_path('DOCUMENTACION_SISTEMA.md');

    if (!File::exists($markdownPath)) {
        throw new Exception("Archivo no encontrado");
    }

    echo "✓ Leyendo documentación...\n";
    $markdown = File::get($markdownPath);

    // Extraer solo la PARTE 1: DOCUMENTACIÓN TÉCNICA
    echo "✓ Extrayendo contenido técnico...\n";

    // Buscar desde el inicio hasta PARTE 2
    preg_match('/# PARTE 1: DOCUMENTACIÓN TÉCNICA(.*?)# PARTE 2: MANUAL DE USUARIO/s', $markdown, $matches);

    if (!isset($matches[1])) {
        // Si no encuentra el patrón, tomar desde PARTE 1 hasta el final de sección
        preg_match('/# PARTE 1: DOCUMENTACIÓN TÉCNICA(.*?)(?=---\n\n# PARTE 2|$)/s', $markdown, $matches);
    }

    $contenidoTecnico = isset($matches[1]) ? trim($matches[1]) : '';

    if (empty($contenidoTecnico)) {
        throw new Exception("No se pudo extraer el contenido técnico");
    }

    echo "✓ Convirtiendo a HTML...\n";

    $environment = new Environment([
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new TableExtension());

    $converter = new MarkdownConverter($environment);

    // Limpiar caracteres problemáticos ANTES de convertir
    $contenidoTecnico = mb_convert_encoding($contenidoTecnico, 'UTF-8', 'UTF-8');

    // Reemplazar TODOS los caracteres especiales
    $replacements = [
        '→' => '->',
        '├' => '|--',
        '└' => '|__',
        '│' => '|',
        '┬' => '+',
        '┼' => '+',
        '☑' => '[X]',
        '☐' => '[ ]',
        '✓' => 'OK',
        '✗' => 'X',
        '✅' => '[OK]',
        '❌' => '[X]',
        '⚠️' => '[!]',
        '⚠' => '[!]',
        '🔵' => '',
        '🟢' => '',
        '📄' => '',
        '📊' => '',
        '🌐' => '',
        '🎯' => '',
        '🔄' => '',
        '🎨' => '',
        '💡' => '',
        '📞' => '',
        '📚' => '',
        '✨' => '',
        '🚀' => '',
        '―' => '-',
        '–' => '-',
        '—' => '-',
        "'" => "'",
        "'" => "'",
        '"' => '"',
        '"' => '"',
        '…' => '...',
    ];

    $contenidoTecnico = str_replace(array_keys($replacements), array_values($replacements), $contenidoTecnico);

    $htmlContent = $converter->convert($contenidoTecnico)->getContent();

    echo "✓ Aplicando estilos profesionales...\n";

    $html = buildPdfHtml($htmlContent, 'DOCUMENTACIÓN TÉCNICA', 'Para Desarrolladores y Administradores del Sistema');

    $outputDir = public_path('documentacion');
    if (!File::exists($outputDir)) {
        File::makeDirectory($outputDir, 0755, true);
    }

    echo "✓ Generando PDF...\n\n";

    $pdf = Pdf::loadHTML($html);
    $pdf->setPaper('a4', 'portrait');
    $pdf->setOption('isHtml5ParserEnabled', true);
    $pdf->setOption('isRemoteEnabled', false);
    $pdf->setOption('dpi', 96);

    $outputPath = $outputDir . '/DOCUMENTACION_TECNICA.pdf';
    $pdf->save($outputPath);

    $fileSize = File::size($outputPath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);

    echo "============================================\n";
    echo "  ✓ PDF TÉCNICO GENERADO  \n";
    echo "============================================\n\n";
    echo "📄 Archivo: $outputPath\n";
    echo "📊 Tamaño: $fileSizeMB MB\n";
    echo "🌐 URL: " . url('documentacion/DOCUMENTACION_TECNICA.pdf') . "\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}

function buildPdfHtml($content, $titulo, $subtitulo) {
    $styles = <<<'CSS'
    <style>
        @page {
            margin: 2.5cm 3cm 2.5cm 3cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            color: #2c3e50;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #2c3e50;
            font-size: 18pt;
            font-weight: bold;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
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
            margin: 25px 0 12px 0;
            padding-bottom: 6px;
            border-bottom: 2px solid #95a5a6;
            page-break-after: avoid;
        }

        h3 {
            color: #34495e;
            font-size: 11pt;
            font-weight: bold;
            margin: 18px 0 10px 0;
            page-break-after: avoid;
        }

        h4 {
            color: #7f8c8d;
            font-size: 10pt;
            font-weight: bold;
            margin: 14px 0 8px 0;
            page-break-after: avoid;
        }

        p {
            margin: 0 0 10px 0;
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

        ul, ol {
            margin: 10px 0 10px 25px;
        }

        li {
            margin: 5px 0;
            line-height: 1.6;
        }

        ul ul, ol ul {
            margin: 5px 0 5px 20px;
        }

        code {
            background-color: #ecf0f1;
            border: 1px solid #bdc3c7;
            border-radius: 3px;
            padding: 2px 6px;
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            color: #c0392b;
        }

        pre {
            background-color: #2c3e50;
            color: #ecf0f1;
            border-left: 4px solid #3498db;
            padding: 12px;
            margin: 12px 0;
            overflow-x: auto;
            page-break-inside: avoid;
            font-family: 'Courier New', monospace;
            font-size: 7pt;
            line-height: 1.4;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        pre code {
            background: transparent;
            border: none;
            color: #ecf0f1;
            padding: 0;
            font-size: 7pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
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
            background-color: #f8f9fa;
        }

        hr {
            border: none;
            border-top: 2px solid #3498db;
            margin: 25px 0;
        }

        a {
            color: #3498db;
            text-decoration: underline;
        }

        blockquote {
            border-left: 4px solid #3498db;
            padding: 10px 10px 10px 15px;
            margin: 12px 0;
            font-style: italic;
            color: #7f8c8d;
            background-color: #ecf0f1;
        }

        .page-break {
            page-break-after: always;
        }

        .cover {
            text-align: center;
            margin-top: 180px;
            margin-bottom: 100px;
            page-break-after: always;
        }

        .cover h1 {
            font-size: 32pt;
            color: #2c3e50;
            border: none;
            margin-bottom: 30px;
            page-break-before: auto;
            padding: 0;
        }

        .cover h2 {
            font-size: 18pt;
            color: #7f8c8d;
            border: none;
            margin-bottom: 60px;
            padding: 0;
        }

        .cover .info {
            font-size: 11pt;
            margin: 12px 0;
            color: #34495e;
        }
    </style>
CSS;

    $fecha = date('d/m/Y');

    $portada = <<<HTML
    <div class="cover">
        <h1>{$titulo}</h1>
        <h2>{$subtitulo}</h2>
        <div class="info"><strong>Framework:</strong> Laravel 9.52</div>
        <div class="info"><strong>Versión:</strong> 1.0</div>
        <div class="info"><strong>Fecha:</strong> {$fecha}</div>
        <div class="info"><strong>Sistema:</strong> E-Commerce B2B + Servicio Técnico</div>
    </div>
HTML;

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{$titulo}</title>
    {$styles}
</head>
<body>
    {$portada}
    {$content}
    <div class="page-break"></div>
    <div style="text-align: center; margin-top: 220px;">
        <h2 style="border: none; color: #2c3e50;">FIN DE LA DOCUMENTACIÓN TÉCNICA</h2>
        <p style="margin-top: 50px; color: #7f8c8d; font-size: 10pt;">
            <strong>Sistema:</strong> Portfolio B2B + Servicio Técnico<br>
            <strong>Framework:</strong> Laravel 9.52<br>
            <strong>Versión:</strong> 1.0
        </p>
    </div>
</body>
</html>
HTML;
}
