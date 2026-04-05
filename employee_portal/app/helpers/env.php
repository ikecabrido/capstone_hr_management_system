<?php
function env($key, $default = null) {
    static $env = null;

    if ($env === null) {
        $env = [];
        $lines = @file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines) {
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                [$name, $value] = array_map('trim', explode('=', $line, 2) + [1 => null]);
                if ($name && $value !== null) {
                    $env[$name] = $value;
                }
            }
        }
    }

    return $env[$key] ?? $default;
}