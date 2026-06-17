<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Zamowienie.php';
require_once __DIR__ . '/../classes/PartiaSkladnika.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$zamowienie = new Zamowienie($polaczenie);
$partia = new PartiaSkladnika($polaczenie);

$stat = $zamowienie->statystyki();
$ostatnie = $zamowienie->ostatnie(5);
$przeterminowane = $partia->pobierzPrzeterminowane();

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Panel administratora</h2>

    <div class="statystyki">
        <div class="stat-box">
            <h3>Ciasta</h3>
            <p><?php echo (int)$stat['liczba_ciast']; ?></p>
        </div>

        <div class="stat-box">
            <h3>Klienci</h3>
            <p><?php echo (int)$stat['liczba_klientow']; ?></p>
        </div>

        <div class="stat-box">
            <h3>Zamówienia</h3>
            <p><?php echo (int)$stat['liczba_zamowien']; ?></p>
        </div>

        <div class="stat-box">
            <h3>Przychód</h3>
            <p><?php echo money($stat['przychod']); ?></p>
        </div>
    </div>

    <div class="admin-card">
        <h3>⚠ Ostrzeżenia magazynowe</h3>
        <ul class="alert-list">
            <?php if ($przeterminowane && mysqli_num_rows($przeterminowane) > 0): ?>
                <?php while ($w = mysqli_fetch_assoc($przeterminowane)): ?>
                    <li><?php echo h($w['skladnik']); ?> - termin ważności <?php echo h($w['data_waznosci']); ?></li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>Brak ostrzeżeń magazynowych.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="admin-card">
        <h3>📦 Zamówienia</h3>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Klient</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Kwota</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ostatnie && mysqli_num_rows($ostatnie) > 0): ?>
                    <?php while ($z = mysqli_fetch_assoc($ostatnie)): ?>
                        <tr>
                            <td>#<?php echo (int)$z['id_zamowienia']; ?></td>
                            <td><?php echo h($z['imie'] . ' ' . $z['nazwisko']); ?></td>
                            <td><?php echo h($z['data_zamowienia']); ?></td>
                            <td><?php echo h($z['stan']); ?></td>
                            <td><?php echo money($z['calkowita_kwota']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Brak zamówień.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
