<?php

$file = 'E:\xampp8.20\htdocs\cleanme\resources\views\admin\landing\index.blade.php';
$content = file_get_contents($file);

$newPricingTab = file_get_contents('E:\xampp8.20\htdocs\cleanme\storage\app\temp_pricing_tab.txt');

// Find and replace between the pricing tab markers
$pattern = '/(<!-- Pricing Calculator Tab -->)(.*?)(<!-- End Pricing Tab -->)/s';
$replacement = $newPricingTab;

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);

echo "Pricing tab replaced successfully!\n";
