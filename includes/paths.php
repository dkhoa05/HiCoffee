<?php

declare(strict_types=1);

/**
 * Phần đường dẫn URL của project trong htdocs (ví dụ: /WebPHP hoặc rỗng nếu vhost trỏ thẳng vào thư mục).
 */
if (!defined('HI_BASE_URL')) {
    $documentRoot = @realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $projectRoot = @realpath(__DIR__ . '/..');
    $base = '';
    if ($documentRoot && $projectRoot && strncmp($projectRoot, $documentRoot, strlen($documentRoot)) === 0) {
        $base = substr($projectRoot, strlen($documentRoot));
        $base = str_replace('\\', '/', (string) $base);
    }
    define('HI_BASE_URL', rtrim($base, '/'));
}

/**
 * Đường dẫn từ gốc domain: /WebPHP/shop/cart.php hoặc /shop/cart.php
 */
function app_url(string $path = ''): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    $b = HI_BASE_URL;
    if ($path === '') {
        return $b === '' ? '/' : $b . '/';
    }

    return ($b === '' ? '' : $b . '/') . $path;
}
