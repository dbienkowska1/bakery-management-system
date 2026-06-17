<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/BazaDanych.php';
require_once __DIR__ . '/classes/Ciasto.php';
require_once __DIR__ . '/classes/Kategoria.php';
require_once __DIR__ . '/classes/Koszyk.php';

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$ciasto = new Ciasto($polaczenie);
$kategoria = new Kategoria($polaczenie);
$koszyk = new Koszyk();

$sortowanie = $_GET['sort'] ?? 'id_asc';
$idKategorii = (int)($_GET['kategoria'] ?? 0);

if (isset($_GET['dodaj'])) {
    $idDodawany = (int)$_GET['dodaj'];
    $sprawdz = $ciasto->pobierzSzczegoly($idDodawany);

    if ($sprawdz && mysqli_num_rows($sprawdz) === 1) {
        $koszyk->dodaj($idDodawany);
    }

    header('Location: ' . app_url('koszyk.php'));
    exit();
}

$wynik = $ciasto->pobierzFiltrowane($sortowanie, $idKategorii);
$kategorie = $kategoria->pobierzWszystkie();

include __DIR__ . '/includes/header.php';
?>

<section class="oferta">
    <h2>NASZE KULTOWE PRODUKTY</h2>

    <p class="oferta-opis">
        Poznaj nasze wypieki. Każdy produkt powstaje z najlepszych składników i
        może być filtrowany po kategorii oraz sortowany według Twoich preferencji.
    </p>

    <form method="get" class="filtry-oferta">
        <div class="filtr-grupa">
            <label for="kategoria">Kategoria</label>
            <select name="kategoria" id="kategoria">
                <option value="0">Wszystkie</option>
                <?php while ($kat = mysqli_fetch_assoc($kategorie)): ?>
                    <option value="<?php echo (int)$kat['id_kategorii']; ?>" <?php echo $idKategorii === (int)$kat['id_kategorii'] ? 'selected' : ''; ?>>
                        <?php echo h($kat['nazwa']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="filtr-grupa">
            <label for="sort">Sortowanie</label>
            <select name="sort" id="sort">
    <option value="id_asc" <?php echo $sortowanie === 'id_asc' ? 'selected' : ''; ?>>ID rosnąco</option>

    <option value="nazwa_asc" <?php echo $sortowanie === 'nazwa_asc' ? 'selected' : ''; ?>>
        Nazwa A-Z
    </option>

    <option value="nazwa_desc" <?php echo $sortowanie === 'nazwa_desc' ? 'selected' : ''; ?>>
        Nazwa Z-A
    </option>

    <option value="cena_asc" <?php echo $sortowanie === 'cena_asc' ? 'selected' : ''; ?>>
        Cena rosnąco
    </option>

    <option value="cena_desc" <?php echo $sortowanie === 'cena_desc' ? 'selected' : ''; ?>>
        Cena malejąco
    </option>
</select>
        </div>

        <button type="submit" class="btn-filtruj">Filtruj</button>
    </form>

    <div class="produkty">
        <?php if ($wynik && mysqli_num_rows($wynik) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($wynik)): ?>
                <div class="produkt">
                    <img src="<?php echo h(cake_image($row)); ?>" alt="<?php echo h($row['nazwa']); ?>">
                    <div class="produkt-info">
                        <h3><?php echo h(mb_strtoupper($row['nazwa'], 'UTF-8')); ?></h3>
                        <p><?php echo h(number_format((float)$row['cena'], 2, '.', '')); ?> zł</p>
                    </div>
                    <div class="produkt-akcje">
                        <span class="produkt-status"><?php echo h($row['kategoria']); ?></span>
                        <a class="btn-maly" href="<?php echo app_url('oferta.php?dodaj=' . (int)$row['id_ciasta']); ?>">Do koszyka</a>
                        <a class="btn-maly btn-outline" href="<?php echo app_url('szczegoly.php?id=' . (int)$row['id_ciasta']); ?>">Szczegóły</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Brak ciast do wyświetlenia.</p>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
