<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/BazaDanych.php';
require_once __DIR__ . '/classes/Klient.php';

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$klientModel = new Klient($polaczenie);

$bledy = [];

if (isset($_POST['zarejestruj'])) {
    $imie = trim($_POST['imie'] ?? '');
    $nazwisko = trim($_POST['nazwisko'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $haslo = (string)($_POST['haslo'] ?? '');
    $haslo2 = (string)($_POST['haslo2'] ?? '');
    $regulamin = !empty($_POST['regulamin']);

    if ($imie === '' || $nazwisko === '' || $telefon === '' || $email === '' || $haslo === '' || $haslo2 === '') {
        $bledy[] = 'Wypełnij wszystkie pola.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $bledy[] = 'Podaj prawidłowy adres e-mail.';
    }
    if (strlen($haslo) < 8) {
        $bledy[] = 'Hasło powinno mieć co najmniej 8 znaków.';
    }
    if ($haslo !== $haslo2) {
        $bledy[] = 'Hasła nie są takie same.';
    }
    if (!$regulamin) {
        $bledy[] = 'Musisz zaakceptować regulamin.';
    }

    if (empty($bledy)) {
        $istnieje = $klientModel->pobierzPoEmail($email);
        if ($istnieje && mysqli_num_rows($istnieje) > 0) {
            $bledy[] = 'Konto z takim adresem e-mail już istnieje.';
        } else {
            $id = $klientModel->dodaj($imie, $nazwisko, $telefon, $email, $haslo);
            if ($id) {
                unset(
                    $_SESSION['admin_logged'],
                    $_SESSION['admin_login']
                );

                $_SESSION['klient_id'] = (int)$id;
                $_SESSION['klient_imie'] = $imie;
                $_SESSION['klient_nazwisko'] = $nazwisko;
                $_SESSION['klient_email'] = $email;
                $_SESSION['klient_telefon'] = $telefon;

                header('Location: ' . app_url('moje_konto.php?status=rejestracja_ok'));
                exit();
            } else {
                $bledy[] = 'Nie udało się utworzyć konta.';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="rejestracja-container">
    <div class="rejestracja-box">
        <h2>Rejestracja konta</h2>

        <p class="info">Obowiązkowe pola oznaczone są symbolem *</p>

        <?php if (!empty($bledy)): ?>
            <div class="komunikat komunikat-blad">
                <?php foreach ($bledy as $blad): ?>
                    <p><?php echo h($blad); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>*Imię</label>
            <input type="text" name="imie" required value="<?php echo h($_POST['imie'] ?? ''); ?>">

            <label>*Nazwisko</label>
            <input type="text" name="nazwisko" required value="<?php echo h($_POST['nazwisko'] ?? ''); ?>">

            <label>*Telefon</label>
            <input type="text" name="telefon" required value="<?php echo h($_POST['telefon'] ?? ''); ?>">

            <label>*Adres e-mail</label>
            <input type="email" name="email" required value="<?php echo h($_POST['email'] ?? ''); ?>">

            <label>*Utwórz hasło</label>
            <input type="password" name="haslo" required minlength="8">

            <label>*Powtórz hasło</label>
            <input type="password" name="haslo2" required minlength="8">

            <label class="checkbox-label">
                <input type="checkbox" name="regulamin" required>
                <span>Zapoznałem się z regulaminem sklepu internetowego i akceptuję jego treść.</span>
            </label>

            <button type="submit" class="btn-rejestracja" name="zarejestruj">Załóż konto</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
