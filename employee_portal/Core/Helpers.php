<?php
function view($viewPath, $data = [])
{
    $file = __DIR__ . '/../views/' . str_replace('.', '/', $viewPath) . '.php';
    if (!file_exists($file)) die("View {$file} not found");

    extract($data); 
    require $file;
}