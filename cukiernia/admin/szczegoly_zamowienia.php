<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Zamowienie.php';
require_once __DIR__ . '/../classes/PozycjaZamowienia.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$zamowienie = new Zamowienie($polaczenie);
$pozycja = new PozycjaZamowienia($polaczenie);

$id = (int)($_GET['id'] ?? 0);
$zam = $id > 0 ? $zamowienie->pobierzPoId($id) : false;
$dane = ($zam && mysqli_num_rows($zam) > 0) ? mysqli_fetch_assoc($zam) : null;
$pozycje = $pozycja->pobierzDlaZamowienia($id);

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Szczegóły zamówienia</h2>

    <?php if (!$dane): ?>
        <div class="admin-card">
            <p>Nie znaleziono zamówienia.</p>
        </div>
    <?php else: ?>
        <div class="admin-card">
            <p><strong>Klient:</strong> <?php echo h($dane['imie'] . ' ' . $dane['nazwisko']); ?></p>
            <p><strong>E-mail:</strong> <?php echo h($dane['email']); ?></p>
            <p><strong>Telefon:</strong> <?php echo h($dane['telefon']); ?></p>
            <p><strong>Data zamówienia:</strong> <?php echo h($dane['data_zamowienia']); ?></p>
            <p><strong>Data odbioru:</strong> <?php echo h($dane['data_odbioru']); ?></p>
            <p><strong>Status:</strong> <?php echo h($dane['stan']); ?></p>
            <p><strong>Uwagi:</strong> <?php echo nl2br(h($dane['uwagi'])); ?></p>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ciasto</th>
                    <th>Ilość</th>
                    <th>Cena jednostkowa</th>
                    <th>Wartość</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pozycje && mysqli_num_rows($pozycje) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($pozycje)): ?>
                        <tr>
                            <td><?php echo h($p['nazwa']); ?></td>
                            <td><?php echo (int)$p['ilosc']; ?></td>
                            <td><?php echo money($p['cena_jednostkowa']); ?></td>
                            <td><?php echo money((float)$p['cena_jednostkowa'] * (int)$p['ilosc']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Brak pozycji.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="admin-actions">
        <a href="<?php echo app_url('admin/zamowienia.php'); ?>" class="admin-btn">Powrót</a>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
