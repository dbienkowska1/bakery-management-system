<?php

class BazaDanych
{
    private mysqli $polaczenie;

    public function __construct()
    {
        $this->polaczenie = mysqli_connect(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME
        );

        if (!$this->polaczenie) {
            die('Błąd połączenia z bazą danych: ' . mysqli_connect_error());
        }

        mysqli_set_charset($this->polaczenie, 'utf8mb4');
    }

    public function getPolaczenie(): mysqli
    {
        return $this->polaczenie;
    }

    public function query(string $sql)
    {
        return mysqli_query($this->polaczenie, $sql);
    }

    public function escape(string $value): string
    {
        return mysqli_real_escape_string($this->polaczenie, $value);
    }

    public function insertId(): int
    {
        return mysqli_insert_id($this->polaczenie);
    }

    public function close(): void
    {
        mysqli_close($this->polaczenie);
    }
}
