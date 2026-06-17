<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Zamowienie.php';
require_once __DIR__ . '/../classes/Status.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$zamowienie = new Zamowienie($polaczenie);
$statusModel = new Status($polaczenie);

if (isset($_POST['zapisz'])) {
    $zamowienie->zmienStatus(
        (int)($_POST['id_zamowienia'] ?? 0),
        (int)($_POST['id_statusu'] ?? 0)
    );

    header('Location: ' . app_url('admin/zamowienia.php'));
    exit();
}

$idStatusuFiltr = (int)($_GET['status'] ?? 0);
$zamowienia = $zamowienie->pobierzWszystkie($idStatusuFiltr ?: null);

$statusy = [];
$tmpStatusy = $statusModel->pobierzWszystkie();
if ($tmpStatusy) {
    while ($st = mysqli_fetch_assoc($tmpStatusy)) {
        $statusy[] = $st;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Zamówienia</h2>

    <form method="get" class="admin-form" style="max-width: 400px; margin-left: 0; padding: 20px;">
        <label for="status">Filtruj po statusie</label>
        <select name="status" id="status">
            <option value="0">Wszystkie</option>
            <?php foreach ($statusy as $st): ?>
                <option value="<?php echo (int)$st['id_statusu']; ?>" <?php echo $idStatusuFiltr === (int)$st['id_statusu'] ? 'selected' : ''; ?>>
                    <?php echo h($st['stan']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="admin-btn">Filtruj</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Klient</th>
                <th>Data zamówienia</th>
                <th>Data odbioru</th>
                <th>Status</th>
                <th>Kwota</th>
                <th>Szczegóły</th>
                <th>Zmiana statusu</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($zamowienia && mysqli_num_rows($zamowienia) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($zamowienia)): ?>
                    <tr>
                        <td>#<?php echo (int)$row['id_zamowienia']; ?></td>
                        <td><?php echo h($row['imie'] . ' ' . $row['nazwisko']); ?></td>
                        <td><?php echo h($row['data_zamowienia']); ?></td>
                        <td><?php echo h($row['data_odbioru']); ?></td>
                        <td><?php echo h($row['stan']); ?></td>
                        <td><?php echo money($row['calkowita_kwota']); ?></td>
                        <td><a href="<?php echo app_url('admin/szczegoly_zamowienia.php?id=' . (int)$row['id_zamowienia']); ?>">Pokaż</a></td>
                        <td>
                            <form method="post" style="display:flex; gap:10px; align-items:center; margin:0;">
                                <input type="hidden" name="id_zamowienia" value="<?php echo (int)$row['id_zamowienia']; ?>">
                                <select name="id_statusu">
                                    <?php foreach ($statusy as $s): ?>
                                        <option value="<?php echo (int)$s['id_statusu']; ?>" <?php echo ((int)$s['id_statusu'] === (int)$row['id_statusu']) ? 'selected' : ''; ?>>
                                            <?php echo h($s['stan']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="zapisz" class="admin-btn">Zmień</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Brak zamówień.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
