<?php

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . app_url('auth/login.php'));
    exit;
}
