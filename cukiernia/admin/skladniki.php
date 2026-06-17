<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Skladnik.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();
$skladnik = new Skladnik($polaczenie);

$trybEdycji = false;
$dane = ['id_skladnika' => '', 'nazwa' => '', 'jednostka' => 'g'];

if (isset($_GET['usun'])) {
    $skladnik->usun((int)$_GET['usun']);
    header('Location: ' . app_url('admin/skladniki.php'));
    exit();
}

if (isset($_GET['edytuj'])) {
    $trybEdycji = true;
    $wynik = $skladnik->pobierzPoId((int)$_GET['edytuj']);
    if ($wynik && mysqli_num_rows($wynik) === 1) {
        $dane = mysqli_fetch_assoc($wynik);
    } else {
        $trybEdycji = false;
    }
}

if (isset($_POST['zapisz'])) {
    $id = (int)($_POST['id_skladnika'] ?? 0);
    $nazwa = trim($_POST['nazwa'] ?? '');
    $jednostka = trim($_POST['jednostka'] ?? '');

    if ($id > 0) {
        $skladnik->edytuj($id, $nazwa, $jednostka);
    } else {
        $skladnik->dodaj($nazwa, $jednostka);
    }

    header('Location: ' . app_url('admin/skladniki.php'));
    exit();
}

$wynik = $skladnik->pobierzWszystkie();

$enumWartosci = ['g', 'kg', 'ml', 'l', 'szt'];

$res = mysqli_query($polaczenie, "SHOW COLUMNS FROM skladniki LIKE 'jednostka'");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    if (preg_match("/^enum\\((.*)\\)$/i", $row['Type'] ?? '', $m)) {
        $tmp = explode(',', $m[1]);
        $enumWartosci = [];
        foreach ($tmp as $wartosc) {
            $enumWartosci[] = trim($wartosc, " '");
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Składniki</h2>

    <div class="admin-card">
        <form class="admin-form" method="post">
            <input type="hidden" name="id_skladnika" value="<?php echo h($dane['id_skladnika']); ?>">

            <label for="nazwa">Nazwa składnika</label>
            <input type="text" id="nazwa" name="nazwa" maxlength="100" required value="<?php echo h($dane['nazwa']); ?>">

            <label for="jednostka">Jednostka</label>
            <select id="jednostka" name="jednostka" required>
                <?php foreach ($enumWartosci as $jednostka): ?>
                    <option value="<?php echo h($jednostka); ?>" <?php echo ($dane['jednostka'] === $jednostka) ? 'selected' : ''; ?>>
                        <?php echo h($jednostka); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="admin-btn" name="zapisz"><?php echo $trybEdycji ? 'Zapisz zmiany' : 'Dodaj składnik'; ?></button>
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Jednostka</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($wynik)): ?>
                <tr>
                    <td><?php echo (int)$row['id_skladnika']; ?></td>
                    <td><?php echo h($row['nazwa']); ?></td>
                    <td><?php echo h($row['jednostka']); ?></td>
                    <td>
                        <a href="<?php echo app_url('admin/skladniki.php?edytuj=' . (int)$row['id_skladnika']); ?>">Edytuj</a> |
                        <a href="<?php echo app_url('admin/skladniki.php?usun=' . (int)$row['id_skladnika']); ?>" onclick="return confirm('Czy na pewno chcesz usunąć ten składnik?');">Usuń</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
