<?php
require_once __DIR__ . '/config/config.php';

unset(
    $_SESSION['klient_id'],
    $_SESSION['klient_imie'],
    $_SESSION['klient_nazwisko'],
    $_SESSION['klient_email'],
    $_SESSION['klient_telefon'],
    $_SESSION['admin_logged'],
    $_SESSION['admin_login'],
    $_SESSION['koszyk']
);

$_SESSION = [];
session_destroy();

include __DIR__ . '/includes/header.php';
?>

<section class="wylogowanie">
    <h2>Zostałeś wylogowany</h2>
    <p>Dziękujemy za odwiedzenie cukierni Cukrówka.</p>

    <a href="<?php echo app_url('index.php'); ?>" class="btn-powrot">Powrót do strony głównej</a>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
