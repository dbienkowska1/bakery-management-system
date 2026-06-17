<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Ciasto.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();
$ciasto = new Ciasto($polaczenie);

if (isset($_GET['usun'])) {
    $ciasto->usun((int)$_GET['usun']);
    header('Location: ' . app_url('admin/ciasta.php'));
    exit();
}

$wynik = mysqli_query(
    $polaczenie,
    "SELECT c.*, k.nazwa AS kategoria
     FROM ciasta c
     JOIN kategorie k ON k.id_kategorii = c.id_kategorii
     ORDER BY c.id_ciasta ASC"
);

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Lista ciast</h2>

    <div class="admin-actions">
        <a href="<?php echo app_url('admin/formularz_ciasto.php'); ?>" class="admin-btn">Dodaj nowe ciasto</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Kategoria</th>
                <th>Cena</th>
                <th>Waga</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($wynik)): ?>
                <tr>
                    <td><?php echo (int)$row['id_ciasta']; ?></td>
                    <td><?php echo h($row['nazwa']); ?></td>
                    <td><?php echo h($row['kategoria']); ?></td>
                    <td><?php echo money($row['cena']); ?></td>
                    <td><?php echo (int)$row['waga']; ?> g</td>
                    <td><?php echo !empty($row['dostepnosc']) ? 'Dostępne' : 'Ukryte'; ?></td>
                    <td>
                        <a href="<?php echo app_url('admin/formularz_ciasto.php?id=' . (int)$row['id_ciasta']); ?>">Edytuj</a> |
                        <a href="<?php echo app_url('admin/ciasta.php?usun=' . (int)$row['id_ciasta']); ?>" onclick="return confirm('Czy na pewno chcesz ukryć to ciasto?');">Usuń</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
