<?php

class AuthKlient
{
    private mysqli $polaczenie;

    public function __construct(mysqli $polaczenie)
    {
        $this->polaczenie = $polaczenie;
    }

    public function zaloguj(string $email, string $haslo): bool
    {
        $email = mysqli_real_escape_string($this->polaczenie, trim($email));

        $zapytanie = "
            SELECT id_klienta, imie, nazwisko, telefon, email, haslo
            FROM klienci
            WHERE email = '$email'
            LIMIT 1
        ";

        $wynik = mysqli_query($this->polaczenie, $zapytanie);

        if ($wynik && mysqli_num_rows($wynik) === 1) {
            $klient = mysqli_fetch_assoc($wynik);
            $hash = (string)($klient['haslo'] ?? '');

            $czyPoprawne = password_verify($haslo, $hash) || hash_equals($hash, $haslo);

            if ($czyPoprawne) {
                unset(
                    $_SESSION['admin_logged'],
                    $_SESSION['admin_login']
                );

                $_SESSION['klient_id'] = (int)$klient['id_klienta'];
                $_SESSION['klient_imie'] = $klient['imie'];
                $_SESSION['klient_nazwisko'] = $klient['nazwisko'];
                $_SESSION['klient_email'] = $klient['email'];
                $_SESSION['klient_telefon'] = $klient['telefon'];

                return true;
            }
        }

        return false;
    }

    public function wyloguj(): void
    {
        unset(
            $_SESSION['klient_id'],
            $_SESSION['klient_imie'],
            $_SESSION['klient_nazwisko'],
            $_SESSION['klient_email'],
            $_SESSION['klient_telefon'],
            $_SESSION['admin_logged'],
            $_SESSION['admin_login']
        );
    }

    public function czyZalogowany(): bool
    {
        return !empty($_SESSION['klient_id']);
    }
}
