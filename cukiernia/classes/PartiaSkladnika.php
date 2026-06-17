<?php

class PartiaSkladnika
{
    private mysqli $polaczenie;

    public function __construct(mysqli $polaczenie)
    {
        $this->polaczenie = $polaczenie;
    }

    public function pobierzWszystkie()
    {
        $zapytanie = "
            SELECT p.*, s.nazwa AS skladnik, s.jednostka
            FROM partie_skladnikow p
            JOIN skladniki s ON s.id_skladnika = p.id_skladnika
            ORDER BY p.id_partii ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPrzeterminowane()
    {
        $zapytanie = "
            SELECT p.*, s.nazwa AS skladnik, s.jednostka
            FROM partie_skladnikow p
            JOIN skladniki s ON s.id_skladnika = p.id_skladnika
            WHERE p.data_waznosci < CURDATE()
            ORDER BY p.id_partii ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzDlaSkladnika(int $idSkladnika)
    {
        $idSkladnika = (int)$idSkladnika;

        $zapytanie = "
            SELECT p.*, s.nazwa AS skladnik, s.jednostka
            FROM partie_skladnikow p
            JOIN skladniki s ON s.id_skladnika = p.id_skladnika
            WHERE p.id_skladnika = $idSkladnika
            ORDER BY p.id_partii ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function dodaj(int $idSkladnika, $ilosc, string $dataWaznosci)
    {
        $idSkladnika = (int)$idSkladnika;
        $ilosc = (float)$ilosc;
        $dataWaznosci = mysqli_real_escape_string($this->polaczenie, trim($dataWaznosci));

        $zapytanie = "
            INSERT INTO partie_skladnikow (id_skladnika, ilosc, data_waznosci)
            VALUES ($idSkladnika, $ilosc, '$dataWaznosci')
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function usun(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            DELETE FROM partie_skladnikow
            WHERE id_partii = $id
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }
}
