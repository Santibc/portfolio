<?php

echo "PDFs en documentacion:\n\n";

$dir = 'c:/xampp/htdocs/portfolio/public/documentacion';
$files = glob($dir . '/*.pdf');

foreach ($files as $file) {
    $size = round(filesize($file) / 1024, 2);
    echo basename($file) . " - " . $size . " KB\n";
}

echo "\nTotal: " . count($files) . " archivos\n";
