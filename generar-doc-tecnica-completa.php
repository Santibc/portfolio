<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

echo "\n================================================\n";
echo "  GENERANDO DOCUMENTACION TECNICA COMPLETA  \n";
echo "================================================\n\n";

// Leer contenido de la Parte 1
$parte1 = file_get_contents('c:/xampp/htdocs/portfolio/temp_parte1.txt');

echo "✓ Contenido extraido: " . strlen($parte1) . " caracteres\n";

// Limpiar caracteres especiales
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

$parte1 = str_replace(array_keys($replacements), array_values($replacements), $parte1);

echo "✓ Caracteres especiales reemplazados\n";

// Convertir Markdown a HTML
$environment = new Environment([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new TableExtension());

$converter = new MarkdownConverter($environment);
$htmlContent = $converter->convert($parte1)->getContent();

echo "✓ Markdown convertido a HTML\n";

// Estilos CSS (tema azul para documentación técnica)
$styles = <<<'CSS'
<style>
    @page {
        margin: 30mm 30mm 30mm 30mm;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 9pt;
        line-height: 1.4;
        color: #2c3e50;
    }

    h1 {
        color: #2c3e50;
        font-size: 20pt;
        margin-top: 25px;
        margin-bottom: 15px;
        border-bottom: 3px solid #3498db;
        padding-bottom: 8px;
        page-break-after: avoid;
    }

    h2 {
        color: #34495e;
        font-size: 16pt;
        margin-top: 20px;
        margin-bottom: 12px;
        border-bottom: 2px solid #95a5a6;
        padding-bottom: 6px;
        page-break-after: avoid;
    }

    h3 {
        color: #34495e;
        font-size: 12pt;
        margin-top: 15px;
        margin-bottom: 10px;
        page-break-after: avoid;
    }

    h4 {
        color: #7f8c8d;
        font-size: 10pt;
        margin-top: 12px;
        margin-bottom: 8px;
        page-break-after: avoid;
    }

    p {
        margin: 0 0 8px 0;
        text-align: justify;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 12px 0;
        font-size: 8pt;
        page-break-inside: avoid;
    }

    thead {
        background-color: #3498db;
        color: white;
    }

    th {
        padding: 6px 5px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #2980b9;
    }

    td {
        padding: 5px;
        border: 1px solid #bdc3c7;
        vertical-align: top;
    }

    tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .portada {
        text-align: center;
        margin-top: 200px;
        page-break-after: always;
    }

    .portada h1 {
        font-size: 36pt;
        color: #2c3e50;
        border: none;
        margin-bottom: 40px;
    }

    .portada h2 {
        font-size: 20pt;
        color: #7f8c8d;
        border: none;
        margin-bottom: 80px;
    }

    .portada p {
        font-size: 12pt;
        margin: 15px 0;
        color: #34495e;
    }

    strong {
        font-weight: bold;
        color: #2c3e50;
    }

    ul, ol {
        margin: 8px 0 8px 20px;
    }

    li {
        margin: 5px 0;
    }

    blockquote {
        background-color: #ecf0f1;
        border-left: 4px solid #3498db;
        padding: 8px 10px;
        margin: 10px 0;
        color: #7f8c8d;
        font-style: italic;
        page-break-inside: avoid;
    }

    pre {
        background-color: #2c3e50;
        color: #ecf0f1;
        border-left: 4px solid #3498db;
        padding: 10px;
        margin: 10px 0;
        font-size: 7pt;
        font-family: 'Courier New', monospace;
        white-space: pre-wrap;
        page-break-inside: avoid;
    }

    code {
        background-color: #ecf0f1;
        border: 1px solid #bdc3c7;
        border-radius: 3px;
        padding: 2px 5px;
        font-family: 'Courier New', monospace;
        font-size: 8pt;
        color: #c0392b;
    }

    pre code {
        background: transparent;
        border: none;
        color: #ecf0f1;
        padding: 0;
    }

    hr {
        border: none;
        border-top: 2px solid #3498db;
        margin: 20px 0;
    }
</style>
CSS;

// Portada
$portada = <<<HTML
<div class="portada">
    <h1>DOCUMENTACION TECNICA</h1>
    <h2>Para Desarrolladores y Administradores del Sistema</h2>
    <p><strong>Framework:</strong> Laravel 9.52</p>
    <p><strong>Version:</strong> 1.0</p>
    <p><strong>Fecha:</strong> 25/11/2024</p>
    <p><strong>Sistema:</strong> E-Commerce B2B + Servicio Tecnico</p>
</div>
HTML;

// Pie de página
$footer = <<<HTML
<div style="page-break-before: always; text-align: center; margin-top: 220px;">
    <h2 style="border: none; color: #2c3e50;">FIN DE LA DOCUMENTACION TECNICA</h2>
    <p style="margin-top: 50px; color: #7f8c8d; font-size: 10pt;">
        <strong>Sistema:</strong> Portfolio B2B + Servicio Tecnico<br>
        <strong>Framework:</strong> Laravel 9.52<br>
        <strong>Version:</strong> 1.0
    </p>
</div>
HTML;

// HTML completo
$html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentacion Tecnica</title>
    {$styles}
</head>
<body>
    {$portada}
    {$htmlContent}
    {$footer}
</body>
</html>
HTML;

echo "✓ HTML completo generado\n";

try {
    echo "✓ Generando PDF...\n";

    $pdf = Pdf::loadHTML($html);
    $pdf->setPaper('a4', 'portrait');

    $outputPath = public_path('documentacion/DOCUMENTACION_TECNICA.pdf');
    $pdf->save($outputPath);

    $fileSize = round(filesize($outputPath) / 1024, 2);

    echo "\n================================================\n";
    echo "  ✓ DOCUMENTACION TECNICA GENERADA!  \n";
    echo "================================================\n\n";
    echo "Archivo: $outputPath\n";
    echo "Tamaño: {$fileSize} KB\n";
    echo "URL: " . url('documentacion/DOCUMENTACION_TECNICA.pdf') . "\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
