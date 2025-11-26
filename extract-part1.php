<?php

$content = file_get_contents('c:/xampp/htdocs/portfolio/DOCUMENTACION_SISTEMA.md');

preg_match('/---\s*\n\s*# PARTE 1: DOCUMENTACIÓN TÉCNICA\s*\n\s*---\s*(.*?)---\s*\n\s*# PARTE 2:/s', $content, $matches);

if (isset($matches[1])) {
    $parte1 = trim($matches[1]);
    file_put_contents('c:/xampp/htdocs/portfolio/temp_parte1.txt', $parte1);
    echo "OK - Extraidos " . strlen($parte1) . " caracteres\n";
    echo "Lineas: " . substr_count($parte1, "\n") . "\n";
} else {
    echo "ERROR - No se encontro PARTE 1\n";
}
