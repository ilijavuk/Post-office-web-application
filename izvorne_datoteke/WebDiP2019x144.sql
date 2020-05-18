-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 05. Apr 2020 um 12:45
-- Server-Version: 5.5.62-0+deb8u1
-- PHP-Version: 7.2.25-1+0~20191128.32+debian8~1.gbp108445

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `WebDiP2019x144`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dnevnik`
--

CREATE TABLE `dnevnik` (
  `dnevnik_id` int(11) NOT NULL,
  `id_korisnik` int(11) NOT NULL,
  `id_tip` int(11) NOT NULL,
  `radnja` text NOT NULL,
  `upit` text NOT NULL,
  `vrijeme` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `dnevnik`
--

INSERT INTO `dnevnik` (`dnevnik_id`, `id_korisnik`, `id_tip`, `radnja`, `upit`, `vrijeme`) VALUES
(1, 2, 1, '', '', '2020-04-04 10:00:00'),
(2, 1, 1, '', '', '2020-04-04 12:00:00'),
(3, 4, 1, '', '', '2020-04-01 11:00:00'),
(4, 3, 1, '', '', '2020-04-03 17:30:00'),
(5, 8, 1, '', '', '2020-03-31 07:00:00'),
(6, 10, 1, '', '', '2020-04-03 12:00:00'),
(7, 1, 1, '', '', '2020-04-01 19:00:00'),
(8, 4, 1, '', '', '2020-03-31 15:00:00'),
(9, 3, 1, '', '', '2020-04-02 10:00:00'),
(10, 1, 1, '', '', '2020-03-30 08:00:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `drzava`
--

CREATE TABLE `drzava` (
  `drzava_id` int(11) NOT NULL,
  `naziv` varchar(35) NOT NULL,
  `skraceniOblik` varchar(7) NOT NULL,
  `produzeniOblik` varchar(70) DEFAULT NULL,
  `clanEU` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `drzava`
--

INSERT INTO `drzava` (`drzava_id`, `naziv`, `skraceniOblik`, `produzeniOblik`, `clanEU`) VALUES
(1, 'Hrvatska', 'RH', 'Republika Hrvatska', 1),
(2, 'Velika Britanija', 'GB', 'Ujedinjena Kraljevina Velike Britanije i Sjeverne Irske', 0),
(3, 'Njemačka', 'DE', 'Savezna Republika Njemačka', 1),
(4, 'Švicarska', 'CH', 'Švicarska Konfederacija', 0),
(5, 'Slovenija', 'SI', 'Republika Slovenija', 1),
(6, 'Belgija', 'BEL', 'Kraljevina Belgija', 1),
(7, 'Bosna i Hercegovina', 'BIH', 'Bosna i Hercegovina', 0),
(8, 'Srbija', 'RS', 'Republika Srbija', 0),
(9, 'Mađarska', 'HUN', 'Republika Mađarska', 1),
(10, 'Makedonija', 'MKD', 'Republika Makedonija', 0),
(11, 'Švedska', 'SWE', 'Kraljevina Švedska ', 1),
(12, 'Španjolska', 'ESP', 'Kraljevina Španjolska', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `korisnik`
--

CREATE TABLE `korisnik` (
  `korisnik_id` int(11) NOT NULL,
  `id_uloga` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  `ime` varchar(45) NOT NULL,
  `prezime` varchar(45) NOT NULL,
  `slika` text NOT NULL,
  `korisnicko_ime` varchar(25) NOT NULL,
  `lozinka` varchar(25) NOT NULL,
  `lozinka_sha1` char(40) NOT NULL,
  `email` varchar(45) NOT NULL,
  `uvjeti` tinyint(1) DEFAULT NULL,
  `linkAktivacije` text,
  `blokiranDo` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `korisnik`
--

INSERT INTO `korisnik` (`korisnik_id`, `id_uloga`, `id_status`, `ime`, `prezime`, `slika`, `korisnicko_ime`, `lozinka`, `lozinka_sha1`, `email`, `uvjeti`, `linkAktivacije`, `blokiranDo`) VALUES
(1, 1, 2, 'Ilija', 'Vuk', 'images/users/ivuk.png', 'ivuk', 'ivuk123', 'e090674387e2338a9ec504f428ced4a751b29a48', 'ivuk@foi.hr', 1, NULL, NULL),
(2, 2, 2, 'Luka', 'Matić', 'images/users/lmatic.jpg', 'lmatic', 'lmatic123', '4a736972851a35abd7cab9e950bb82b0b1269e2b', 'lmatic@foi.hr', 0, NULL, NULL),
(3, 3, 2, 'Toni', 'Anić', 'images/users/default.jpg', 'tanic', 'tanic', '4a56e64fc62413b95832ef2587372028ea3398d2', 'tanic@foi.hr', 1, NULL, NULL),
(4, 3, 1, 'Vlatka', 'Petković', 'images/users/default.jpg', 'vpetkov', 'vpetkov', '7e2573abef14c711c3013852cc3751f115367842', 'vpetkov@foi.hr', 1, 'http://barka.foi.hr/WebDiP/2019/zadaca_02/ivuk/aktivacija.php?ime=vlatka&prezime=petkovic&username=vpetkov?', NULL),
(5, 3, 3, 'Ivanka', 'Bačić', 'images/users/default.jpg', 'ibacic', 'ibacic', '3428a1b277809621152d9e3c2691ea1365e3d587', 'ibacic@foi.hr', 0, NULL, '2020-04-15 00:00:00'),
(6, 2, 2, 'Slavenka', 'Perković', 'images/users/sperkov.jpg', 'sperkov', 'sperkov', '4d5e4c2e42fbdaaf26f8f8269dcf1c1759b61ecb', 'sperkov', 1, NULL, NULL),
(7, 3, 2, 'Mira', 'Marjanović', 'images/users/default.jpg', 'mmarjanov', 'mmarjanov', 'dab5bdf317c309afc1ada73b2d0fe125719e16bd', 'mmarjanov@foi.hr', 1, NULL, NULL),
(8, 3, 2, 'Hrvoslav', 'Vidović', 'images/users/default.jpg', 'hvidovic', 'hvidovic123', '7164ecc2dfcbd6f526b59a047b5f813a83bd62b1', 'hvidovic@foi.hr', 0, NULL, NULL),
(9, 2, 2, 'Marjan', 'Jakšić', 'images/users/mjaksic.jpg', 'mjaksic', 'mjaksic', '197bb9c287d28e191d5673fb1ed43cb53dc400cb', 'mjaksic', 1, NULL, NULL),
(10, 3, 2, 'Domagoj', 'Duvnjak', 'images/users/default.jpg', 'dduvnjak', 'dduvnjak', '4c99e39a784541ba842cca915e971da28b476d9f', 'dduvnjak@foi.hr', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `posiljka`
--

CREATE TABLE `posiljka` (
  `posiljka_id` int(11) NOT NULL,
  `id_posiljatelja` int(11) NOT NULL,
  `id_primatelja` int(11) NOT NULL,
  `id_konacniUred` int(11) DEFAULT NULL,
  `id_trenutniUred` int(11) DEFAULT NULL,
  `spremnaZaIsporuku` tinyint(1) NOT NULL,
  `cijenaPoKg` double DEFAULT NULL,
  `masa` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `posiljka`
--

INSERT INTO `posiljka` (`posiljka_id`, `id_posiljatelja`, `id_primatelja`, `id_konacniUred`, `id_trenutniUred`, `spremnaZaIsporuku`, `cijenaPoKg`, `masa`) VALUES
(1, 1, 2, 2, 1, 0, 15, 1.5),
(2, 2, 3, 8, 8, 1, 15, 2.3),
(3, 4, 2, NULL, NULL, 0, 20, 2.5),
(4, 7, 4, 1, 1, 1, 30, 3.7),
(5, 2, 1, 9, 7, 0, 15, 1.3),
(6, 4, 6, 5, 5, 1, 15, 1.8),
(7, 4, 1, 8, 3, 0, 20, 2.34),
(8, 5, 7, NULL, NULL, 0, 0, 1.7),
(9, 7, 2, NULL, NULL, 0, NULL, 2.3),
(10, 1, 7, 8, 8, 1, 25, 2.7),
(11, 4, 2, 5, 5, 1, 20, 2.5),
(12, 7, 4, 1, 1, 1, 25, 3.7),
(13, 2, 1, 9, 7, 0, 15, 1.3),
(14, 4, 6, 5, 5, 1, 15, 1.8),
(15, 4, 1, 8, 3, 0, 20, 2.34),
(16, 5, 7, NULL, NULL, 0, 20, 1.7),
(17, 7, 2, NULL, NULL, 0, NULL, 2.3),
(18, 1, 7, 8, 8, 1, 25, 2.7),
(19, 4, 4, 4, 4, 1, 20, 2.45),
(20, 2, 1, 2, 2, 1, 40, 4.5);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `postanskiUred`
--

CREATE TABLE `postanskiUred` (
  `postanskiUred_id` int(11) NOT NULL,
  `id_moderatora` int(11) NOT NULL,
  `id_drzave` int(11) NOT NULL,
  `naziv` varchar(45) NOT NULL,
  `adresa` varchar(60) NOT NULL,
  `postanskiBroj` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `postanskiUred`
--

INSERT INTO `postanskiUred` (`postanskiUred_id`, `id_moderatora`, `id_drzave`, `naziv`, `adresa`, `postanskiBroj`) VALUES
(1, 2, 1, 'Poštanski Ured Zagreb', 'Ul. Kneza Branimira 4, Zagreb', '10000'),
(2, 6, 1, 'Poštanski Ured Zagreb', 'Jurišićeva ul. 13, Zagreb', '10101'),
(3, 2, 1, 'Poštanski Ured Zagreb-Šestine', 'Šestinski trg 10, Zagreb', '10168'),
(4, 9, 3, 'Deutsche Post Suhl', 'Ilmenauer Str. 12, Suhl', '98527'),
(5, 6, 1, 'Poštanski Ured Varaždin - Središte', 'Trg slobode 9, Varaždin', '42000'),
(6, 9, 3, 'Deutsche Post München', 'Agnesstraße 1-5, München', '80801'),
(7, 2, 1, 'Poštanski Ured Varaždin', 'Ul. Miroslava Krleže 1A, Varaždin', '42000'),
(8, 2, 1, 'Poštanski Ured Daruvar', 'Ul. Josipa Jelačića 6, Daruvar', '43500'),
(9, 6, 1, 'Poštanski Ured Bjelovar', ' Ul. Ljudevita Gaja 2,Bjelovar', '43000'),
(10, 9, 3, 'Deutsche Post München', 'Rundfunkpl. 4, München', '80335');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `postavke`
--

CREATE TABLE `postavke` (
  `postavke_id` int(11) NOT NULL,
  `id_administratora` int(11) NOT NULL,
  `stranicenje` int(11) NOT NULL,
  `trajanjeKolacica` time DEFAULT NULL,
  `tema` varchar(15) DEFAULT NULL,
  `bojaPozadine` char(7) DEFAULT NULL,
  `velicinaFonta` int(11) NOT NULL,
  `bojaFonta` char(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `postavke`
--

INSERT INTO `postavke` (`postavke_id`, `id_administratora`, `stranicenje`, `trajanjeKolacica`, `tema`, `bojaPozadine`, `velicinaFonta`, `bojaFonta`) VALUES
(1, 1, 7, '02:00:00', 'Grey', '#C0C0C0', 30, '#000000');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `racun`
--

CREATE TABLE `racun` (
  `racun_id` int(11) NOT NULL,
  `id_posiljka` int(11) NOT NULL,
  `vrijemeIzdavanja` datetime NOT NULL,
  `placen` tinyint(1) NOT NULL,
  `iznos` double NOT NULL,
  `puniIznos` double NOT NULL,
  `slika` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `racun`
--

INSERT INTO `racun` (`racun_id`, `id_posiljka`, `vrijemeIzdavanja`, `placen`, `iznos`, `puniIznos`, `slika`) VALUES
(1, 4, '2020-04-03 10:00:00', 1, 111, 133, NULL),
(2, 10, '2020-04-01 10:40:00', 0, 67.5, 81, NULL),
(3, 6, '2020-04-03 13:00:00', 0, 27, 32.4, NULL),
(4, 12, '2020-03-19 08:00:00', 1, 92.5, 111, NULL),
(5, 14, '2020-03-31 12:35:00', 1, 27, 32.4, NULL),
(6, 18, '2020-04-01 14:00:00', 0, 67.5, 81, NULL),
(7, 11, '2020-04-02 13:00:00', 0, 50, 60, NULL),
(8, 2, '2020-04-04 11:00:00', 1, 19.5, 23.4, NULL),
(9, 19, '2020-04-01 16:00:00', 0, 49, 58.8, NULL),
(10, 20, '2020-03-29 15:00:00', 1, 180, 216, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `status`
--

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `naziv` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `status`
--

INSERT INTO `status` (`status_id`, `naziv`) VALUES
(1, 'Čeka aktivaciju'),
(2, 'Aktiviran'),
(3, 'Blokiran');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tip`
--

