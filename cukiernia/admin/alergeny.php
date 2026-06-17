<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/BazaDanych.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Alergen.php';

$auth = new Auth();
$auth->sprawdzDostep();

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$alergen = new Alergen($polaczenie);

if (isset($_POST['dodaj_alergen'])) {
    $alergen->dodaj(trim($_POST['nazwa'] ?? ''));
    header('Location: ' . app_url('admin/alergeny.php'));
    exit();
}

if (isset($_POST['przypisz'])) {
    $alergen->przypiszDoSkladnika((int)($_POST['id_skladnika'] ?? 0), (int)($_POST['id_alergenu'] ?? 0));
    header('Location: ' . app_url('admin/alergeny.php'));
    exit();
}

if (isset($_GET['usun_przypisanie'])) {
    $alergen->usunPrzypisanie((int)$_GET['id_skladnika'], (int)$_GET['id_alergenu']);
    header('Location: ' . app_url('admin/alergeny.php'));
    exit();
}

if (isset($_GET['usun'])) {
    $alergen->usun((int)$_GET['usun']);
    header('Location: ' . app_url('admin/alergeny.php'));
    exit();
}

$listaAlergenow = $alergen->pobierzWszystkie();
$listaSkladnikow = $alergen->pobierzSkladniki();
$przypisania = $alergen->pobierzPrzypisania();

include __DIR__ . '/../includes/header.php';
?>

<section class="admin-page">
    <h2>Alergeny</h2>

    <div class="admin-card">
        <form class="admin-form" method="post">
            <label>Nazwa alergenu</label>
            <input type="text" name="nazwa" placeholder="np. gluten, mleko, orzechy" required>
            <button type="submit" class="admin-btn" name="dodaj_alergen">Dodaj alergen</button>
        </form>
    </div>

    <div class="admin-card">
        <h3>Przypisz alergen do składnika</h3>
        <form class="admin-form" method="post" style="max-width: 700px; margin-left: 0; padding: 20px;">
            <label>Składnik</label>
            <select name="id_skladnika" required>
                <?php mysqli_data_seek($listaSkladnikow, 0); while ($s = mysqli_fetch_assoc($listaSkladnikow)): ?>
                    <option value="<?php echo (int)$s['id_skladnika']; ?>"><?php echo h($s['nazwa']); ?></option>
                <?php endwhile; ?>
            </select>

            <label>Alergen</label>
            <select name="id_alergenu" required>
                <?php mysqli_data_seek($listaAlergenow, 0); while ($a = mysqli_fetch_assoc($listaAlergenow)): ?>
                    <option value="<?php echo (int)$a['id_alergenu']; ?>"><?php echo h($a['nazwa']); ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="admin-btn" name="przypisz">Przypisz</button>
        </form>
    </div>

    <div class="admin-card">
        <h3>Lista alergenów</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa alergenu</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php mysqli_data_seek($listaAlergenow, 0); while ($row = mysqli_fetch_assoc($listaAlergenow)): ?>
                    <tr>
                        <td><?php echo (int)$row['id_alergenu']; ?></td>
                        <td><?php echo h($row['nazwa']); ?></td>
                        <td>
                            <a href="<?php echo app_url('admin/alergeny.php?usun=' . (int)$row['id_alergenu']); ?>" onclick="return confirm('Czy na pewno chcesz usunąć ten alergen?');">Usuń</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-card">
        <h3>Przypisania alergeny → składniki</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Składnik</th>
                    <th>Alergen</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($przypisania && mysqli_num_rows($przypisania) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($przypisania)): ?>
                        <tr>
                            <td><?php echo h($row['skladnik']); ?></td>
                            <td><?php echo h($row['alergen']); ?></td>
                            <td>
                                <a href="<?php echo app_url('admin/alergeny.php?usun_przypisanie=1&id_skladnika=' . (int)$row['id_skladnika'] . '&id_alergenu=' . (int)$row['id_alergenu']); ?>">Usuń przypisanie</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Brak przypisań.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
