<?php
require_once __DIR__ . '/includes/config.php';

$token = $_GET['t'] ?? '';
if ($token === '') {
    http_response_code(400);
    echo 'Missing token';
    exit;
}

$payload = verifyUrlToken($token);
if ($payload === null) {
    http_response_code(403);
    echo 'Invalid or expired token';
    exit;
}

$path = (string) ($payload['p'] ?? '');
// Disallow absolute external URLs to prevent open-redirects
if (preg_match('#^https?://#i', $path) || strpos($path, '//') !== false) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

// Ensure path is a root-relative path
if ($path === '' || $path[0] !== '/') {
    $path = '/' . ltrim($path, '/');
}

$redirect = APP_PUBLIC_URL . appUrl($path);
header('Location: ' . $redirect);
exit;
