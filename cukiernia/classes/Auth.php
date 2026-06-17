<?php

class Auth
{
    public function zaloguj(string $login, string $haslo): bool
    {
        if ($login === ADMIN_LOGIN && $haslo === ADMIN_PASSWORD) {
            unset(
                $_SESSION['klient_id'],
                $_SESSION['klient_imie'],
                $_SESSION['klient_nazwisko'],
                $_SESSION['klient_email'],
                $_SESSION['klient_telefon']
            );

            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_login'] = $login;

            return true;
        }

        return false;
    }

    public function czyZalogowany(): bool
    {
        return !empty($_SESSION['admin_logged']);
    }

    public function wyloguj(): void
    {
        unset(
            $_SESSION['admin_logged'],
            $_SESSION['admin_login'],
            $_SESSION['klient_id'],
            $_SESSION['klient_imie'],
            $_SESSION['klient_nazwisko'],
            $_SESSION['klient_email'],
            $_SESSION['klient_telefon']
        );
    }

    public function sprawdzDostep(): void
    {
        if (!$this->czyZalogowany()) {
            header('Location: ' . app_url('admin/login.php'));
            exit();
        }
    }
}
