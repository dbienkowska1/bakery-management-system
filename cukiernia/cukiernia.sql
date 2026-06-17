CREATE DATABASE IF NOT EXISTS cukiernia CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci;
USE cukiernia;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS pozycje_zamowien;
DROP TABLE IF EXISTS zamowienia;
DROP TABLE IF EXISTS skladnik_alergen;
DROP TABLE IF EXISTS ciasto_skladnik;
DROP TABLE IF EXISTS partie_skladnikow;
DROP TABLE IF EXISTS alergeny;
DROP TABLE IF EXISTS skladniki;
DROP TABLE IF EXISTS ciasta;
DROP TABLE IF EXISTS klienci;
DROP TABLE IF EXISTS status;
DROP TABLE IF EXISTS kategorie;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE kategorie (
    id_kategorii INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazwa VARCHAR(100) NOT NULL,
    opis TEXT DEFAULT NULL,
    PRIMARY KEY (id_kategorii)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE ciasta (
    id_ciasta INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazwa VARCHAR(100) NOT NULL,
    opis TEXT DEFAULT NULL,
    cena DECIMAL(10,2) NOT NULL,
    waga INT NOT NULL,
    czas_przygotowania INT NOT NULL,
    id_kategorii INT UNSIGNED NOT NULL,
    dostepnosc TINYINT(1) NOT NULL DEFAULT 1,
    promowane TINYINT(1) NOT NULL DEFAULT 0,
    zdjecie VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id_ciasta),
    KEY idx_ciasta_kategoria (id_kategorii),
    CONSTRAINT fk_ciasta_kategorie FOREIGN KEY (id_kategorii) REFERENCES kategorie (id_kategorii)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE skladniki (
    id_skladnika INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazwa VARCHAR(100) NOT NULL,
    jednostka ENUM('g','kg','ml','l','szt') NOT NULL,
    PRIMARY KEY (id_skladnika)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE partie_skladnikow (
    id_partii INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_skladnika INT UNSIGNED NOT NULL,
    ilosc DECIMAL(10,2) NOT NULL,
    data_waznosci DATE NOT NULL,
    PRIMARY KEY (id_partii),
    KEY idx_partie_skladnik (id_skladnika),
    CONSTRAINT fk_partie_skladniki FOREIGN KEY (id_skladnika) REFERENCES skladniki (id_skladnika)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE ciasto_skladnik (
    id_ciasta INT UNSIGNED NOT NULL,
    id_skladnika INT UNSIGNED NOT NULL,
    ilosc DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_ciasta, id_skladnika),
    CONSTRAINT fk_cs_ciasta FOREIGN KEY (id_ciasta) REFERENCES ciasta (id_ciasta)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_cs_skladniki FOREIGN KEY (id_skladnika) REFERENCES skladniki (id_skladnika)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE alergeny (
    id_alergenu INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nazwa VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_alergenu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE skladnik_alergen (
    id_skladnika INT UNSIGNED NOT NULL,
    id_alergenu INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_skladnika, id_alergenu),
    CONSTRAINT fk_sa_skladniki FOREIGN KEY (id_skladnika) REFERENCES skladniki (id_skladnika)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_sa_alergeny FOREIGN KEY (id_alergenu) REFERENCES alergeny (id_alergenu)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE klienci (
    id_klienta INT UNSIGNED NOT NULL AUTO_INCREMENT,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(80) NOT NULL,
    telefon VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL,
    haslo VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_klienta),
    UNIQUE KEY uniq_klient_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE status (
    id_statusu INT UNSIGNED NOT NULL AUTO_INCREMENT,
    stan VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_statusu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE zamowienia (
    id_zamowienia INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_klienta INT UNSIGNED NOT NULL,
    data_zamowienia DATETIME NOT NULL,
    data_odbioru DATE NOT NULL,
    id_statusu INT UNSIGNED NOT NULL,
    calkowita_kwota DECIMAL(10,2) NOT NULL,
    uwagi TEXT DEFAULT NULL,
    PRIMARY KEY (id_zamowienia),
    KEY idx_zam_klient (id_klienta),
    KEY idx_zam_status (id_statusu),
    CONSTRAINT fk_zam_klient FOREIGN KEY (id_klienta) REFERENCES klienci (id_klienta)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_zam_status FOREIGN KEY (id_statusu) REFERENCES status (id_statusu)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE pozycje_zamowien (
    id_pozycji INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_zamowienia INT UNSIGNED NOT NULL,
    id_ciasta INT UNSIGNED NOT NULL,
    ilosc INT UNSIGNED NOT NULL,
    cena_jednostkowa DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_pozycji),
    KEY idx_pz_zam (id_zamowienia),
    KEY idx_pz_ciasto (id_ciasta),
    CONSTRAINT fk_pz_zam FOREIGN KEY (id_zamowienia) REFERENCES zamowienia (id_zamowienia)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_pz_ciasto FOREIGN KEY (id_ciasta) REFERENCES ciasta (id_ciasta)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

INSERT INTO kategorie (id_kategorii, nazwa, opis) VALUES
(1, 'Torty', 'Torty okolicznościowe, urodzinowe i weselne.'),
(2, 'Desery', 'Delikatne desery i wypieki do kawy.'),
(3, 'Ciasta tradycyjne', 'Domowe klasyki i wypieki rodzinne.');

INSERT INTO ciasta (id_ciasta, nazwa, opis, cena, waga, czas_przygotowania, id_kategorii, dostepnosc, promowane, zdjecie) VALUES
(1, 'Tort śmietankowy', 'Lekki tort z kremem śmietankowym.', 120.00, 1200, 120, 1, 1, 1, 'images/tort_smietanka.jpg'),
(2, 'Tort wiśnia', 'Tort z wiśniami i czekoladowym biszkoptem.', 200.00, 1500, 180, 1, 1, 1, 'images/tort_wiśnia.jpg'),
(3, 'Ptysie', 'Delikatne ptysie z kremem.', 15.00, 250, 60, 2, 1, 1, 'images/ptysie.jpg'),
(4, 'Miodownik', 'Miodowe warstwy z kremem.', 70.00, 900, 90, 3, 1, 0, 'images/miodownik.jpg'),
(5, 'Malinowa chmurka', 'Delikatne ciasto z malinami i bezą.', 80.00, 1000, 150, 2, 1, 0, 'images/malinowa_chmurka.jpg');

INSERT INTO skladniki (id_skladnika, nazwa, jednostka) VALUES
(1, 'Mąka pszenna', 'g'),
(2, 'Cukier', 'g'),
(3, 'Jajka', 'szt'),
(4, 'Masło 82%', 'g'),
(5, 'Maliny', 'g'),
(6, 'Śmietanka 30%', 'ml'),
(7, 'Miód', 'g');

INSERT INTO ciasto_skladnik (id_ciasta, id_skladnika, ilosc) VALUES
(1, 1, 300),
(1, 2, 150),
(1, 3, 6),
(1, 6, 250),
(2, 1, 250),
(2, 2, 180),
(2, 3, 6),
(2, 5, 200),
(3, 1, 120),
(3, 3, 4),
(3, 6, 200),
(4, 1, 220),
(4, 2, 160),
(4, 4, 180),
(4, 7, 140),
(5, 1, 200),
(5, 2, 170),
(5, 3, 5),
(5, 5, 250),
(5, 6, 200);

INSERT INTO alergeny (id_alergenu, nazwa) VALUES
(1, 'gluten'),
(2, 'mleko'),
(3, 'jajka');

INSERT INTO skladnik_alergen (id_skladnika, id_alergenu) VALUES
(1, 1),
(3, 3),
(4, 2),
(6, 2);

INSERT INTO status (id_statusu, stan) VALUES
(1, 'nowe'),
(2, 'w przygotowaniu'),
(3, 'gotowe'),
(4, 'odebrane'),
(5, 'anulowane');

INSERT INTO klienci (id_klienta, imie, nazwisko, telefon, email, haslo) VALUES
(1, 'Jan', 'Nowak', '111222333', 'jnowak@test.pl', '$2y$12$KyeF5gHp4Rk9Tap2LNBd5eG2UPYJlpM8TdlUyjTJBqIm.NRJP96n2'),
(2, 'Adam', 'Kowalski', '444555666', 'akowalski@test.pl', '$2y$12$KyeF5gHp4Rk9Tap2LNBd5eG2UPYJlpM8TdlUyjTJBqIm.NRJP96n2');

INSERT INTO zamowienia (id_zamowienia, id_klienta, data_zamowienia, data_odbioru, id_statusu, calkowita_kwota, uwagi) VALUES
(1, 1, '2026-05-18 12:00:00', '2026-05-23', 3, 154.80, 'brak'),
(2, 2, '2026-05-17 15:30:00', '2026-05-30', 1, 80.00, '');

INSERT INTO pozycje_zamowien (id_pozycji, id_zamowienia, id_ciasta, ilosc, cena_jednostkowa) VALUES
(1, 1, 1, 1, 120.00),
(2, 1, 3, 2, 15.00),
(3, 2, 2, 1, 80.00);
