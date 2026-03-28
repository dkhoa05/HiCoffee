<?php

require_once __DIR__ . '/includes/init.php';

$q = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header('Location: ' . app_url('shop/order.php') . $q);
exit;
