<?php
$envFile = 'D:\\Year 4\\S2\\E-COMMERCE\\FRONTEND\\Digital-store\\.env';
error_log("Attempting to load .env from: $envFile");

if (file_exists($envFile)) {
    $env = parse_ini_file($envFile, false);
    if ($env === false) {
        error_log("Failed to parse .env file at: $envFile");
        $env = [];
    }
} else {
    error_log("No .env file found at: $envFile");
    $env = [];
}

define('API_BASE_URL', $env['API_BASE_URL'] ?? 'http://127.0.0.1:8000/api');
?>