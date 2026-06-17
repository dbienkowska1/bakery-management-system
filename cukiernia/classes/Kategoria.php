<?php

class Kategoria
{
    private mysqli $polaczenie;

    public function __construct(mysqli $polaczenie)
    {
        $this->polaczenie = $polaczenie;
    }

    public function pobierzWszystkie()
    {
        $zapytanie = "
            SELECT *
            FROM kategorie
            ORDER BY id_kategorii ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPoId(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            SELECT *
            FROM kategorie
            WHERE id_kategorii = $id
            LIMIT 1
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }
}
