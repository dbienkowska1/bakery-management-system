<?php

class PozycjaZamowienia
{
    private mysqli $polaczenie;

    public function __construct(mysqli $polaczenie)
    {
        $this->polaczenie = $polaczenie;
    }

    public function dodaj(int $idZamowienia, int $idCiasta, int $ilosc, $cenaJednostkowa)
    {
        $idZamowienia = (int)$idZamowienia;
        $idCiasta = (int)$idCiasta;
        $ilosc = max(1, (int)$ilosc);
        $cenaJednostkowa = number_format((float)$cenaJednostkowa, 2, '.', '');

        $zapytanie = "
            INSERT INTO pozycje_zamowien (id_zamowienia, id_ciasta, ilosc, cena_jednostkowa)
            VALUES ($idZamowienia, $idCiasta, $ilosc, $cenaJednostkowa)
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzDlaZamowienia(int $idZamowienia)
    {
        $idZamowienia = (int)$idZamowienia;

        $zapytanie = "
            SELECT p.*, c.nazwa, c.opis
            FROM pozycje_zamowien p
            JOIN ciasta c ON c.id_ciasta = p.id_ciasta
            WHERE p.id_zamowienia = $idZamowienia
            ORDER BY p.id_pozycji ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function sumaZamowienia(int $idZamowienia): float
    {
        $idZamowienia = (int)$idZamowienia;

        $zapytanie = "
            SELECT COALESCE(SUM(ilosc * cena_jednostkowa), 0) AS suma
            FROM pozycje_zamowien
            WHERE id_zamowienia = $idZamowienia
        ";

        $wynik = mysqli_query($this->polaczenie, $zapytanie);
        $row = $wynik ? mysqli_fetch_assoc($wynik) : null;

        return (float)($row['suma'] ?? 0);
    }

    public function usunDlaZamowienia(int $idZamowienia): bool
    {
        $idZamowienia = (int)$idZamowienia;

        $zapytanie = "
            DELETE FROM pozycje_zamowien
            WHERE id_zamowienia = $idZamowienia
        ";

        return (bool) mysqli_query($this->polaczenie, $zapytanie);
    }
}
