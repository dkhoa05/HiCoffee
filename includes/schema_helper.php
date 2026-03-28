<?php

function products_has_category_column(PDO $conn): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $conn->query('SELECT category FROM products LIMIT 1');
        $cache = true;
    } catch (PDOException $e) {
        $cache = false;
    }

    return $cache;
}

/** Cột size / sweetness / ice_level / item_note trên order_items (sau khi chạy SQL migration). */
function order_items_has_option_columns(PDO $conn): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $conn->query('SELECT `size`, `sweetness`, `ice_level`, `item_note` FROM order_items LIMIT 1');
        $cache = true;
    } catch (PDOException $e) {
        $cache = false;
    }

    return $cache;
}

function order_items_has_confirmed_received(PDO $conn): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $conn->query('SELECT confirmed_received FROM order_items LIMIT 1');
        $cache = true;
    } catch (PDOException $e) {
        $cache = false;
    }

    return $cache;
}

function orders_has_cancel_reason(PDO $conn): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $conn->query('SELECT cancel_reason FROM orders LIMIT 1');
        $cache = true;
    } catch (PDOException $e) {
        $cache = false;
    }

    return $cache;
}

/** Bảng đánh giá sản phẩm (migration). */
function product_reviews_table_exists(PDO $conn): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $conn->query('SELECT 1 FROM product_reviews LIMIT 1');
        $cache = true;
    } catch (PDOException $e) {
        $cache = false;
    }

    return $cache;
}

function comments_has_contact_columns(PDO $conn): bool
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $conn->query('SELECT phone, email FROM comments LIMIT 1');
        $cache = true;
    } catch (PDOException $e) {
        $cache = false;
    }

    return $cache;
}
