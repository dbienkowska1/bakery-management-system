<?php

class Zamowienie
{
    private mysqli $polaczenie;

    public function __construct(mysqli $polaczenie)
    {
        $this->polaczenie = $polaczenie;
    }

    public function dodaj(int $idKlienta, string $dataOdbioru, float $kwota, string $uwagi = '')
    {
        $idKlienta = (int)$idKlienta;
        $dataOdbioru = mysqli_real_escape_string($this->polaczenie, trim($dataOdbioru));
        $uwagi = mysqli_real_escape_string($this->polaczenie, trim($uwagi));
        $kwota = number_format($kwota, 2, '.', '');

        $zapytanie = "
            INSERT INTO zamowienia
            (id_klienta, data_zamowienia, data_odbioru, id_statusu, calkowita_kwota, uwagi)
            VALUES
            ($idKlienta, NOW(), '$dataOdbioru', 1, $kwota, '$uwagi')
        ";

        if (!mysqli_query($this->polaczenie, $zapytanie)) {
            return false;
        }

        return mysqli_insert_id($this->polaczenie);
    }

    public function pobierzWszystkie(?int $idStatusu = null)
    {
        $where = '';
        if (!empty($idStatusu)) {
            $idStatusu = (int)$idStatusu;
            $where = "WHERE z.id_statusu = $idStatusu";
        }

        $zapytanie = "
            SELECT z.*, k.imie, k.nazwisko, k.email, s.stan
            FROM zamowienia z
            JOIN klienci k ON k.id_klienta = z.id_klienta
            JOIN status s ON s.id_statusu = z.id_statusu
            $where
            ORDER BY z.id_zamowienia ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function ostatnie(int $limit = 5)
    {
        $limit = max(1, (int)$limit);

        $zapytanie = "
            SELECT z.*, k.imie, k.nazwisko, s.stan
            FROM zamowienia z
            JOIN klienci k ON k.id_klienta = z.id_klienta
            JOIN status s ON s.id_statusu = z.id_statusu
            ORDER BY z.id_zamowienia ASC
            LIMIT $limit
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPoId(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            SELECT z.*, k.imie, k.nazwisko, k.telefon, k.email, s.stan
            FROM zamowienia z
            JOIN klienci k ON k.id_klienta = z.id_klienta
            JOIN status s ON s.id_statusu = z.id_statusu
            WHERE z.id_zamowienia = $id
            LIMIT 1
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function zmienStatus(int $idZamowienia, int $idStatusu)
    {
        $idZamowienia = (int)$idZamowienia;
        $idStatusu = (int)$idStatusu;

        $zapytanie = "
            UPDATE zamowienia
            SET id_statusu = $idStatusu
            WHERE id_zamowienia = $idZamowienia
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function usun(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            DELETE FROM zamowienia
            WHERE id_zamowienia = $id
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function statystyki(): array
    {
        $wynik = [];

        $q1 = mysqli_query($this->polaczenie, "SELECT COUNT(*) AS liczba FROM zamowienia");
        $wynik['liczba_zamowien'] = (int)(mysqli_fetch_assoc($q1)['liczba'] ?? 0);

        $q2 = mysqli_query($this->polaczenie, "SELECT COUNT(*) AS liczba FROM klienci");
        $wynik['liczba_klientow'] = (int)(mysqli_fetch_assoc($q2)['liczba'] ?? 0);

        $q3 = mysqli_query($this->polaczenie, "SELECT COUNT(*) AS liczba FROM ciasta WHERE dostepnosc = 1");
        $wynik['liczba_ciast'] = (int)(mysqli_fetch_assoc($q3)['liczba'] ?? 0);

        $q4 = mysqli_query($this->polaczenie, "SELECT COALESCE(SUM(calkowita_kwota), 0) AS suma FROM zamowienia");
        $wynik['przychod'] = (float)(mysqli_fetch_assoc($q4)['suma'] ?? 0);

        return $wynik;
    }
}
