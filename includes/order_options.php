<?php

declare(strict_types=1);

/** @return array<string, string> */
function order_size_choices(): array
{
    return [
        'S' => 'S',
        'M' => 'M',
        'L' => 'L',
        'XL' => 'XL',
    ];
}

/** @return array<string, string> */
function order_sweetness_choices(): array
{
    return [
        'vua' => 'Vừa',
        'du' => 'Đủ',
        'nhieu' => 'Nhiều',
    ];
}

/** @return array<string, string> */
function order_ice_choices(): array
{
    return [
        'it' => 'Ít đá',
        'nhieu' => 'Nhiều đá',
        'rieng' => 'Riêng (mang về)',
    ];
}

function order_status_label(string $status): string
{
    return match ($status) {
        'Pending' => 'Trong giỏ',
        'Placed' => 'Chờ duyệt',
        'Completed' => 'Đã hoàn thành',
        'Cancelled' => 'Đã hủy',
        default => $status,
    };
}

function order_normalize_size(string $v): string
{
    $v = strtoupper($v);
    return in_array($v, ['S', 'M', 'L', 'XL'], true) ? $v : 'M';
}

function order_normalize_sweetness(string $v): string
{
    return in_array($v, ['vua', 'du', 'nhieu'], true) ? $v : 'vua';
}

function order_normalize_ice(string $v): string
{
    return in_array($v, ['it', 'nhieu', 'rieng'], true) ? $v : 'it';
}

/** @param array<string, mixed> $row order_items row + optional keys */
function order_item_options_line(array $row): string
{
    if (!array_key_exists('size', $row) || $row['size'] === null || $row['size'] === '') {
        return '';
    }

    $size = htmlspecialchars((string) ($row['size'] ?? 'M'));
    $sw = order_sweetness_choices()[(string) ($row['sweetness'] ?? 'vua')] ?? (string) ($row['sweetness'] ?? '');
    $ice = order_ice_choices()[(string) ($row['ice_level'] ?? 'it')] ?? (string) ($row['ice_level'] ?? '');
    $sw = htmlspecialchars($sw);
    $ice = htmlspecialchars($ice);
    $parts = ["Size {$size}", "Độ ngọt: {$sw}", "Đá: {$ice}"];
    $note = trim((string) ($row['item_note'] ?? ''));
    if ($note !== '') {
        $parts[] = 'Ghi chú: ' . htmlspecialchars($note);
    }
    return implode(' · ', $parts);
}
