<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Ciasto.php';
require_once __DIR__ . '/../classes/Kategoria.php';
require_once __DIR__ . '/../classes/Skladnik.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$ciasto = new Ciasto($polaczenie);
$kategoria = new Kategoria($polaczenie);
$skladnikModel = new Skladnik($polaczenie);

$kategorie = $kategoria->pobierzWszystkie();
$skladnikiWszystkie = $skladnikModel->pobierzWszystkie();

$trybEdycji = false;
$daneCiasta = [
    'id_ciasta' => '',
    'nazwa' => '',
    'opis' => '',
    'cena' => '',
    'waga' => '',
    'czas_przygotowania' => '',
    'id_kategorii' => '',
    'dostepnosc' => 1,
    'promowane' => 0,
    'zdjecie' => '',
];

$daneSkladniki = [];

if (isset($_GET['id'])) {
    $trybEdycji = true;
    $wynikCiasta = $ciasto->pobierzPoId((int)$_GET['id']);

    if ($wynikCiasta && mysqli_num_rows($wynikCiasta) === 1) {
        $daneCiasta = mysqli_fetch_assoc($wynikCiasta);
        $daneSkladniki = $ciasto->pobierzSkladniki((int)$daneCiasta['id_ciasta']);
    } else {
        $trybEdycji = false;
    }
}

if (isset($_POST['zapisz'])) {
    $id = (int)($_POST['id_ciasta'] ?? 0);
    $nazwa = trim($_POST['nazwa'] ?? '');
    $opis = trim($_POST['opis'] ?? '');
    $cena = (float)($_POST['cena'] ?? 0);
    $waga = (int)($_POST['waga'] ?? 0);
    $czas = (int)($_POST['czas'] ?? 0);
    $idKategorii = (int)($_POST['id_kategorii'] ?? 0);
    $dostepnosc = (int)($_POST['dostepnosc'] ?? 1);
    $promowane = (int)($_POST['promowane'] ?? 0);
    $zdjecie = trim($_POST['zdjecie'] ?? '');
    $skladniki = $_POST['skladniki'] ?? [];

    if ($id > 0) {
        $ciasto->aktualizuj($id, $nazwa, $opis, $cena, $waga, $czas, $idKategorii, $dostepnosc, $promowane, $zdjecie);
        $idCiasta = $id;
    } else {
        $idCiasta = $ciasto->dodaj($nazwa, $opis, $cena, $waga, $czas, $idKategorii, $dostepnosc, $promowane, $zdjecie);
    }

    if ($idCiasta) {
        $ciasto->zapiszSkladniki((int)$idCiasta, $skladniki);
    }

    header('Location: ' . app_url('admin/ciasta.php'));
    exit();
}

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2><?php echo $trybEdycji ? 'Edytuj ciasto' : 'Dodaj ciasto'; ?></h2>

    <form class="admin-form" method="post">
        <input type="hidden" name="id_ciasta" value="<?php echo h($daneCiasta['id_ciasta']); ?>">

        <label for="nazwa">Nazwa ciasta</label>
        <input type="text" id="nazwa" name="nazwa" required maxlength="100" value="<?php echo h($daneCiasta['nazwa']); ?>">

        <label for="opis">Opis</label>
        <textarea id="opis" name="opis" rows="4" maxlength="1000"><?php echo h($daneCiasta['opis']); ?></textarea>

        <label for="cena">Cena</label>
        <input type="number" id="cena" name="cena" step="0.01" min="0" required value="<?php echo h($daneCiasta['cena']); ?>">

        <label for="waga">Waga w gramach</label>
        <input type="number" id="waga" name="waga" min="1" required value="<?php echo h($daneCiasta['waga']); ?>">

        <label for="czas">Czas przygotowania w minutach</label>
        <input type="number" id="czas" name="czas" min="1" required value="<?php echo h($daneCiasta['czas_przygotowania']); ?>">

        <label for="id_kategorii">Kategoria</label>
        <select id="id_kategorii" name="id_kategorii" required>
            <option value="">-- wybierz kategorię --</option>
            <?php while ($kat = mysqli_fetch_assoc($kategorie)): ?>
                <option value="<?php echo (int)$kat['id_kategorii']; ?>" <?php echo ((int)$daneCiasta['id_kategorii'] === (int)$kat['id_kategorii']) ? 'selected' : ''; ?>>
                    <?php echo h($kat['nazwa']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="zdjecie">Zdjęcie (ścieżka w folderze images)</label>
        <input type="text" id="zdjecie" name="zdjecie" placeholder="images/tort_smietanka.jpg" value="<?php echo h($daneCiasta['zdjecie']); ?>">

        <label for="dostepnosc">Dostępność</label>
        <select id="dostepnosc" name="dostepnosc" required>
            <option value="1" <?php echo ((int)$daneCiasta['dostepnosc'] === 1) ? 'selected' : ''; ?>>Dostępne</option>
            <option value="0" <?php echo ((int)$daneCiasta['dostepnosc'] === 0) ? 'selected' : ''; ?>>Ukryte</option>
        </select>

        <label for="promowane">Promowane</label>
        <select id="promowane" name="promowane" required>
            <option value="1" <?php echo ((int)$daneCiasta['promowane'] === 1) ? 'selected' : ''; ?>>Tak</option>
            <option value="0" <?php echo ((int)$daneCiasta['promowane'] === 0) ? 'selected' : ''; ?>>Nie</option>
        </select>

        <div class="admin-card">
            <h3>Składniki</h3>
            <p class="info">Zaznacz składniki i wpisz ilość dla każdego z nich.</p>

            <?php while ($s = mysqli_fetch_assoc($skladnikiWszystkie)): ?>
                <?php
                    $idSkladnika = (int)$s['id_skladnika'];
                    $aktywny = isset($daneSkladniki[$idSkladnika]);
                    $ilosc = $aktywny ? $daneSkladniki[$idSkladnika]['ilosc'] : '';
                ?>
                <div style="display:grid; grid-template-columns: 1fr 140px; gap: 12px; align-items:center; margin-bottom: 12px;">
                    <label style="display:flex; align-items:center; gap:10px; margin:0;">
                        <input type="checkbox" name="skladniki[<?php echo $idSkladnika; ?>][aktywne]" value="1" <?php echo $aktywny ? 'checked' : ''; ?>>
                        <?php echo h($s['nazwa']); ?> (<?php echo h($s['jednostka']); ?>)
                    </label>
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="skladniki[<?php echo $idSkladnika; ?>][ilosc]"
                        placeholder="Ilość"
                        value="<?php echo h($ilosc); ?>">
                </div>
            <?php endwhile; ?>
        </div>

        <button type="submit" class="admin-btn" name="zapisz"><?php echo $trybEdycji ? 'Zapisz zmiany' : 'Dodaj ciasto'; ?></button>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
