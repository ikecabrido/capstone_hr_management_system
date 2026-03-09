<?php

class View
{
    public static function render($view, $data = [])
    {
        extract($data);

        // Capture view output
        ob_start();
        require_once __DIR__ . "/../views/$view.php";
        $content = ob_get_clean();

        // Load layout
        require_once __DIR__ . "/../views/partials/layout.php";
    }
}



