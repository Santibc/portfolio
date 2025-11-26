<?php

/**
 * Script simplificado para generar PDF de documentación
 * Usa configuración optimizada para documentos grandes
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

echo "\n===========================================\n";
echo "  Generador de PDF - Versión Optimizada  \n";
echo "===========================================\n\n";

try {
    // Leer archivo Markdown
    $markdownPath = base_path('DOCUMENTACION_SISTEMA.md');

    if (!File::exists($markdownPath)) {
        throw new Exception("Archivo no encontrado: DOCUMENTACION_SISTEMA.md");
    }

    echo "✓ Leyendo archivo Markdown...\n";
    $content = File::get($markdownPath);

    // Convertir a HTML simple (sin procesamiento complejo)
    echo "✓ Convirtiendo a HTML simple...\n";
    $html = convertToSimpleHtml($content);

    // Crear directorio
    $outputDir = public_path('documentacion');
    if (!File::exists($outputDir)) {
        File::makeDirectory($outputDir, 0755, true);
    }

    echo "✓ Generando PDF (esto puede tardar 1-2 minutos)...\n";
    echo "  Por favor espere...\n\n";

    // Configuración optimizada para documentos grandes
    $pdf = Pdf::loadHTML($html);
    $pdf->setPaper('a4', 'portrait');

    // Opciones para mejorar rendimiento
    $pdf->setOption('isHtml5ParserEnabled', false); // Más rápido
    $pdf->setOption('isRemoteEnabled', false);
    $pdf->setOption('chroot', public_path());

    // Guardar
    $outputPath = $outputDir . '/DOCUMENTACION_SISTEMA.pdf';
    $pdf->save($outputPath);

    $fileSize = File::size($outputPath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);

    echo "============================================\n";
    echo "  ✓ PDF GENERADO EXITOSAMENTE  \n";
    echo "============================================\n\n";
    echo "Ubicación: $outputPath\n";
    echo "Tamaño: $fileSizeMB MB\n\n";
    echo "Acceso web: " . url('documentacion/DOCUMENTACION_SISTEMA.pdf') . "\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}

function convertToSimpleHtml($markdown) {
    // Escapar HTML
    $html = htmlspecialchars($markdown, ENT_NOQUOTES, 'UTF-8');

    // Headers
    $html = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $html);
    $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);

    // Bold y cursiva
    $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $html);

    // Code inline
    $html = preg_replace('/`([^`]+)`/', '<code style="background:#f5f5f5;padding:2px 4px;font-family:monospace;">$1</code>', $html);

    // Code blocks
    $html = preg_replace('/```[\w]*\n(.*?)```/s', '<pre style="background:#f5f5f5;padding:10px;border-left:3px solid #0066cc;overflow-x:auto;"><code>$1</code></pre>', $html);

    // Listas
    $html = preg_replace('/^- (.+)$/m', '<li>$1</li>', $html);
    $html = preg_replace('/^  - (.+)$/m', '<li style="margin-left:20px;">$1</li>', $html);
    $html = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $html);
    $html = preg_replace('/<\/ul>\s*<ul>/', '', $html);

    // Párrafos
    $html = preg_replace('/^(?!<[huplc])(.+)$/m', '<p>$1</p>', $html);

    // Limpiar párrafos en headers
    $html = preg_replace('/<p>(<h[1-6]>)/i', '$1', $html);
    $html = preg_replace('/(<\/h[1-6]>)<\/p>/i', '$1', $html);

    // Líneas horizontales
    $html = preg_replace('/^---$/m', '<hr style="border:none;border-top:2px solid #0066cc;margin:20px 0;">', $html);

    // Saltos de línea
    $html = nl2br($html);

    // Envolver en estructura HTML con estilos
    return '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            margin: 20px;
        }
        h1 {
            color: #0066cc;
            font-size: 18pt;
            margin-top: 30px;
            page-break-before: always;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 5px;
        }
        h2 {
            color: #0066cc;
            font-size: 14pt;
            margin-top: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        h3 {
            color: #004080;
            font-size: 12pt;
            margin-top: 15px;
        }
        h4 {
            color: #333;
            font-size: 11pt;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9pt;
        }
        th {
            background: #0066cc;
            color: white;
            padding: 5px;
            text-align: left;
        }
        td {
            border: 1px solid #ddd;
            padding: 5px;
        }
        code {
            background: #f5f5f5;
            padding: 2px 4px;
            font-family: monospace;
        }
        pre {
            background: #f5f5f5;
            padding: 10px;
            border-left: 3px solid #0066cc;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        ul {
            margin-left: 20px;
        }
        li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div style="text-align:center;margin-top:100px;margin-bottom:100px;">
        <h1 style="font-size:28pt;border:none;">DOCUMENTACIÓN COMPLETA DEL SISTEMA</h1>
        <h2 style="font-size:18pt;color:#666;border:none;">Sistema de E-Commerce B2B y Gestión de Servicio Técnico</h2>
        <p style="margin-top:50px;">
            <strong>Framework:</strong> Laravel 9.52<br>
            <strong>Versión:</strong> 1.0<br>
            <strong>Fecha:</strong> ' . date('d/m/Y') . '
        </p>
    </div>
    <div style="page-break-after:always;"></div>
    ' . $html . '
    <div style="page-break-before:always;text-align:center;margin-top:100px;">
        <h2 style="border:none;">FIN DE LA DOCUMENTACIÓN</h2>
        <p style="margin-top:50px;">
            <strong>Versión:</strong> 1.0<br>
            <strong>Sistema:</strong> Portfolio B2B + Servicio Técnico<br>
            <strong>Framework:</strong> Laravel 9.52
        </p>
    </div>
</body>
</html>';
}
