<?php
require_once __DIR__ . '/../config/config.php';

unset(
    $_SESSION['admin_logged'],
    $_SESSION['admin_login'],
    $_SESSION['klient_id'],
    $_SESSION['klient_imie'],
    $_SESSION['klient_nazwisko'],
    $_SESSION['klient_email'],
    $_SESSION['klient_telefon']
);

$_SESSION = [];
session_destroy();

header('Location: ' . app_url('admin/login.php'));
exit();
