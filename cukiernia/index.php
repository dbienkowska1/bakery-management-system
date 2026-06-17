<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/BazaDanych.php';
require_once __DIR__ . '/classes/Ciasto.php';
require_once __DIR__ . '/classes/Kategoria.php';

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$ciasto = new Ciasto($polaczenie);
$kategoria = new Kategoria($polaczenie);

$promowane = $ciasto->pobierzPromowane(3);
$kategorie = $kategoria->pobierzWszystkie();

include __DIR__ . '/includes/header.php';
?>

<section class="oferta">
    <h2>Witamy w Cukrówce</h2>

    <p class="oferta-opis">
        Ręcznie przygotowywane ciasta, torty i desery z najlepszych składników.
        Przejrzyj nasze kategorie i zobacz promowane wypieki.
    </p>

    <h3 style="color:#8b6f4e; font-weight:500; margin-bottom: 20px;">Kategorie</h3>
    <div class="kategorie-list">
        <?php while ($kat = mysqli_fetch_assoc($kategorie)): ?>
            <a class="kategoria-chip" href="<?php echo app_url('oferta.php?kategoria=' . (int)$kat['id_kategorii']); ?>">
                <?php echo h($kat['nazwa']); ?>
            </a>
        <?php endwhile; ?>
    </div>

    <h2 style="margin-top: 60px;">Nasze kultowe produkty</h2>

    <div class="produkty">
        <?php while ($row = mysqli_fetch_assoc($promowane)): ?>
            <div class="produkt">
                <img src="<?php echo h(cake_image($row)); ?>" alt="<?php echo h($row['nazwa']); ?>">
                <div class="produkt-info">
                    <h3><?php echo h(mb_strtoupper($row['nazwa'], 'UTF-8')); ?></h3>
                    <p><?php echo h(rtrim(rtrim(number_format((float)$row['cena'], 2, '.', ''), '0'), '.')); ?> zł</p>
                </div>
                <div class="produkt-akcje">
                    <a class="btn-maly" href="<?php echo app_url('szczegoly.php?id=' . (int)$row['id_ciasta']); ?>">Szczegóły</a>
                    <a class="btn-maly btn-outline" href="<?php echo app_url('oferta.php?dodaj=' . (int)$row['id_ciasta']); ?>">Do koszyka</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
