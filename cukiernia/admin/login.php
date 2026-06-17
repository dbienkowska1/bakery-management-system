<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

if (!empty($_SESSION['admin_logged'])) {
    header('Location: ' . app_url('admin/panel.php'));
    exit();
}

$auth = new Auth();
$bledy = [];

if (isset($_POST['zaloguj'])) {
    $login = trim($_POST['login'] ?? '');
    $haslo = (string)($_POST['haslo'] ?? '');

    if ($auth->zaloguj($login, $haslo)) {
        header('Location: ' . app_url('admin/panel.php'));
        exit();
    }

    $bledy[] = 'Błędny login lub hasło.';
}

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Logowanie administratora</h2>

    <?php if (!empty($bledy)): ?>
        <div class="komunikat komunikat-blad">
            <?php foreach ($bledy as $blad): ?>
                <p><?php echo h($blad); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="admin-form" method="post">
        <label for="login">Login</label>
        <input type="text" id="login" name="login" required value="<?php echo h($_POST['login'] ?? ''); ?>">

        <label for="haslo">Hasło</label>
        <input type="password" id="haslo" name="haslo" required>

        <button type="submit" class="admin-btn" name="zaloguj">Zaloguj</button>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
