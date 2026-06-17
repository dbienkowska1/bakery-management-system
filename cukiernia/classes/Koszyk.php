<?php

class Koszyk
{
    private function &referencja()
    {
        if (!isset($_SESSION['koszyk']) || !is_array($_SESSION['koszyk'])) {
            $_SESSION['koszyk'] = [];
        }

        return $_SESSION['koszyk'];
    }

    public function dodaj(int $idCiasta, int $ilosc = 1): void
    {
        $idCiasta = (int)$idCiasta;
        $ilosc = max(1, (int)$ilosc);

        $koszyk = &$this->referencja();

        if (!isset($koszyk[$idCiasta])) {
            $koszyk[$idCiasta] = 0;
        }

        $koszyk[$idCiasta] += $ilosc;
    }

    public function usun(int $idCiasta): void
    {
        $koszyk = &$this->referencja();
        unset($koszyk[(int)$idCiasta]);
    }

    public function zwieksz(int $idCiasta): void
    {
        $this->dodaj($idCiasta, 1);
    }

    public function zmniejsz(int $idCiasta): void
    {
        $koszyk = &$this->referencja();
        $idCiasta = (int)$idCiasta;

        if (isset($koszyk[$idCiasta])) {
            $koszyk[$idCiasta]--;
            if ($koszyk[$idCiasta] <= 0) {
                unset($koszyk[$idCiasta]);
            }
        }
    }

    public function ustawIlosc(int $idCiasta, int $ilosc): void
    {
        $koszyk = &$this->referencja();
        $idCiasta = (int)$idCiasta;
        $ilosc = (int)$ilosc;

        if ($ilosc <= 0) {
            unset($koszyk[$idCiasta]);
            return;
        }

        $koszyk[$idCiasta] = $ilosc;
    }

    public function pobierz(): array
    {
        $koszyk = &$this->referencja();
        return $koszyk;
    }

    public function czyPusty(): bool
    {
        return empty($this->pobierz());
    }

    public function wyczysc(): void
    {
        unset($_SESSION['koszyk']);
    }

    public function liczbaProduktow(): int
    {
        return array_sum($this->pobierz());
    }

    public function pobierzPozycje(mysqli $polaczenie): array
    {
        $pozycje = [];
        foreach ($this->pobierz() as $idCiasta => $ilosc) {
            $idCiasta = (int)$idCiasta;

            $wynik = mysqli_query(
                $polaczenie,
                "SELECT c.*, k.nazwa AS kategoria
                 FROM ciasta c
                 JOIN kategorie k ON k.id_kategorii = c.id_kategorii
                 WHERE c.id_ciasta = $idCiasta
                 LIMIT 1"
            );

            if ($wynik && ($row = mysqli_fetch_assoc($wynik))) {
                $row['ilosc'] = (int)$ilosc;
                $pozycje[] = $row;
            }
        }

        return $pozycje;
    }

    public function wyliczSume(mysqli $polaczenie): float
    {
        $suma = 0.0;
        foreach ($this->pobierz() as $idCiasta => $ilosc) {
            $idCiasta = (int)$idCiasta;

            $wynik = mysqli_query(
                $polaczenie,
                "SELECT cena FROM ciasta WHERE id_ciasta = $idCiasta LIMIT 1"
            );

            if ($wynik && ($row = mysqli_fetch_assoc($wynik))) {
                $suma += ((float)$row['cena']) * (int)$ilosc;
            }
        }

        return $suma;
    }
}
