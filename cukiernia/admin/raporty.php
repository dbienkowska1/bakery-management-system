<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Zamowienie.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$zamowienie = new Zamowienie($polaczenie);
$stat = $zamowienie->statystyki();

$topCiasto = mysqli_query(
    $polaczenie,
    "SELECT c.nazwa, SUM(p.ilosc) AS sprzedano
     FROM pozycje_zamowien p
     JOIN ciasta c ON c.id_ciasta = p.id_ciasta
     GROUP BY c.id_ciasta
     ORDER BY sprzedano DESC
     LIMIT 1"
);
$topCiastoRow = $topCiasto ? mysqli_fetch_assoc($topCiasto) : null;

$stany = mysqli_query(
    $polaczenie,
    "SELECT s.nazwa AS skladnik, SUM(ps.ilosc) AS suma
     FROM partie_skladnikow ps
     JOIN skladniki s ON s.id_skladnika = ps.id_skladnika
     GROUP BY s.id_skladnika
     ORDER BY s.id_skladnika ASC"
);

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Raporty i statystyki</h2>

    <div class="statystyki">
        <div class="stat-box">
            <h3>Sprzedaż</h3>
            <p><?php echo money($stat['przychod']); ?></p>
        </div>

        <div class="stat-box">
            <h3>Najpopularniejsze ciasto</h3>
            <p class="tekst-stat"><?php echo h($topCiastoRow['nazwa'] ?? 'brak danych'); ?></p>
        </div>

        <div class="stat-box">
            <h3>Liczba zamówień</h3>
            <p><?php echo (int)$stat['liczba_zamowien']; ?></p>
        </div>
    </div>

    <div class="admin-card">
        <h3>Stany składników</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Składnik</th>
                    <th>Ilość</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($stany && mysqli_num_rows($stany) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($stany)): ?>
                        <tr>
                            <td><?php echo h($row['skladnik']); ?></td>
                            <td><?php echo rtrim(rtrim(number_format((float)$row['suma'], 2, '.', ''), '0'), '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2">Brak danych.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
