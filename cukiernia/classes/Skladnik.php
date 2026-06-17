<?php

class Skladnik
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
            FROM skladniki
            ORDER BY id_skladnika ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPoId(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            SELECT *
            FROM skladniki
            WHERE id_skladnika = $id
            LIMIT 1
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function dodaj(string $nazwa, string $jednostka)
    {
        $nazwa = mysqli_real_escape_string($this->polaczenie, trim($nazwa));
        $jednostka = mysqli_real_escape_string($this->polaczenie, trim($jednostka));

        $zapytanie = "
            INSERT INTO skladniki (nazwa, jednostka)
            VALUES ('$nazwa', '$jednostka')
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function edytuj(int $id, string $nazwa, string $jednostka)
    {
        $id = (int)$id;
        $nazwa = mysqli_real_escape_string($this->polaczenie, trim($nazwa));
        $jednostka = mysqli_real_escape_string($this->polaczenie, trim($jednostka));

        $zapytanie = "
            UPDATE skladniki
            SET nazwa = '$nazwa',
                jednostka = '$jednostka'
            WHERE id_skladnika = $id
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function usun(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            DELETE FROM skladniki
            WHERE id_skladnika = $id
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPrzypisania()
    {
        $zapytanie = "
            SELECT s.id_skladnika, s.nazwa AS skladnik, a.id_alergenu, a.nazwa AS alergen
            FROM skladniki s
            JOIN skladnik_alergen sa ON sa.id_skladnika = s.id_skladnika
            JOIN alergeny a ON a.id_alergenu = sa.id_alergenu
            ORDER BY s.id_skladnika ASC, a.id_alergenu ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzAlergenyDlaSkladnika(int $idSkladnika)
    {
        $idSkladnika = (int)$idSkladnika;

        $zapytanie = "
            SELECT a.*
            FROM alergeny a
            JOIN skladnik_alergen sa ON sa.id_alergenu = a.id_alergenu
            WHERE sa.id_skladnika = $idSkladnika
            ORDER BY a.id_alergenu ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }
}
