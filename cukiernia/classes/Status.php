<?php

class Status
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
            FROM status
            ORDER BY id_statusu ASC
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }

    public function pobierzPoId(int $id)
    {
        $id = (int)$id;

        $zapytanie = "
            SELECT *
            FROM status
            WHERE id_statusu = $id
            LIMIT 1
        ";

        return mysqli_query($this->polaczenie, $zapytanie);
    }
}
