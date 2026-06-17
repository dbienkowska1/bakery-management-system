<?php

class Klient
{
    private mysqli $polaczenie;

    public function __construct(mysqli $polaczenie)
    {
        $this->polaczenie = $polaczenie;
    }

    public function dodaj(string $imie, string $nazwisko, string $telefon, string $email, string $haslo)
    {
        $imie = mysqli_real_escape_string($this->polaczenie, trim($imie));
        $nazwisko = mysqli_real_escape_string($this->polaczenie, trim($nazwisko));
        $telefon = mysqli_real_escape_string($this->polaczenie, trim($telefon));
        $email = mysqli_real_escape_string($this->polaczenie, trim($email));
        $hash = password_hash($haslo, PASSWORD_DEFAULT);

        $zapytanie = "
            INSERT INTO klienci (imie, nazwisko, telefon, email, haslo)
            VALUES ('$imie', '$nazwisko', '$telefon', '$email', '$hash')
        ";

        if (!mysqli_query($this->polaczenie, $zapytanie)) {
            return false;
        }

        return mysqli_insert_id($this->polaczenie);
    }

    public function aktualizuj(int $id, string $imie, string $nazwisko, string $telefon)
    {
        $id = (int)$id;
        $imie = mysqli_real_escape_string($this->polaczenie, trim($imie));
        $nazwisko = mysqli_real_escape_string($this->polaczenie, trim($nazwisko));
        $telefon = mysqli_real_escape_string($this->polaczenie, trim($telefon));

        $zapytanie = "
            UPDATE klienci
            SET imie = '$imie',
                nazwisko = '$nazwisko',
                telefon = '$telefon'
            WHERE id_klienta = $id
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPoId(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            SELECT *
            FROM klienci
            WHERE id_klienta = $id
            LIMIT 1
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPoEmail(string $email)
    {
        $email = mysqli_real_escape_string($this->polaczenie, trim($email));

        $zapytanie = "
            SELECT *
            FROM klienci
            WHERE email = '$email'
            LIMIT 1
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzWszystkich()
    {
        $zapytanie = "
            SELECT *
            FROM klienci
            ORDER BY id_klienta ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function historiaZamowien(int $idKlienta)
    {
        $idKlienta = (int)$idKlienta;

        $zapytanie = "
            SELECT z.id_zamowienia, z.data_zamowienia, z.data_odbioru, z.calkowita_kwota, s.stan
            FROM zamowienia z
            JOIN status s ON s.id_statusu = z.id_statusu
            WHERE z.id_klienta = $idKlienta
            ORDER BY z.id_zamowienia ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }
}
