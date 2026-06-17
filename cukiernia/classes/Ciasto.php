<?php

class Ciasto
{
    private mysqli $polaczenie;

    public function __construct(mysqli $polaczenie)
    {
        $this->polaczenie = $polaczenie;
    }

    public function pobierzWszystkie()
    {
        $zapytanie = "
            SELECT c.*, k.nazwa AS kategoria
            FROM ciasta c
            JOIN kategorie k
                ON c.id_kategorii = k.id_kategorii
            WHERE c.dostepnosc = 1
            ORDER BY c.id_ciasta ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPromowane(int $limit = 3)
    {
        $limit = max(1, (int)$limit);

        $zapytanie = "
            SELECT c.*, k.nazwa AS kategoria
            FROM ciasta c
            JOIN kategorie k
                ON c.id_kategorii = k.id_kategorii
            WHERE c.dostepnosc = 1
              AND c.promowane = 1
            ORDER BY c.id_ciasta ASC
            LIMIT $limit
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPoId(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            SELECT c.*, k.nazwa AS kategoria
            FROM ciasta c
            JOIN kategorie k
                ON c.id_kategorii = k.id_kategorii
            WHERE c.id_ciasta = $id
            LIMIT 1
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzSzczegoly(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            SELECT c.*, k.nazwa AS kategoria
            FROM ciasta c
            JOIN kategorie k
                ON c.id_kategorii = k.id_kategorii
            WHERE c.id_ciasta = $id
              AND c.dostepnosc = 1
            LIMIT 1
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzFiltrowane(string $sortowanie, int $idKategorii)
    {
        $orderBy = 'c.id_ciasta ASC';

        if ($sortowanie === 'nazwa_asc') {
            $orderBy = 'c.nazwa ASC';
        } elseif ($sortowanie === 'nazwa_desc') {
            $orderBy = 'c.nazwa DESC';
        } elseif ($sortowanie === 'cena_asc') {
            $orderBy = 'c.cena ASC';
        } elseif ($sortowanie === 'cena_desc') {
            $orderBy = 'c.cena DESC';
        }

        $idKategorii = (int)$idKategorii;

        $where = "WHERE c.dostepnosc = 1";
        if ($idKategorii > 0) {
            $where .= " AND c.id_kategorii = $idKategorii";
        }

        $zapytanie = "
            SELECT c.*, k.nazwa AS kategoria
            FROM ciasta c
            JOIN kategorie k
                ON c.id_kategorii = k.id_kategorii
            $where
            ORDER BY $orderBy
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function dodaj(
        string $nazwa,
        string $opis,
        $cena,
        $waga,
        $czas,
        int $idKategorii,
        int $dostepnosc = 1,
        int $promowane = 0,
        string $zdjecie = ''
    ) {
        $nazwa = mysqli_real_escape_string($this->polaczenie, trim($nazwa));
        $opis = mysqli_real_escape_string($this->polaczenie, trim($opis));
        $cena = number_format((float)$cena, 2, '.', '');
        $waga = (int)$waga;
        $czas = (int)$czas;
        $idKategorii = (int)$idKategorii;
        $dostepnosc = (int)$dostepnosc;
        $promowane = (int)$promowane;
        $zdjecie = mysqli_real_escape_string($this->polaczenie, trim($zdjecie));

        $zapytanie = "
            INSERT INTO ciasta
            (nazwa, opis, cena, waga, czas_przygotowania, id_kategorii, dostepnosc, promowane, zdjecie)
            VALUES
            ('$nazwa', '$opis', $cena, $waga, $czas, $idKategorii, $dostepnosc, $promowane, '$zdjecie')
        ";

        if (!mysqli_query($this->polaczenie, $zapytanie)) {
            return false;
        }

        return mysqli_insert_id($this->polaczenie);
    }

    public function aktualizuj(
        int $id,
        string $nazwa,
        string $opis,
        $cena,
        $waga,
        $czas,
        int $idKategorii,
        int $dostepnosc = 1,
        int $promowane = 0,
        string $zdjecie = ''
    ) {
        $id = (int)$id;
        $nazwa = mysqli_real_escape_string($this->polaczenie, trim($nazwa));
        $opis = mysqli_real_escape_string($this->polaczenie, trim($opis));
        $cena = number_format((float)$cena, 2, '.', '');
        $waga = (int)$waga;
        $czas = (int)$czas;
        $idKategorii = (int)$idKategorii;
        $dostepnosc = (int)$dostepnosc;
        $promowane = (int)$promowane;
        $zdjecie = mysqli_real_escape_string($this->polaczenie, trim($zdjecie));

        $zapytanie = "
            UPDATE ciasta
            SET nazwa = '$nazwa',
                opis = '$opis',
                cena = $cena,
                waga = $waga,
                czas_przygotowania = $czas,
                id_kategorii = $idKategorii,
                dostepnosc = $dostepnosc,
                promowane = $promowane,
                zdjecie = '$zdjecie'
            WHERE id_ciasta = $id
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function usun(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            UPDATE ciasta
            SET dostepnosc = 0
            WHERE id_ciasta = $id
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzSkladniki(int $idCiasta)
    {
        $idCiasta = (int)$idCiasta;

        $zapytanie = "
            SELECT cs.id_skladnika, s.nazwa, s.jednostka, cs.ilosc
            FROM ciasto_skladnik cs
            JOIN skladniki s ON s.id_skladnika = cs.id_skladnika
            WHERE cs.id_ciasta = $idCiasta
            ORDER BY s.id_skladnika ASC
        ";

        $wynik = mysqli_query($this->polaczenie, $zapytanie);
        $dane = [];

        if ($wynik) {
            while ($row = mysqli_fetch_assoc($wynik)) {
                $dane[(int)$row['id_skladnika']] = $row;
            }
        }

        return $dane;
    }

    public function pobierzAlergeny(int $idCiasta)
    {
        $idCiasta = (int)$idCiasta;

        $zapytanie = "
            SELECT DISTINCT a.*
            FROM alergeny a
            JOIN skladnik_alergen sa ON sa.id_alergenu = a.id_alergenu
            JOIN ciasto_skladnik cs ON cs.id_skladnika = sa.id_skladnika
            WHERE cs.id_ciasta = $idCiasta
            ORDER BY a.id_alergenu ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function zapiszSkladniki(int $idCiasta, array $skladniki): bool
    {
        $idCiasta = (int)$idCiasta;

        mysqli_query($this->polaczenie, "DELETE FROM ciasto_skladnik WHERE id_ciasta = $idCiasta");

        foreach ($skladniki as $idSkladnika => $dane) {
            $idSkladnika = (int)$idSkladnika;
            $ilosc = (float)($dane['ilosc'] ?? 0);
            $aktywne = !empty($dane['aktywne']);

            if ($aktywne && $ilosc > 0) {
                $ilosc = number_format($ilosc, 2, '.', '');
                mysqli_query(
                    $this->polaczenie,
                    "INSERT INTO ciasto_skladnik (id_ciasta, id_skladnika, ilosc)
                     VALUES ($idCiasta, $idSkladnika, $ilosc)"
                );
            }
        }

        return true;
    }
}
