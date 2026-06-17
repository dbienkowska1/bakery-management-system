<?php
require_once __DIR__ . '/../config/config.php';

if (!empty($_SESSION['admin_logged'])) {
    header('Location: ' . app_url('admin/panel.php'));
    exit();
}

header('Location: ' . app_url('admin/login.php'));
exit();
