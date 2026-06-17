<nav class="menu">
    <ul class="menu-list">
        <?php if ($zalogowanyAdmin): ?>
            <li><a href="<?php echo app_url('index.php'); ?>">Strona główna</a></li>
            <li><a href="<?php echo app_url('admin/panel.php'); ?>">Panel administratora</a></li>
            <li><a href="<?php echo app_url('admin/ciasta.php'); ?>">Ciasta</a></li>
            <li><a href="<?php echo app_url('admin/zamowienia.php'); ?>">Zamówienia</a></li>
            <li><a href="<?php echo app_url('admin/skladniki.php'); ?>">Składniki</a></li>
            <li><a href="<?php echo app_url('admin/partie.php'); ?>">Partie</a></li>
            <li><a href="<?php echo app_url('admin/alergeny.php'); ?>">Alergeny</a></li>
            <li><a href="<?php echo app_url('admin/klienci.php'); ?>">Klienci</a></li>
            <li><a href="<?php echo app_url('admin/raporty.php'); ?>">Raporty</a></li>
            <li><a href="<?php echo app_url('wylogowanie.php'); ?>">Wyloguj</a></li>
        <?php else: ?>
            <li><a href="<?php echo app_url('index.php'); ?>">Strona główna</a></li>
            <li><a href="<?php echo app_url('oferta.php'); ?>">Oferta</a></li>
            <li><a href="<?php echo app_url('koszyk.php'); ?>">Koszyk</a></li>

            <?php if ($zalogowanyKlient): ?>
                <li><a href="<?php echo app_url('moje_konto.php'); ?>">Moje konto</a></li>
                <li><a href="<?php echo app_url('wylogowanie.php'); ?>">Wyloguj</a></li>
            <?php else: ?>
                <li><a href="<?php echo app_url('logowanie.php'); ?>">Logowanie</a></li>
                <li><a href="<?php echo app_url('rejestracja.php'); ?>">Rejestracja</a></li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
</nav>
