<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Klient.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$klientModel = new Klient($polaczenie);

$id = (int)($_GET['id'] ?? 0);
$wynik = $id > 0 ? $klientModel->pobierzPoId($id) : false;
$dane = ($wynik && mysqli_num_rows($wynik) === 1) ? mysqli_fetch_assoc($wynik) : null;
$historia = $id > 0 ? $klientModel->historiaZamowien($id) : false;

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Szczegóły klienta</h2>

    <?php if (!$dane): ?>
        <div class="admin-card">
            <p>Nie znaleziono klienta.</p>
        </div>
    <?php else: ?>
        <div class="admin-card">
            <p><strong>Imię:</strong> <?php echo h($dane['imie']); ?></p>
            <p><strong>Nazwisko:</strong> <?php echo h($dane['nazwisko']); ?></p>
            <p><strong>Telefon:</strong> <?php echo h($dane['telefon']); ?></p>
            <p><strong>E-mail:</strong> <?php echo h($dane['email']); ?></p>
        </div>

        <div class="admin-card">
            <h3>Historia zamówień</h3>

            <?php if ($historia && mysqli_num_rows($historia) > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data zamówienia</th>
                            <th>Data odbioru</th>
                            <th>Status</th>
                            <th>Kwota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($z = mysqli_fetch_assoc($historia)): ?>
                            <tr>
                                <td>#<?php echo (int)$z['id_zamowienia']; ?></td>
                                <td><?php echo h($z['data_zamowienia']); ?></td>
                                <td><?php echo h($z['data_odbioru']); ?></td>
                                <td><?php echo h($z['stan']); ?></td>
                                <td><?php echo money($z['calkowita_kwota']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Brak zamówień dla tego klienta.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="admin-actions">
        <a href="<?php echo app_url('admin/klienci.php'); ?>" class="admin-btn">Powrót</a>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
