<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/BazaDanych.php';
require_once __DIR__ . '/classes/AuthKlient.php';

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$authKlient = new AuthKlient($polaczenie);
$bledy = [];

if (isset($_POST['zaloguj'])) {
    $email = trim($_POST['email'] ?? '');
    $haslo = (string)($_POST['haslo'] ?? '');

    if ($authKlient->zaloguj($email, $haslo)) {
        header('Location: ' . app_url('index.php'));
        exit();
    }

    $bledy[] = 'Nieprawidłowy e-mail lub hasło.';
}

include __DIR__ . '/includes/header.php';
?>

<section class="logowanie-container">
    <div class="logowanie-box">
        <h2>Logowanie</h2>

        <?php if (!empty($bledy)): ?>
            <div class="komunikat komunikat-blad">
                <?php foreach ($bledy as $blad): ?>
                    <p><?php echo h($blad); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Adres e-mail</label>
            <input type="email" name="email" required value="<?php echo h($_POST['email'] ?? ''); ?>">

            <label>Hasło</label>
            <input type="password" name="haslo" required>

            <button type="submit" class="btn-logowanie" name="zaloguj">Zaloguj się</button>
        </form>

        <p class="rejestracja-link">
            Nie masz jeszcze konta?
            <a href="<?php echo app_url('rejestracja.php'); ?>">Zarejestruj się</a>
        </p>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
