<?php 

$partialPath = dirname(__FILE__) . '/partials/sorteo_form.php';
$content = '';

if (is_file($partialPath)) {
    ob_start();
    include $partialPath;
    $content = ob_get_clean();
}

include dirname(__FILE__) . '/../base.php'; 
