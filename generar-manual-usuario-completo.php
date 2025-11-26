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
echo "  GENERANDO MANUAL DE USUARIO COMPLETO  \n";
echo "================================================\n\n";

// Leer contenido de la Parte 2
$parte2 = file_get_contents('c:/xampp/htdocs/portfolio/temp_parte2.txt');

echo "✓ Contenido extraido: " . strlen($parte2) . " caracteres\n";

// Renumerar secciones: 9->1, 10->2, 11->3, 12->4, 13->5, 14->6, 15->7
$parte2 = preg_replace('/^## 9\./m', '## 1.', $parte2);
$parte2 = preg_replace('/^### 9\./m', '### 1.', $parte2);
$parte2 = preg_replace('/^## 10\./m', '## 2.', $parte2);
$parte2 = preg_replace('/^### 10\./m', '### 2.', $parte2);
$parte2 = preg_replace('/^## 11\./m', '## 3.', $parte2);
$parte2 = preg_replace('/^### 11\./m', '### 3.', $parte2);
$parte2 = preg_replace('/^## 12\./m', '## 4.', $parte2);
$parte2 = preg_replace('/^### 12\./m', '### 4.', $parte2);
$parte2 = preg_replace('/^## 13\./m', '## 5.', $parte2);
$parte2 = preg_replace('/^### 13\./m', '### 5.', $parte2);
$parte2 = preg_replace('/^## 14\./m', '## 6.', $parte2);
$parte2 = preg_replace('/^### 14\./m', '### 6.', $parte2);
$parte2 = preg_replace('/^## 15\./m', '## 7.', $parte2);
$parte2 = preg_replace('/^### 15\./m', '### 7.', $parte2);

// Agregar índice al inicio
$indice = "# INDICE\n\n";
$indice .= "1. [Introduccion para Usuarios](#1-introduccion-para-usuarios)\n";
$indice .= "2. [Guia de Configuracion Inicial](#2-guia-de-configuracion-inicial)\n";
$indice .= "3. [Modulos de Servicio Tecnico](#3-modulos-de-servicio-tecnico)\n";
$indice .= "4. [Operaciones Diarias](#4-operaciones-diarias)\n";
$indice .= "5. [Flujos de Trabajo Completos](#5-flujos-de-trabajo-completos)\n";
$indice .= "6. [Preguntas Frecuentes](#6-preguntas-frecuentes)\n";
$indice .= "7. [Glosario](#7-glosario)\n\n";
$indice .= "---\n\n";

$parte2 = $indice . $parte2;

echo "✓ Secciones renumeradas (9-15 -> 1-7)\n";
echo "✓ Indice agregado\n";

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

$parte2 = str_replace(array_keys($replacements), array_values($replacements), $parte2);

echo "✓ Caracteres especiales reemplazados\n";

// Convertir Markdown a HTML
$environment = new Environment([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new TableExtension());

$converter = new MarkdownConverter($environment);
$htmlContent = $converter->convert($parte2)->getContent();

echo "✓ Markdown convertido a HTML\n";

// Estilos CSS
$styles = <<<'CSS'
<style>
    @page {
        margin: 30mm 30mm 30mm 30mm;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10pt;
        line-height: 1.5;
        color: #333;
    }

    h1 {
        color: #27ae60;
        font-size: 20pt;
        margin-top: 25px;
        margin-bottom: 15px;
        border-bottom: 3px solid #27ae60;
        padding-bottom: 8px;
        page-break-after: avoid;
    }

    h2 {
        color: #16a085;
        font-size: 16pt;
        margin-top: 20px;
        margin-bottom: 12px;
        border-bottom: 2px solid #bdc3c7;
        padding-bottom: 6px;
        page-break-after: avoid;
    }

    h3 {
        color: #16a085;
        font-size: 13pt;
        margin-top: 15px;
        margin-bottom: 10px;
        page-break-after: avoid;
    }

    h4 {
        color: #7f8c8d;
        font-size: 11pt;
        margin-top: 12px;
        margin-bottom: 8px;
        page-break-after: avoid;
    }

    p {
        margin: 0 0 10px 0;
        text-align: justify;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 12px 0;
        font-size: 9pt;
        page-break-inside: avoid;
    }

    thead {
        background-color: #27ae60;
        color: white;
    }

    th {
        padding: 8px 6px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #229954;
    }

    td {
        padding: 6px;
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
        color: #27ae60;
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
        margin: 10px 0 10px 25px;
    }

    li {
        margin: 6px 0;
    }

    blockquote {
        background-color: #fff3cd;
        border-left: 4px solid #f39c12;
        padding: 10px 12px;
        margin: 12px 0;
        page-break-inside: avoid;
    }

    pre {
        background-color: #f8f9fa;
        border-left: 4px solid #27ae60;
        padding: 10px;
        margin: 10px 0;
        font-size: 8pt;
        white-space: pre-wrap;
        page-break-inside: avoid;
    }

    code {
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 3px;
        padding: 2px 5px;
        font-family: 'Courier New', monospace;
        font-size: 9pt;
        color: #856404;
    }

    pre code {
        background: transparent;
        border: none;
        color: #2c3e50;
        padding: 0;
    }

    hr {
        border: none;
        border-top: 2px solid #27ae60;
        margin: 20px 0;
    }
</style>
CSS;

// Portada
$portada = <<<HTML
<div class="portada">
    <h1>MANUAL DE USUARIO</h1>
    <h2>Guia Practica para Usuarios del Sistema</h2>
    <p><strong>Sistema:</strong> E-Commerce B2B + Servicio Tecnico</p>
    <p><strong>Version:</strong> 1.0</p>
    <p><strong>Fecha:</strong> 25/11/2024</p>
    <p><strong>Framework:</strong> Laravel 9.52</p>
</div>
HTML;

// Pie de página
$footer = <<<HTML
<div style="page-break-before: always; text-align: center; margin-top: 220px;">
    <h2 style="border: none; color: #27ae60;">FIN DEL MANUAL DE USUARIO</h2>
    <p style="margin-top: 50px; color: #7f8c8d; font-size: 10pt;">
        <strong>Sistema:</strong> Portfolio B2B + Servicio Tecnico<br>
        <strong>Version:</strong> 1.0<br>
        <strong>Soporte:</strong> Contacte a su administrador del sistema
    </p>
</div>
HTML;

// HTML completo
$html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual de Usuario</title>
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

    $outputPath = public_path('documentacion/MANUAL_USUARIO.pdf');
    $pdf->save($outputPath);

    $fileSize = round(filesize($outputPath) / 1024, 2);

    echo "\n================================================\n";
    echo "  ✓ MANUAL DE USUARIO GENERADO!  \n";
    echo "================================================\n\n";
    echo "Archivo: $outputPath\n";
    echo "Tamaño: {$fileSize} KB\n";
    echo "URL: " . url('documentacion/MANUAL_USUARIO.pdf') . "\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
