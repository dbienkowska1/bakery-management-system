<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Klient.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$klient = new Klient($polaczenie);
$wynik = $klient->pobierzWszystkich();

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Klienci</h2>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Telefon</th>
                <th>Email</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($wynik)): ?>
                <tr>
                    <td><?php echo (int)$row['id_klienta']; ?></td>
                    <td><?php echo h($row['imie']); ?></td>
                    <td><?php echo h($row['nazwisko']); ?></td>
                    <td><?php echo h($row['telefon']); ?></td>
                    <td><?php echo h($row['email']); ?></td>
                    <td>
                        <a href="<?php echo app_url('admin/szczegoly_klienta.php?id=' . (int)$row['id_klienta']); ?>">Podgląd</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
