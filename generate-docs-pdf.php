<?php

/**
 * Script para generar la documentación del sistema en PDF
 *
 * Uso: php generate-docs-pdf.php
 *
 * Genera: public/documentacion/DOCUMENTACION_SISTEMA.pdf
 */

// Cargar autoload de Laravel
require __DIR__.'/vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once __DIR__.'/bootstrap/app.php';

// Inicializar el kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

echo "\n==========================================\n";
echo "  Generador de Documentación del Sistema  \n";
echo "==========================================\n\n";

try {
    // 1. Verificar que existe el archivo Markdown
    $markdownPath = base_path('DOCUMENTACION_SISTEMA.md');

    if (!File::exists($markdownPath)) {
        throw new Exception("No se encontró el archivo DOCUMENTACION_SISTEMA.md");
    }

    echo "✓ Archivo Markdown encontrado\n";

    // 2. Leer el contenido Markdown
    $markdownContent = File::get($markdownPath);
    echo "✓ Contenido Markdown leído (" . strlen($markdownContent) . " caracteres)\n";

    // 3. Convertir Markdown a HTML básico
    echo "→ Convirtiendo Markdown a HTML...\n";
    $htmlContent = convertMarkdownToHtml($markdownContent);
    echo "✓ HTML generado (" . strlen($htmlContent) . " caracteres)\n";

    // 4. Crear directorio de salida si no existe
    $outputDir = public_path('documentacion');
    if (!File::exists($outputDir)) {
        File::makeDirectory($outputDir, 0755, true);
        echo "✓ Directorio 'public/documentacion' creado\n";
    } else {
        echo "✓ Directorio 'public/documentacion' existe\n";
    }

    // 5. Generar PDF usando DomPDF
    echo "→ Generando PDF (esto puede tardar unos segundos)...\n";

    $pdf = Pdf::loadView('pdf.documentacion-sistema', [
        'contenidoHtml' => $htmlContent
    ]);

    // Configurar opciones del PDF
    $pdf->setPaper('a4', 'portrait');
    $pdf->setOption('enable_html5_parser', true);
    $pdf->setOption('isRemoteEnabled', true);

    // 6. Guardar PDF
    $outputPath = $outputDir . '/DOCUMENTACION_SISTEMA.pdf';
    $pdf->save($outputPath);

    echo "✓ PDF generado exitosamente\n\n";

    // 7. Mostrar información del archivo generado
    $fileSize = File::size($outputPath);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);

    echo "==========================================\n";
    echo "  GENERACIÓN COMPLETADA  \n";
    echo "==========================================\n\n";
    echo "Ubicación: $outputPath\n";
    echo "Tamaño: $fileSizeMB MB\n";
    echo "Páginas: ~" . estimatePages(strlen($markdownContent)) . " páginas\n";
    echo "\nPuedes acceder al PDF desde:\n";
    echo "→ " . url('documentacion/DOCUMENTACION_SISTEMA.pdf') . "\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n\n";
    exit(1);
}

/**
 * Convierte Markdown básico a HTML
 *
 * @param string $markdown
 * @return string
 */
function convertMarkdownToHtml($markdown) {
    // Esta es una conversión básica. Para una conversión completa,
    // se podría usar una librería como parsedown o league/commonmark

    $html = $markdown;

    // Escapar HTML para seguridad
    $html = htmlspecialchars($html, ENT_NOQUOTES, 'UTF-8');

    // Headers
    $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
    $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $html);

    // Bold
    $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);

    // Italic
    $html = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $html);

    // Inline code
    $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);

    // Code blocks
    $html = preg_replace('/```(\w*)\n(.*?)```/s', '<pre><code>$2</code></pre>', $html);

    // Links
    $html = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $html);

    // Listas desordenadas
    $html = preg_replace('/^- (.+)$/m', '<ul><li>$1</li></ul>', $html);
    $html = preg_replace('/^  - (.+)$/m', '<ul><ul><li>$1</li></ul></ul>', $html);
    $html = preg_replace('/<\/ul>\n<ul>/', '', $html); // Unir listas consecutivas

    // Listas ordenadas
    $html = preg_replace('/^\d+\. (.+)$/m', '<ol><li>$1</li></ol>', $html);
    $html = preg_replace('/<\/ol>\n<ol>/', '', $html);

    // Tablas (básico)
    $html = preg_replace_callback('/\|(.+)\|\n\|[-:\| ]+\|\n((?:\|.+\|\n?)+)/m', function($matches) {
        $header = '<tr>' . preg_replace('/\|(.+?)\|/', '<th>$1</th>', $matches[1]) . '</tr>';
        $rows = preg_replace('/\|(.+?)\|/m', '<td>$1</td>', $matches[2]);
        $rows = '<tr>' . str_replace("\n", '</tr><tr>', trim($rows)) . '</tr>';
        return '<table class="no-break">' . $header . $rows . '</table>';
    }, $html);

    // Líneas horizontales
    $html = preg_replace('/^---$/m', '<hr>', $html);

    // Alertas (custom)
    $html = preg_replace('/⚠️ \*\*(.+?)\*\*:(.+)$/m', '<div class="alert alert-warning no-break"><strong>⚠️ $1:</strong>$2</div>', $html);

    // Párrafos (líneas que no son otros elementos)
    $html = preg_replace('/^(?!<)(.+)$/m', '<p>$1</p>', $html);

    // Limpiar tags de párrafo en elementos de bloque
    $html = preg_replace('/<p>(<h[1-6]|<\/h[1-6]|<table|<\/table|<ul|<\/ul|<ol|<\/ol|<pre|<\/pre|<div|<\/div|<hr>)/i', '$1', $html);
    $html = preg_replace('/(<\/h[1-6]>|<\/table>|<\/ul>|<\/ol>|<\/pre>|<\/div>|<hr>)<\/p>/i', '$1', $html);

    // Saltos de línea
    $html = nl2br($html);

    // Parte 2: Manual de Usuario (separador visual)
    $html = str_replace(
        '<h1>PARTE 2: MANUAL DE USUARIO</h1>',
        '<div class="parte-titulo"><h1>PARTE 2<br>MANUAL DE USUARIO</h1></div>',
        $html
    );

    return $html;
}

/**
 * Estima el número de páginas basado en el tamaño del contenido
 *
 * @param int $contentLength
 * @return int
 */
function estimatePages($contentLength) {
    // Estimación: ~3000 caracteres por página
    return max(1, ceil($contentLength / 3000));
}
