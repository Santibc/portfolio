<?php

$content = file_get_contents('c:/xampp/htdocs/portfolio/DOCUMENTACION_SISTEMA.md');

// Extraer SOLO desde "## 9. INTRODUCCIÓN PARA USUARIOS" hasta el final
preg_match('/## 9\. INTRODUCCIÓN PARA USUARIOS(.*?)(?=# FIN DE LA DOCUMENTACIÓN|$)/s', $content, $matches);

if (isset($matches[1])) {
    // Incluir el título "## 9. INTRODUCCIÓN PARA USUARIOS"
    $parte2 = "## 9. INTRODUCCIÓN PARA USUARIOS\n\n" . trim($matches[1]);
    file_put_contents('c:/xampp/htdocs/portfolio/temp_parte2.txt', $parte2);
    echo "OK - Extraidos " . strlen($parte2) . " caracteres\n";
    echo "Lineas: " . substr_count($parte2, "\n") . "\n";
} else {
    echo "ERROR - No se encontro PARTE 2\n";
}
