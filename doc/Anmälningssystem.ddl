-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 02 apr 2023 kl 15:53
-- Serverversion: 10.4.25-MariaDB
-- PHP-version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Databas: berghemsvanner_
--

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_npc
--

CREATE TABLE regsys_npc (
  Id int(11) NOT NULL,
  Name varchar(255) DEFAULT NULL,
  Description varchar(255) DEFAULT NULL,
  Time varchar(255) DEFAULT NULL,
  PersonId int(11) DEFAULT NULL,
  NPCGroupId int(11) DEFAULT NULL,
  LARPId int(11) DEFAULT NULL,
  ImageId int(11) DEFAULT NULL,
  IsReleased tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell regsys_npc
--
ALTER TABLE regsys_npc
  ADD PRIMARY KEY (Id),
  ADD KEY PersonId (PersonId),
  ADD KEY LARPId (LARPId),
  ADD KEY ImageId (ImageId),
  ADD KEY NPCGroupId (NPCGroupId);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell regsys_npc
--
ALTER TABLE regsys_npc
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell regsys_npc
--
ALTER TABLE regsys_npc
  ADD CONSTRAINT regsys_npc_ibfk_1 FOREIGN KEY (PersonId) REFERENCES regsys_person (Id),
  ADD CONSTRAINT regsys_npc_ibfk_2 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id),
  ADD CONSTRAINT regsys_npc_ibfk_3 FOREIGN KEY (ImageId) REFERENCES regsys_image (Id),
  ADD CONSTRAINT regsys_npc_ibfk_4 FOREIGN KEY (NPCGroupId) REFERENCES regsys_npcgroup (Id);
COMMIT;
