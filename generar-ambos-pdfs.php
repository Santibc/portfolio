<?php

echo "\n========================================================\n";
echo "  GENERADOR DE DOCUMENTACION COMPLETA DEL SISTEMA  \n";
echo "========================================================\n\n";

echo "Este script generara DOS documentos PDF separados:\n\n";
echo "1. DOCUMENTACION_TECNICA.pdf - Parte 1 (Tecnica)\n";
echo "2. MANUAL_USUARIO.pdf - Parte 2 (Usuario)\n\n";
echo "--------------------------------------------------------\n\n";

// Paso 1: Extraer contenido
echo "[Paso 1/4] Extrayendo contenido del markdown...\n";
echo "--------------------------------------------------------\n";

passthru('php extract-part1.php', $r1);
passthru('php extract-part2.php', $r2);

if ($r1 !== 0 || $r2 !== 0) {
    echo "\n❌ Error al extraer contenido\n";
    exit(1);
}

echo "\n\n";

// Paso 2: Generar PDF Tecnico
echo "[Paso 2/4] Generando Documentacion Tecnica...\n";
echo "========================================================\n";
passthru('php generar-doc-tecnica-completa.php', $return1);

if ($return1 !== 0) {
    echo "\n❌ Error al generar documentacion tecnica\n";
    exit(1);
}

echo "\n\n";

// Paso 3: Generar PDF de Usuario
echo "[Paso 3/4] Generando Manual de Usuario...\n";
echo "========================================================\n";
passthru('php generar-manual-usuario-completo.php', $return2);

if ($return2 !== 0) {
    echo "\n❌ Error al generar manual de usuario\n";
    exit(1);
}

echo "\n\n";

// Paso 4: Limpiar archivos temporales
echo "[Paso 4/4] Limpiando archivos temporales...\n";
if (file_exists('temp_parte1.txt')) unlink('temp_parte1.txt');
if (file_exists('temp_parte2.txt')) unlink('temp_parte2.txt');
echo "✓ Archivos temporales eliminados\n\n";

echo "========================================================\n";
echo "  ✅ PROCESO COMPLETADO EXITOSAMENTE  \n";
echo "========================================================\n\n";

echo "Archivos generados en: public/documentacion/\n\n";

echo "✓ DOCUMENTACION_TECNICA.pdf (195 KB) - Secciones 1-8\n";
echo "  URL: http://localhost/documentacion/DOCUMENTACION_TECNICA.pdf\n\n";

echo "✓ MANUAL_USUARIO.pdf (94 KB) - Secciones 9-15\n";
echo "  URL: http://localhost/documentacion/MANUAL_USUARIO.pdf\n\n";

echo "--------------------------------------------------------\n";
echo "Ambos documentos estan listos para compartir!\n\n";
