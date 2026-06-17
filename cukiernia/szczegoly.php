<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/BazaDanych.php';
require_once __DIR__ . '/classes/Ciasto.php';
require_once __DIR__ . '/classes/Koszyk.php';

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$ciastoModel = new Ciasto($polaczenie);
$koszyk = new Koszyk();

$id = (int)($_GET['id'] ?? 0);
$wynik = $id > 0 ? $ciastoModel->pobierzSzczegoly($id) : false;
$dane = ($wynik && mysqli_num_rows($wynik) > 0) ? mysqli_fetch_assoc($wynik) : null;

if (!$dane) {
    include __DIR__ . '/includes/header.php';
    echo '<section class="szczegoly-produktu"><div class="produkt-opis"><h2>Nie znaleziono ciasta</h2><p>Wybrane ciasto nie istnieje lub zostało ukryte.</p></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit();
}

if (isset($_POST['dodaj_koszyk'])) {
    $ilosc = max(1, (int)($_POST['ilosc'] ?? 1));
    $koszyk->dodaj($id, $ilosc);
    header('Location: ' . app_url('koszyk.php'));
    exit();
}

$skladniki = $ciastoModel->pobierzSkladniki($id);
$alergeny = $ciastoModel->pobierzAlergeny($id);

include __DIR__ . '/includes/header.php';
?>

<section class="szczegoly-produktu">
    <div class="produkt-zdjecie">
        <img src="<?php echo h(cake_image($dane)); ?>" alt="<?php echo h($dane['nazwa']); ?>">
    </div>

    <div class="produkt-opis">
        <h2><?php echo h(mb_strtoupper($dane['nazwa'], 'UTF-8')); ?></h2>
        <p class="cena"><?php echo money($dane['cena']); ?></p>

        <p class="opis">
            <?php echo nl2br(h($dane['opis'])); ?>
        </p>

        <div class="info-produktu">
            <p><strong>Kategoria:</strong> <?php echo h($dane['kategoria']); ?></p>

            <p><strong>Składniki:</strong>
                <?php
                if (!empty($skladniki)) {
                    $lista = [];
                    foreach ($skladniki as $s) {
                        $lista[] = h($s['nazwa']) . ' (' . rtrim(rtrim(number_format((float)$s['ilosc'], 2, '.', ''), '0'), '.') . ' ' . h($s['jednostka']) . ')';
                    }
                    echo implode(', ', $lista);
                } else {
                    echo 'brak danych';
                }
                ?>
            </p>

            <p><strong>Alergeny:</strong>
                <?php
                if ($alergeny && mysqli_num_rows($alergeny) > 0) {
                    $lista = [];
                    while ($a = mysqli_fetch_assoc($alergeny)) {
                        $lista[] = h($a['nazwa']);
                    }
                    echo implode(', ', $lista);
                } else {
                    echo 'brak danych';
                }
                ?>
            </p>

            <p><strong>Waga:</strong> <?php echo (int)$dane['waga']; ?> g</p>
            <p><strong>Czas przygotowania:</strong> <?php echo (int)$dane['czas_przygotowania']; ?> min</p>
        </div>

        <form action="" method="post" class="koszyk-form">
            <div class="ilosc">
                <button type="button" onclick="var i=this.parentNode.querySelector('input'); i.stepDown(); if(i.value<1)i.value=1;">-</button>
                <input type="number" name="ilosc" value="1" min="1">
                <button type="button" onclick="var i=this.parentNode.querySelector('input'); i.stepUp();">+</button>
            </div>

            <button type="submit" name="dodaj_koszyk" class="dodaj-koszyk">
                Dodaj do koszyka
            </button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