CREATE TABLE `tip` (
  `tip_id` int(11) NOT NULL,
  `naziv` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tip`
--

INSERT INTO `tip` (`tip_id`, `naziv`) VALUES
(1, 'prijava/odjava'),
(2, 'rad s bazom'),
(3, 'ostale radnje');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `uloga`
--

CREATE TABLE `uloga` (
  `uloga_id` int(11) NOT NULL,
  `naziv` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `uloga`
--

INSERT INTO `uloga` (`uloga_id`, `naziv`) VALUES
(1, 'Administrator'),
(2, 'Moderator'),
(3, 'Registrirani korisnik');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `dnevnik`
--
ALTER TABLE `dnevnik`
  ADD PRIMARY KEY (`dnevnik_id`,`id_korisnik`,`id_tip`),
  ADD KEY `fk_dnevnik_korisnik1_idx` (`id_korisnik`),
  ADD KEY `fk_dnevnik_tip1_idx` (`id_tip`);

--
-- Indizes für die Tabelle `drzava`
--
ALTER TABLE `drzava`
  ADD PRIMARY KEY (`drzava_id`);

--
-- Indizes für die Tabelle `korisnik`
--
ALTER TABLE `korisnik`
  ADD PRIMARY KEY (`korisnik_id`),
  ADD KEY `fk_korisnik_uloga_idx` (`id_uloga`),
  ADD KEY `fk_korisnik_status1_idx` (`id_status`);

--
-- Indizes für die Tabelle `posiljka`
--
ALTER TABLE `posiljka`
  ADD PRIMARY KEY (`posiljka_id`),
  ADD KEY `fk_posiljka_korisnik1_idx` (`id_posiljatelja`),
  ADD KEY `fk_posiljka_korisnik2_idx` (`id_primatelja`),
  ADD KEY `fk_posiljka_postanskiUred1_idx` (`id_konacniUred`),
  ADD KEY `fk_posiljka_postanskiUred2_idx` (`id_trenutniUred`);

--
-- Indizes für die Tabelle `postanskiUred`
--
ALTER TABLE `postanskiUred`
  ADD PRIMARY KEY (`postanskiUred_id`),
  ADD KEY `fk_postanskiUred_korisnik1_idx` (`id_moderatora`),
  ADD KEY `fk_postanskiUred_drzava1_idx` (`id_drzave`);

--
-- Indizes für die Tabelle `postavke`
--
ALTER TABLE `postavke`
  ADD PRIMARY KEY (`postavke_id`),
  ADD KEY `fk_postavke_korisnik1_idx` (`id_administratora`);

--
-- Indizes für die Tabelle `racun`
--
ALTER TABLE `racun`
  ADD PRIMARY KEY (`racun_id`);

--
-- Indizes für die Tabelle `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indizes für die Tabelle `tip`
--
ALTER TABLE `tip`
  ADD PRIMARY KEY (`tip_id`);

--
-- Indizes für die Tabelle `uloga`
--
ALTER TABLE `uloga`
  ADD PRIMARY KEY (`uloga_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `dnevnik`
--
ALTER TABLE `dnevnik`
  MODIFY `dnevnik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT für Tabelle `drzava`
--
ALTER TABLE `drzava`
  MODIFY `drzava_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT für Tabelle `korisnik`
--
ALTER TABLE `korisnik`
  MODIFY `korisnik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT für Tabelle `posiljka`
--
ALTER TABLE `posiljka`
  MODIFY `posiljka_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT für Tabelle `postanskiUred`
--
ALTER TABLE `postanskiUred`
  MODIFY `postanskiUred_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT für Tabelle `postavke`
--
ALTER TABLE `postavke`
  MODIFY `postavke_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `racun`
--
ALTER TABLE `racun`
  MODIFY `racun_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT für Tabelle `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `tip`
--
ALTER TABLE `tip`
  MODIFY `tip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `uloga`
--
ALTER TABLE `uloga`
  MODIFY `uloga_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `dnevnik`
--
ALTER TABLE `dnevnik`
  ADD CONSTRAINT `fk_dnevnik_korisnik1` FOREIGN KEY (`id_korisnik`) REFERENCES `korisnik` (`korisnik_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dnevnik_tip1` FOREIGN KEY (`id_tip`) REFERENCES `tip` (`tip_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `korisnik`
--
ALTER TABLE `korisnik`
  ADD CONSTRAINT `fk_korisnik_status1` FOREIGN KEY (`id_status`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_korisnik_uloga` FOREIGN KEY (`id_uloga`) REFERENCES `uloga` (`uloga_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `posiljka`
--
ALTER TABLE `posiljka`
  ADD CONSTRAINT `fk_posiljka_korisnik1` FOREIGN KEY (`id_posiljatelja`) REFERENCES `korisnik` (`korisnik_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_posiljka_korisnik2` FOREIGN KEY (`id_primatelja`) REFERENCES `korisnik` (`korisnik_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_posiljka_postanskiUred1` FOREIGN KEY (`id_konacniUred`) REFERENCES `postanskiUred` (`postanskiUred_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_posiljka_postanskiUred2` FOREIGN KEY (`id_trenutniUred`) REFERENCES `postanskiUred` (`postanskiUred_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `postanskiUred`
--
ALTER TABLE `postanskiUred`
  ADD CONSTRAINT `fk_postanskiUred_korisnik1` FOREIGN KEY (`id_moderatora`) REFERENCES `korisnik` (`korisnik_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_postanskiUred_drzava1` FOREIGN KEY (`id_drzave`) REFERENCES `drzava` (`drzava_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `postavke`
--
ALTER TABLE `postavke`
  ADD CONSTRAINT `fk_postavke_korisnik1` FOREIGN KEY (`id_administratora`) REFERENCES `korisnik` (`korisnik_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
