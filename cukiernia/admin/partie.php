<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/PartiaSkladnika.php';
require_once __DIR__ . '/../classes/Skladnik.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$partia = new PartiaSkladnika($polaczenie);
$skladnik = new Skladnik($polaczenie);

if (isset($_GET['usun'])) {
    $partia->usun((int)$_GET['usun']);
    header('Location: ' . app_url('admin/partie.php'));
    exit();
}

if (isset($_POST['dodaj'])) {
    $partia->dodaj(
        (int)($_POST['id_skladnika'] ?? 0),
        (float)($_POST['ilosc'] ?? 0),
        trim($_POST['data_waznosci'] ?? '')
    );
    header('Location: ' . app_url('admin/partie.php'));
    exit();
}

$wynik = $partia->pobierzWszystkie();
$skladniki = $skladnik->pobierzWszystkie();

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Partie składników</h2>

    <form class="admin-form" method="post">
        <label for="id_skladnika">Składnik</label>
        <select id="id_skladnika" name="id_skladnika" required>
            <?php while ($s = mysqli_fetch_assoc($skladniki)): ?>
                <option value="<?php echo (int)$s['id_skladnika']; ?>"><?php echo h($s['nazwa']); ?> (<?php echo h($s['jednostka']); ?>)</option>
            <?php endwhile; ?>
        </select>

        <label for="ilosc">Ilość</label>
        <input type="number" step="0.01" min="0" id="ilosc" name="ilosc" required>

        <label for="data_waznosci">Data ważności</label>
        <input type="date" id="data_waznosci" name="data_waznosci" required>

        <button type="submit" class="admin-btn" name="dodaj">Dodaj partię</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID partii</th>
                <th>Składnik</th>
                <th>Ilość</th>
                <th>Data ważności</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($wynik)): ?>
                <tr>
                    <td><?php echo (int)$row['id_partii']; ?></td>
                    <td><?php echo h($row['skladnik']); ?></td>
                    <td><?php echo rtrim(rtrim(number_format((float)$row['ilosc'], 2, '.', ''), '0'), '.'); ?> <?php echo h($row['jednostka']); ?></td>
                    <td><?php echo h($row['data_waznosci']); ?></td>
                    <td>
                        <a href="<?php echo app_url('admin/partie.php?usun=' . (int)$row['id_partii']); ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę partię?');">Usuń</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
