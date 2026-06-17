<?php

$zalogowanyKlient = !empty($_SESSION['klient_id']);
$zalogowanyAdmin = !empty($_SESSION['admin_logged']);

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cukrówka</title>

    <link rel="icon" type="image/png" href="<?php echo app_url('images/logo.png'); ?>">
    <link rel="stylesheet" href="<?php echo app_url('css/style.css'); ?>?v=<?php echo time(); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="header-container">
        <a href="<?php echo app_url('index.php'); ?>" class="logo-section">
            <img src="<?php echo app_url('images/logo.png'); ?>" alt="Logo Cukrówka" class="logo">
            <h1>Cukrówka</h1>
        </a>

        <div class="icons">
            <a href="https://www.facebook.com/profile.php?id=61585849611642" target="_blank" title="Facebook" rel="noreferrer">
                <i class="fa-brands fa-facebook-f"></i>
            </a>

            <a href="https://www.instagram.com/_cukrowka_/#" target="_blank" title="Instagram" rel="noreferrer">
                <i class="fa-brands fa-instagram"></i>
            </a>

            <?php if ($zalogowanyKlient): ?>
                <a href="<?php echo app_url('moje_konto.php'); ?>" title="Moje konto">
                    <i class="fa-regular fa-user"></i>
                </a>
            <?php elseif ($zalogowanyAdmin): ?>
                <a href="<?php echo app_url('admin/panel.php'); ?>" title="Panel administratora">
                    <i class="fa-solid fa-user-shield"></i>
                </a>
            <?php else: ?>
                <a href="<?php echo app_url('logowanie.php'); ?>" title="Logowanie">
                    <i class="fa-regular fa-user"></i>
                </a>
            <?php endif; ?>

            <a href="<?php echo app_url('koszyk.php'); ?>" title="Koszyk">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>

            <?php if ($zalogowanyKlient || $zalogowanyAdmin): ?>
                <a href="<?php echo app_url('wylogowanie.php'); ?>" title="Wyloguj">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php include __DIR__ . '/menu.php'; ?>

<main class="main-content">
