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
echo "  Manual de Usuario - Generador PDF  \n";
echo "=============================================\n\n";

try {
    $markdownPath = base_path('DOCUMENTACION_SISTEMA.md');

    if (!File::exists($markdownPath)) {
        throw new Exception("Archivo no encontrado");
    }

    echo "✓ Leyendo documentación...\n";
    $markdown = File::get($markdownPath);

    // Extraer solo la PARTE 2: MANUAL DE USUARIO
    echo "✓ Extrayendo contenido de usuario...\n";

    preg_match('/# PARTE 2: MANUAL DE USUARIO(.*?)(?=# FIN DE LA DOCUMENTACIÓN|$)/s', $markdown, $matches);

    $contenidoUsuario = isset($matches[1]) ? trim($matches[1]) : '';

    if (empty($contenidoUsuario)) {
        throw new Exception("No se pudo extraer el contenido del manual de usuario");
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
    $contenidoUsuario = mb_convert_encoding($contenidoUsuario, 'UTF-8', 'UTF-8');

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
        '⚠️' => '[IMPORTANTE]',
        '⚠' => '[IMPORTANTE]',
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

    $contenidoUsuario = str_replace(array_keys($replacements), array_values($replacements), $contenidoUsuario);

    $htmlContent = $converter->convert($contenidoUsuario)->getContent();

    echo "✓ Aplicando estilos profesionales...\n";

    $html = buildPdfHtml($htmlContent, 'MANUAL DE USUARIO', 'Guía Práctica para Usuarios del Sistema');

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

    $outputPath = $outputDir . '/MANUAL_USUARIO.pdf';
    $pdf->save($outputPath);

    $fileSize = File::size($outputPath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);

    echo "============================================\n";
    echo "  ✓ MANUAL DE USUARIO GENERADO  \n";
    echo "============================================\n\n";
    echo "📄 Archivo: $outputPath\n";
    echo "📊 Tamaño: $fileSizeMB MB\n";
    echo "🌐 URL: " . url('documentacion/MANUAL_USUARIO.pdf') . "\n\n";

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
            font-size: 10pt;
            line-height: 1.6;
            color: #2c3e50;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #27ae60;
            font-size: 18pt;
            font-weight: bold;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 3px solid #27ae60;
            page-break-before: always;
            page-break-after: avoid;
        }

        h1:first-of-type {
            page-break-before: auto;
        }

        h2 {
            color: #16a085;
            font-size: 14pt;
            font-weight: bold;
            margin: 25px 0 12px 0;
            padding-bottom: 6px;
            border-bottom: 2px solid #95a5a6;
            page-break-after: avoid;
        }

        h3 {
            color: #16a085;
            font-size: 12pt;
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
            margin: 0 0 12px 0;
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
            margin: 12px 0 12px 25px;
        }

        li {
            margin: 6px 0;
            line-height: 1.7;
        }

        ul ul, ol ul {
            margin: 6px 0 6px 20px;
        }

        code {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 3px;
            padding: 2px 6px;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            color: #856404;
        }

        pre {
            background-color: #f8f9fa;
            color: #2c3e50;
            border-left: 4px solid #27ae60;
            padding: 12px;
            margin: 12px 0;
            overflow-x: auto;
            page-break-inside: avoid;
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        pre code {
            background: transparent;
            border: none;
            color: #2c3e50;
            padding: 0;
            font-size: 8pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            page-break-inside: avoid;
            font-size: 9pt;
        }

        thead {
            display: table-header-group;
        }

        th {
            background-color: #27ae60;
            color: white;
            font-weight: bold;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #229954;
        }

        td {
            padding: 8px;
            border: 1px solid #bdc3c7;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        hr {
            border: none;
            border-top: 2px solid #27ae60;
            margin: 25px 0;
        }

        a {
            color: #16a085;
            text-decoration: underline;
        }

        blockquote {
            border-left: 4px solid #f39c12;
            padding: 10px 10px 10px 15px;
            margin: 12px 0;
            background-color: #fff3cd;
            color: #856404;
            page-break-inside: avoid;
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
            color: #27ae60;
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

        /* Alertas estilo aviso */
        .alert-warning {
            background-color: #fff3cd;
            border-left: 4px solid #f39c12;
            padding: 10px 12px;
            margin: 12px 0;
            page-break-inside: avoid;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 10px 12px;
            margin: 12px 0;
            page-break-inside: avoid;
        }
    </style>
CSS;

    $fecha = date('d/m/Y');

    $portada = <<<HTML
    <div class="cover">
        <h1>{$titulo}</h1>
        <h2>{$subtitulo}</h2>
        <div class="info"><strong>Sistema:</strong> E-Commerce B2B + Servicio Técnico</div>
        <div class="info"><strong>Versión:</strong> 1.0</div>
        <div class="info"><strong>Fecha:</strong> {$fecha}</div>
        <div class="info"><strong>Framework:</strong> Laravel 9.52</div>
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
        <h2 style="border: none; color: #27ae60;">FIN DEL MANUAL DE USUARIO</h2>
        <p style="margin-top: 50px; color: #7f8c8d; font-size: 10pt;">
            <strong>Sistema:</strong> Portfolio B2B + Servicio Técnico<br>
            <strong>Versión:</strong> 1.0<br>
            <strong>Soporte:</strong> Contacte a su administrador del sistema
        </p>
    </div>
</body>
</html>
HTML;
}
