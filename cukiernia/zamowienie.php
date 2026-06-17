<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/BazaDanych.php';
require_once __DIR__ . '/classes/Koszyk.php';
require_once __DIR__ . '/classes/Zamowienie.php';
require_once __DIR__ . '/classes/PozycjaZamowienia.php';
require_once __DIR__ . '/classes/Klient.php';

if (empty($_SESSION['klient_id'])) {
    header('Location: ' . app_url('logowanie.php'));
    exit();
}

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$koszyk = new Koszyk();
$zamowienieModel = new Zamowienie($polaczenie);
$pozycjaModel = new PozycjaZamowienia($polaczenie);
$klientModel = new Klient($polaczenie);

if ($koszyk->czyPusty()) {
    include __DIR__ . '/includes/header.php';
    echo '<section class="zamowienie"><h2>Finalizacja zamówienia</h2><p>Twój koszyk jest pusty.</p></section>';
    include __DIR__ . '/includes/footer.php';
    exit();
}

$idKlienta = (int)$_SESSION['klient_id'];

$wynikKlienta = $klientModel->pobierzPoId($idKlienta);
$daneKlienta = ($wynikKlienta && mysqli_num_rows($wynikKlienta) === 1) ? mysqli_fetch_assoc($wynikKlienta) : null;

$pozycje = $koszyk->pobierzPozycje($polaczenie);
$suma = $koszyk->wyliczSume($polaczenie);

$domyslne = [
    'imie' => $daneKlienta['imie'] ?? '',
    'nazwisko' => $daneKlienta['nazwisko'] ?? '',
    'telefon' => $daneKlienta['telefon'] ?? '',
    'email' => $daneKlienta['email'] ?? ''
];

$bledy = [];

if (isset($_POST['zloz_zamowienie'])) {
    $dataOdbioru = trim($_POST['data_odbioru'] ?? '');
    $uwagi = trim($_POST['uwagi'] ?? '');

    if ($dataOdbioru === '') {
        $bledy[] = 'Wybierz datę odbioru.';
    }

    if (empty($bledy)) {
        $idZamowienia = $zamowienieModel->dodaj($idKlienta, $dataOdbioru, (float)$suma, $uwagi);

        if (!$idZamowienia) {
            $bledy[] = 'Nie udało się zapisać zamówienia.';
        } else {
            foreach ($pozycje as $produkt) {
                $pozycjaModel->dodaj(
                    (int)$idZamowienia,
                    (int)$produkt['id_ciasta'],
                    (int)$produkt['ilosc'],
                    (float)$produkt['cena']
                );
            }

            $koszyk->wyczysc();

            header('Location: ' . app_url('moje_konto.php?status=zamowienie_zlozone'));
            exit();
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="zamowienie">
    <h2>Finalizacja zamówienia</h2>

    <?php if (!empty($bledy)): ?>
        <div class="komunikat komunikat-blad">
            <?php foreach ($bledy as $blad): ?>
                <p><?php echo h($blad); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <h3>Pozycje w koszyku</h3>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ciasto</th>
                    <th>Ilość</th>
                    <th>Cena</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pozycje as $produkt): ?>
                    <tr>
                        <td><?php echo h($produkt['nazwa']); ?></td>
                        <td><?php echo (int)$produkt['ilosc']; ?></td>
                        <td><?php echo money((float)$produkt['cena'] * (int)$produkt['ilosc']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-card">
        <h3>Podsumowanie</h3>
        <p><strong>Liczba pozycji:</strong> <?php echo $koszyk->liczbaProduktow(); ?></p>
        <p><strong>Łączna kwota:</strong> <?php echo money($suma); ?></p>
    </div>

    <div class="admin-card">
        <h3>Dane do zamówienia</h3>

        <form action="" method="POST" class="formularz-zamowienia">
            <div class="formularz-grupa">
                <label>Imię</label>
                <input type="text" value="<?php echo h($domyslne['imie']); ?>" disabled>
            </div>

            <div class="formularz-grupa">
                <label>Nazwisko</label>
                <input type="text" value="<?php echo h($domyslne['nazwisko']); ?>" disabled>
            </div>

            <div class="formularz-grupa">
                <label>Telefon</label>
                <input type="text" value="<?php echo h($domyslne['telefon']); ?>" disabled>
            </div>

            <div class="formularz-grupa">
                <label>E-mail</label>
                <input type="text" value="<?php echo h($domyslne['email']); ?>" disabled>
            </div>

            <div class="formularz-grupa">
                <label for="data_odbioru">Data odbioru</label>
                <input type="date" id="data_odbioru" name="data_odbioru" required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="formularz-grupa">
                <label for="uwagi">Uwagi do zamówienia</label>
                <textarea id="uwagi" name="uwagi" rows="4"></textarea>
            </div>

            <button type="submit" name="zloz_zamowienie" class="btn-zamow">Złóż zamówienie</button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
