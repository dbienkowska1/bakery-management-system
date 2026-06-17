<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/BazaDanych.php';
require_once __DIR__ . '/classes/Koszyk.php';

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$koszyk = new Koszyk();

if (isset($_GET['usun'])) {
    $koszyk->usun((int)$_GET['usun']);
    header('Location: ' . app_url('koszyk.php'));
    exit();
}
if (isset($_GET['plus'])) {
    $koszyk->zwieksz((int)$_GET['plus']);
    header('Location: ' . app_url('koszyk.php'));
    exit();
}
if (isset($_GET['minus'])) {
    $koszyk->zmniejsz((int)$_GET['minus']);
    header('Location: ' . app_url('koszyk.php'));
    exit();
}
if (isset($_GET['wyczysc'])) {
    $koszyk->wyczysc();
    header('Location: ' . app_url('koszyk.php'));
    exit();
}
if (isset($_POST['aktualizuj']) && !empty($_POST['ilosc']) && is_array($_POST['ilosc'])) {
    foreach ($_POST['ilosc'] as $idCiasta => $ilosc) {
        $koszyk->ustawIlosc((int)$idCiasta, (int)$ilosc);
    }
    header('Location: ' . app_url('koszyk.php'));
    exit();
}

$pozycje = $koszyk->pobierzPozycje($polaczenie);
$suma = $koszyk->wyliczSume($polaczenie);

include __DIR__ . '/includes/header.php';
?>

<section class="koszyk">
    <div class="koszyk-lewa">
        <div class="koszyk-naglowek">
            <p>Produkt</p>
            <p>Cena</p>
            <p>Ilość</p>
            <p>Kwota</p>
        </div>

        <?php if (empty($pozycje)): ?>
            <div class="koszyk-produkt">
                <p>Twój koszyk jest pusty.</p>
            </div>
        <?php else: ?>
            <form method="post">
                <?php foreach ($pozycje as $produkt): ?>
                    <div class="koszyk-produkt">
                        <a href="<?php echo app_url('koszyk.php?usun=' . (int)$produkt['id_ciasta']); ?>" class="usun" title="Usuń">×</a>

                        <img src="<?php echo h(cake_image($produkt)); ?>" alt="<?php echo h($produkt['nazwa']); ?>">

                        <p class="nazwa"><?php echo h(mb_strtoupper($produkt['nazwa'], 'UTF-8')); ?></p>
                        <p><?php echo money($produkt['cena']); ?></p>

                        <div class="ilosc">
                            <a href="<?php echo app_url('koszyk.php?minus=' . (int)$produkt['id_ciasta']); ?>">−</a>
                            <input type="number" name="ilosc[<?php echo (int)$produkt['id_ciasta']; ?>]" value="<?php echo (int)$produkt['ilosc']; ?>" min="1">
                            <a href="<?php echo app_url('koszyk.php?plus=' . (int)$produkt['id_ciasta']); ?>">+</a>
                        </div>

                        <p><?php echo money((float)$produkt['cena'] * (int)$produkt['ilosc']); ?></p>
                    </div>
                <?php endforeach; ?>

                <div class="koszyk-akcje">
                    <a href="<?php echo app_url('koszyk.php?wyczysc=1'); ?>" class="btn-powrot">Wyczyść koszyk</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <div class="koszyk-prawa">
        <h2>Podsumowanie koszyka</h2>

        <div class="podsumowanie">
            <strong>Łącznie</strong>
            <span><?php echo money($suma); ?></span>
        </div>

        <p class="dostawa">
            Odbierz towar w którymś z naszych lokali lub zamów z dostawą już od 18.50 zł.
        </p>

        <a href="<?php echo app_url('zamowienie.php'); ?>" class="btn-zamowienie">Przejdź do zamówienia</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
