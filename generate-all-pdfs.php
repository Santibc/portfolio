<?php

echo "\n========================================================\n";
echo "  Generador de Documentación Completa del Sistema  \n";
echo "========================================================\n\n";

echo "Este script generará DOS documentos PDF separados:\n\n";
echo "1. DOCUMENTACION_TECNICA.pdf - Parte 1 (Técnica)\n";
echo "2. MANUAL_USUARIO.pdf - Parte 2 (Usuario)\n\n";
echo "--------------------------------------------------------\n\n";

// Eliminar PDF antiguo si existe
$oldPdf = __DIR__ . '/public/documentacion/DOCUMENTACION_SISTEMA.pdf';
if (file_exists($oldPdf)) {
    echo "→ Eliminando PDF antiguo...\n";
    unlink($oldPdf);
    echo "✓ PDF antiguo eliminado\n\n";
}

// Generar PDF Técnico
echo "[1/2] Generando Documentación Técnica...\n";
echo "========================================================\n";
passthru('php generate-pdf-tecnico.php', $return1);

if ($return1 !== 0) {
    echo "\n❌ Error al generar documentación técnica\n";
    exit(1);
}

echo "\n\n";

// Generar PDF de Usuario
echo "[2/2] Generando Manual de Usuario...\n";
echo "========================================================\n";
passthru('php generate-pdf-usuario.php', $return2);

if ($return2 !== 0) {
    echo "\n❌ Error al generar manual de usuario\n";
    exit(1);
}

echo "\n\n";
echo "========================================================\n";
echo "  ✅ PROCESO COMPLETADO EXITOSAMENTE  \n";
echo "========================================================\n\n";

echo "Archivos generados en: public/documentacion/\n\n";

echo "✓ DOCUMENTACION_TECNICA.pdf\n";
echo "  URL: http://localhost/documentacion/DOCUMENTACION_TECNICA.pdf\n\n";

echo "✓ MANUAL_USUARIO.pdf\n";
echo "  URL: http://localhost/documentacion/MANUAL_USUARIO.pdf\n\n";

echo "--------------------------------------------------------\n";
echo "Ambos documentos están listos para compartir!\n\n";
