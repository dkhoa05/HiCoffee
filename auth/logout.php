<?php
require_once dirname(__DIR__) . '/includes/init.php';

session_unset();
session_destroy();

header('Location: ' . app_url('auth/login.php'));
exit;
