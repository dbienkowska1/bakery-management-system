<?php

class Alergen
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
            FROM alergeny
            ORDER BY id_alergenu ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzSkladniki()
    {
        $zapytanie = "
            SELECT *
            FROM skladniki
            ORDER BY id_skladnika ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPrzypisania()
    {
        $zapytanie = "
            SELECT sa.id_skladnika, s.nazwa AS skladnik, sa.id_alergenu, a.nazwa AS alergen
            FROM skladnik_alergen sa
            JOIN skladniki s ON s.id_skladnika = sa.id_skladnika
            JOIN alergeny a ON a.id_alergenu = sa.id_alergenu
            ORDER BY sa.id_skladnika ASC, sa.id_alergenu ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function dodaj(string $nazwa)
    {
        $nazwa = mysqli_real_escape_string($this->polaczenie, trim($nazwa));

        $zapytanie = "
            INSERT INTO alergeny (nazwa)
            VALUES ('$nazwa')
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function edytuj(int $id, string $nazwa)
    {
        $id = (int)$id;
        $nazwa = mysqli_real_escape_string($this->polaczenie, trim($nazwa));

        $zapytanie = "
            UPDATE alergeny
            SET nazwa = '$nazwa'
            WHERE id_alergenu = $id
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function usun(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            DELETE FROM alergeny
            WHERE id_alergenu = $id
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function przypiszDoSkladnika(int $idSkladnika, int $idAlergenu)
    {
        $idSkladnika = (int)$idSkladnika;
        $idAlergenu = (int)$idAlergenu;

        $zapytanie = "
            INSERT IGNORE INTO skladnik_alergen (id_skladnika, id_alergenu)
            VALUES ($idSkladnika, $idAlergenu)
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function usunPrzypisanie(int $idSkladnika, int $idAlergenu)
    {
        $idSkladnika = (int)$idSkladnika;
        $idAlergenu = (int)$idAlergenu;

        $zapytanie = "
            DELETE FROM skladnik_alergen
            WHERE id_skladnika = $idSkladnika
              AND id_alergenu = $idAlergenu
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }
}
