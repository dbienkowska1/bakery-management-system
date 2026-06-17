<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/BazaDanych.php';
require_once __DIR__ . '/classes/Klient.php';

if (empty($_SESSION['klient_id'])) {
    header('Location: ' . app_url('logowanie.php'));
    exit();
}

$db = new BazaDanych();
$polaczenie = $db->getPolaczenie();

$klientModel = new Klient($polaczenie);

$wynikKlienta = $klientModel->pobierzPoId((int)$_SESSION['klient_id']);
$daneKlienta = ($wynikKlienta && mysqli_num_rows($wynikKlienta) === 1) ? mysqli_fetch_assoc($wynikKlienta) : null;
$historia = $klientModel->historiaZamowien((int)$_SESSION['klient_id']);

include __DIR__ . '/includes/header.php';
?>

<section class="moje-konto">
    <h2>Moje konto</h2>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'zamowienie_zlozone'): ?>
        <div class="komunikat komunikat-sukces">
            <p>Zamówienie zostało zapisane.</p>
        </div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'rejestracja_ok'): ?>
        <div class="komunikat komunikat-sukces">
            <p>Konto zostało utworzone.</p>
        </div>
    <?php endif; ?>

    <?php if ($daneKlienta): ?>
        <div class="konto-box">
            <h3>Dane klienta</h3>

            <p><strong>Imię:</strong> <?php echo h($daneKlienta['imie']); ?></p>
            <p><strong>Nazwisko:</strong> <?php echo h($daneKlienta['nazwisko']); ?></p>
            <p><strong>Telefon:</strong> <?php echo h($daneKlienta['telefon']); ?></p>
            <p><strong>E-mail:</strong> <?php echo h($daneKlienta['email']); ?></p>
        </div>

        <div class="konto-box">
            <h3>Historia zamówień</h3>

            <?php if ($historia && mysqli_num_rows($historia) > 0): ?>
                <table class="tabela-zamowien">
                    <thead>
                        <tr>
                            <th>Nr zamówienia</th>
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
                <p>Brak zamówień do wyświetlenia.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="konto-box">
            <p>Nie udało się wczytać danych konta.</p>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
