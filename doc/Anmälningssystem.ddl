-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 02 apr 2023 kl 15:39
-- Serverversion: 10.4.25-MariaDB
-- PHP-version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Databas: berghemsvanner_
--
CREATE DATABASE IF NOT EXISTS berghemsvanner_ DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE berghemsvanner_;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_campaign
--

CREATE TABLE regsys_campaign (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Abbreviation varchar(255) NOT NULL,
  Description varchar(255) DEFAULT NULL,
  Icon varchar(255) NOT NULL,
  Homepage varchar(255) NOT NULL,
  Email varchar(255) NOT NULL,
  Bankaccount varchar(255) NOT NULL,
  MinimumAge int(11) DEFAULT NULL,
  MinimumAgeWithoutGuardian int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_experience
--

CREATE TABLE regsys_experience (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text NOT NULL,
  Active tinyint(1) NOT NULL DEFAULT 1,
  SortOrder int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_group
--

CREATE TABLE regsys_group (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Friends text DEFAULT NULL,
  Description text NOT NULL,
  Enemies text DEFAULT NULL,
  IntrigueIdeas text DEFAULT NULL,
  OtherInformation text DEFAULT NULL,
  WealthId int(11) NOT NULL,
  PlaceOfResidenceId int(11) NOT NULL,
  PersonId int(11) NOT NULL,
  CampaignId int(11) NOT NULL,
  IsDead tinyint(1) NOT NULL DEFAULT 0,
  DescriptionForOthers mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_house
--

CREATE TABLE regsys_house (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  NumberOfBeds varchar(255) NOT NULL,
  PositionInVillage text NOT NULL,
  Description text NOT NULL,
  ImageId int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_housecaretaker
--

CREATE TABLE regsys_housecaretaker (
  AdditionalPlayers int(10) NOT NULL,
  LARPId int(11) NOT NULL,
  HouseId int(11) NOT NULL,
  PersonId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_housing
--

CREATE TABLE regsys_housing (
  LARPId int(11) NOT NULL,
  PersonId int(11) NOT NULL,
  HouseId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_housingrequest
--

CREATE TABLE regsys_housingrequest (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text NOT NULL,
  Active tinyint(1) NOT NULL DEFAULT 1,
  SortOrder int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_image
--

CREATE TABLE regsys_image (
  Id int(11) NOT NULL,
  file_name varchar(255) NOT NULL,
  file_mime varchar(255) NOT NULL,
  file_data longblob NOT NULL,
  Photographer varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_intriguetype
--

CREATE TABLE regsys_intriguetype (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text NOT NULL,
  Active tinyint(111) NOT NULL DEFAULT 1,
  SortOrder int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_intriguetype_larp_group
--

CREATE TABLE regsys_intriguetype_larp_group (
  LARP_GroupGroupId int(11) NOT NULL,
  LARP_GroupLARPId int(11) NOT NULL,
  IntrigueTypeId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_intriguetype_larp_role
--

CREATE TABLE regsys_intriguetype_larp_role (
  IntrigueTypeId int(11) NOT NULL,
  LARP_RoleLARPid int(11) NOT NULL,
  LARP_RoleRoleId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_larp
--

CREATE TABLE regsys_larp (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  TagLine varchar(255) DEFAULT NULL,
  StartDate datetime NOT NULL,
  EndDate datetime NOT NULL,
  MaxParticipants int(11) NOT NULL,
  LatestRegistrationDate date DEFAULT NULL,
  StartTimeLARPTime datetime DEFAULT NULL,
  EndTimeLARPTime datetime DEFAULT NULL,
  DisplayIntrigues tinyint(1) NOT NULL,
  CampaignId int(11) NOT NULL,
  RegistrationOpen tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_larpertype
--

CREATE TABLE regsys_larpertype (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text NOT NULL,
  Active tinyint(1) NOT NULL DEFAULT 1,
  SortOrder int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_larp_group
--

CREATE TABLE regsys_larp_group (
  GroupId int(11) NOT NULL,
  LARPId int(11) NOT NULL,
  WantIntrigue tinyint(1) NOT NULL,
  Intrigue text DEFAULT NULL,
  RemainingIntrigues text DEFAULT NULL,
  ApproximateNumberOfMembers int(10) NOT NULL,
  HousingRequestId int(11) NOT NULL,
  NeedFireplace tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_larp_role
--

CREATE TABLE regsys_larp_role (
  LARPId int(11) NOT NULL,
  RoleId int(11) NOT NULL,
  Intrigue text DEFAULT NULL,
  WhatHappened text DEFAULT NULL,
  WhatHappendToOthers text DEFAULT NULL,
  StartingMoney int(11) DEFAULT NULL,
  EndingMoney int(11) DEFAULT NULL,
  Result text DEFAULT NULL,
  IsMainRole tinyint(1) NOT NULL,
  UserMayEdit tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_normalallergytype
--

CREATE TABLE regsys_normalallergytype (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text DEFAULT NULL,
  Active tinyint(1) NOT NULL DEFAULT 1,
  SortOrder int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_normalallergytype_person
--

CREATE TABLE regsys_normalallergytype_person (
  NormalAllergyTypeId int(11) NOT NULL,
  PersonId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  ImageId int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_npcgroup
--

CREATE TABLE regsys_npcgroup (
  Id int(11) NOT NULL,
  Name varchar(255) DEFAULT NULL,
  Description varchar(255) DEFAULT NULL,
  Time varchar(255) DEFAULT NULL,
  LARPId int(11) DEFAULT NULL,
  IsReleased tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_officialtype
--

CREATE TABLE regsys_officialtype (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text NOT NULL,
  Active tinyint(1) NOT NULL DEFAULT 1,
  SortOrder int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_officialtype_person
--

CREATE TABLE regsys_officialtype_person (
  OfficialTypeId int(11) NOT NULL,
  RegistrationId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_paymentinformation
--

CREATE TABLE regsys_paymentinformation (
  Id int(11) NOT NULL,
  LARPId int(11) NOT NULL,
  FromDate date NOT NULL,
  ToDate date NOT NULL,
  FromAge int(11) NOT NULL,
  ToAge int(11) NOT NULL,
  Cost int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_person
--

CREATE TABLE regsys_person (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  SocialSecurityNumber varchar(255) NOT NULL,
  PhoneNumber varchar(255) DEFAULT NULL,
  EmergencyContact text DEFAULT NULL,
  Email varchar(255) NOT NULL,
  FoodAllergiesOther text DEFAULT NULL,
  TypeOfLarperComment text DEFAULT NULL,
  OtherInformation text DEFAULT NULL,
  ExperienceId int(11) NOT NULL,
  TypeOfFoodId int(11) NOT NULL,
  LarperTypeId int(11) NOT NULL,
  UserId int(11) NOT NULL,
  NotAcceptableIntrigues varchar(255) DEFAULT NULL,
  HouseId int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_placeofresidence
--

CREATE TABLE regsys_placeofresidence (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text NOT NULL,
  Active tinyint(1) NOT NULL DEFAULT 1,
  SortOrder int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_prop
--

CREATE TABLE regsys_prop (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text DEFAULT NULL,
  StorageLocation varchar(255) DEFAULT NULL,
  GroupId int(11) DEFAULT NULL,
  RoleId int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL,
  ImageId int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_registration
--

CREATE TABLE regsys_registration (
  Id int(11) NOT NULL,
  LARPId int(11) NOT NULL,
  PersonId int(11) NOT NULL,
  Approved date DEFAULT NULL,
  RegisteredAt datetime DEFAULT NULL,
  PaymentReference varchar(255) DEFAULT NULL,
  AmountToPay int(11) DEFAULT NULL,
  AmountPayed int(11) DEFAULT NULL,
  Payed date DEFAULT NULL,
  IsMember tinyint(1) DEFAULT NULL,
  MembershipCheckedAt datetime DEFAULT NULL,
  NotComing tinyint(1) DEFAULT NULL,
  ToBeRefunded int(11) DEFAULT NULL,
  RefundDate date DEFAULT NULL,
  IsOfficial tinyint(1) NOT NULL,
  NPCDesire varchar(255) DEFAULT NULL,
  HousingRequestId int(11) NOT NULL,
  GuardianId int(11) DEFAULT NULL,
  NotComingReason varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_resource
--

CREATE TABLE regsys_resource (
  Id int(11) NOT NULL,
  Name varchar(255) DEFAULT NULL,
  UnitSingular varchar(255) DEFAULT NULL,
  UnitPlural varchar(255) DEFAULT NULL,
  PriceSlowRiver int(11) DEFAULT NULL,
  PriceJunkCity int(11) DEFAULT NULL,
  IsRare tinyint(1) NOT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_resource_titledeed
--

CREATE TABLE regsys_resource_titledeed (
  ResourceId int(11) NOT NULL,
  TitleDeedId int(11) NOT NULL,
  Quantity int(11) DEFAULT NULL,
  QuantityForUpgrade int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_role
--

CREATE TABLE regsys_role (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  IsNPC tinyint(1) NOT NULL,
  Profession varchar(255) NOT NULL,
  Description mediumtext NOT NULL,
  PreviousLarps mediumtext DEFAULT NULL,
  ReasonForBeingInSlowRiver text NOT NULL,
  Religion varchar(255) DEFAULT NULL,
  DarkSecret text NOT NULL,
  DarkSecretIntrigueIdeas varchar(255) NOT NULL,
  IntrigueSuggestions text DEFAULT NULL,
  NotAcceptableIntrigues varchar(255) DEFAULT NULL,
  OtherInformation text DEFAULT NULL,
  PersonId int(11) NOT NULL,
  GroupId int(11) DEFAULT NULL,
  WealthId int(11) NOT NULL,
  PlaceOfResidenceId int(11) NOT NULL,
  Birthplace varchar(255) NOT NULL,
  CharactersWithRelations text DEFAULT NULL,
  CampaignId int(11) NOT NULL,
  ImageId int(11) DEFAULT NULL,
  DescriptionForGroup mediumtext DEFAULT NULL,
  DescriptionForOthers mediumtext DEFAULT NULL,
  IsDead tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_telegram
--

CREATE TABLE regsys_telegram (
  Id int(11) NOT NULL,
  Deliverytime datetime NOT NULL,
  Sender varchar(255) NOT NULL,
  SenderCity varchar(255) NOT NULL,
  Reciever varchar(255) NOT NULL,
  RecieverCity varchar(255) NOT NULL,
  Message text NOT NULL,
  OrganizerNotes text DEFAULT NULL,
  LARPId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_titledeed
--

CREATE TABLE regsys_titledeed (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Location varchar(255) NOT NULL,
  Tradeable tinyint(1) DEFAULT NULL,
  IsTradingPost tinyint(1) NOT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_titledeedresult
--

CREATE TABLE regsys_titledeedresult (
  TitleDeedId int(11) NOT NULL,
  LARPId int(11) NOT NULL,
  Result text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_titledeed_role
--

CREATE TABLE regsys_titledeed_role (
  TitleDeedId int(11) NOT NULL,
  RoleId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_typeoffood
--

CREATE TABLE regsys_typeoffood (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text DEFAULT NULL,
  Active tinyint(1) NOT NULL DEFAULT 1,
  SortOrder int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_user
--

CREATE TABLE regsys_user (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Email varchar(255) NOT NULL,
  Password varchar(255) NOT NULL,
  IsAdmin tinyint(1) NOT NULL DEFAULT 0,
  ActivationCode varchar(255) DEFAULT NULL,
  EmailChangeCode varchar(255) DEFAULT NULL,
  Blocked tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellstruktur regsys_wealth
--

CREATE TABLE regsys_wealth (
  Id int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Description text NOT NULL,
  Active tinyint(1) NOT NULL DEFAULT 1,
  SortOrder int(11) DEFAULT NULL,
  CampaignId int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell regsys_campaign
--
ALTER TABLE regsys_campaign
  ADD PRIMARY KEY (Id);

--
-- Index för tabell regsys_experience
--
ALTER TABLE regsys_experience
  ADD PRIMARY KEY (Id),
  ADD KEY FKExperience134107 (CampaignId);

--
-- Index för tabell regsys_group
--
ALTER TABLE regsys_group
  ADD PRIMARY KEY (Id),
  ADD KEY FKGroup650928 (WealthId),
  ADD KEY FKGroup886041 (PlaceOfResidenceId),
  ADD KEY FKGroup964147 (PersonId),
  ADD KEY FKGroup14482 (CampaignId);

--
-- Index för tabell regsys_house
--
ALTER TABLE regsys_house
  ADD PRIMARY KEY (Id),
  ADD KEY FKHouse702165 (ImageId);

--
-- Index för tabell regsys_housecaretaker
--
ALTER TABLE regsys_housecaretaker
  ADD KEY FKHouseCaret167131 (HouseId),
  ADD KEY FKHouseCaret557191 (LARPId),
  ADD KEY FKHouseCaret326645 (PersonId);

--
-- Index för tabell regsys_housing
--
ALTER TABLE regsys_housing
  ADD KEY FKHousing905119 (LARPId),
  ADD KEY FKHousing135666 (PersonId),
  ADD KEY FKHousing295180 (HouseId);

--
-- Index för tabell regsys_housingrequest
--
ALTER TABLE regsys_housingrequest
  ADD PRIMARY KEY (Id),
  ADD KEY FKHousingReq101250 (CampaignId);

--
-- Index för tabell regsys_image
--
ALTER TABLE regsys_image
  ADD PRIMARY KEY (Id);

--
-- Index för tabell regsys_intriguetype
--
ALTER TABLE regsys_intriguetype
  ADD PRIMARY KEY (Id),
  ADD KEY FKIntrigueTy623945 (CampaignId);

--
-- Index för tabell regsys_intriguetype_larp_group
--
ALTER TABLE regsys_intriguetype_larp_group
  ADD PRIMARY KEY (LARP_GroupGroupId,LARP_GroupLARPId,IntrigueTypeId),
  ADD KEY FKIntrigueTy716976 (IntrigueTypeId);

--
-- Index för tabell regsys_intriguetype_larp_role
--
ALTER TABLE regsys_intriguetype_larp_role
  ADD PRIMARY KEY (IntrigueTypeId,LARP_RoleLARPid,LARP_RoleRoleId);

--
-- Index för tabell regsys_larp
--
ALTER TABLE regsys_larp
  ADD PRIMARY KEY (Id),
  ADD KEY FKLARP267043 (CampaignId);

--
-- Index för tabell regsys_larpertype
--
ALTER TABLE regsys_larpertype
  ADD PRIMARY KEY (Id),
  ADD KEY FKLarperType83591 (CampaignId);

--
-- Index för tabell regsys_larp_group
--
ALTER TABLE regsys_larp_group
  ADD PRIMARY KEY (GroupId,LARPId),
  ADD KEY FKLARP_Group374108 (LARPId),
  ADD KEY FKLARP_Group496361 (HousingRequestId);

--
-- Index för tabell regsys_larp_role
--
ALTER TABLE regsys_larp_role
  ADD PRIMARY KEY (LARPId,RoleId),
  ADD KEY FKLARP_Role421832 (RoleId);

--
-- Index för tabell regsys_normalallergytype
--
ALTER TABLE regsys_normalallergytype
  ADD PRIMARY KEY (Id),
  ADD KEY FKNormalAlle534322 (CampaignId);

--
-- Index för tabell regsys_normalallergytype_person
--
ALTER TABLE regsys_normalallergytype_person
  ADD PRIMARY KEY (NormalAllergyTypeId,PersonId),
  ADD KEY FKNormalAlle960015 (PersonId);

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
-- Index för tabell regsys_npcgroup
--
ALTER TABLE regsys_npcgroup
  ADD PRIMARY KEY (Id),
  ADD KEY LARPId (LARPId);

--
-- Index för tabell regsys_officialtype
--
ALTER TABLE regsys_officialtype
  ADD PRIMARY KEY (Id),
  ADD KEY FKOfficialTy15169 (CampaignId);

--
-- Index för tabell regsys_officialtype_person
--
ALTER TABLE regsys_officialtype_person
  ADD PRIMARY KEY (OfficialTypeId,RegistrationId),
  ADD KEY FKOfficialTy127443 (RegistrationId);

--
-- Index för tabell regsys_paymentinformation
--
ALTER TABLE regsys_paymentinformation
  ADD PRIMARY KEY (Id),
  ADD KEY FKPaymentInf462816 (LARPId);

--
-- Index för tabell regsys_person
--
ALTER TABLE regsys_person
  ADD PRIMARY KEY (Id),
  ADD KEY FKPerson256526 (ExperienceId),
  ADD KEY FKPerson458285 (TypeOfFoodId),
  ADD KEY FKPerson858970 (LarperTypeId),
  ADD KEY FKPerson603198 (UserId),
  ADD KEY FKPerson239050 (HouseId);

--
-- Index för tabell regsys_placeofresidence
--
ALTER TABLE regsys_placeofresidence
  ADD PRIMARY KEY (Id),
  ADD KEY FKPlaceOfRes206849 (CampaignId);

--
-- Index för tabell regsys_prop
--
ALTER TABLE regsys_prop
  ADD PRIMARY KEY (Id),
  ADD KEY FKProp658852 (GroupId),
  ADD KEY FKProp499335 (RoleId),
  ADD KEY FKProp434227 (CampaignId),
  ADD KEY FKProp122262 (ImageId);

--
-- Index för tabell regsys_registration
--
ALTER TABLE regsys_registration
  ADD PRIMARY KEY (Id),
  ADD KEY FKRegistrati709651 (LARPId),
  ADD KEY FKRegistrati940197 (PersonId),
  ADD KEY FKRegistrati831904 (HousingRequestId);

--
-- Index för tabell regsys_resource
--
ALTER TABLE regsys_resource
  ADD PRIMARY KEY (Id),
  ADD KEY FKResource516999 (CampaignId);

--
-- Index för tabell regsys_resource_titledeed
--
ALTER TABLE regsys_resource_titledeed
  ADD PRIMARY KEY (ResourceId,TitleDeedId),
  ADD KEY FKResource_T990336 (TitleDeedId);

--
-- Index för tabell regsys_role
--
ALTER TABLE regsys_role
  ADD PRIMARY KEY (Id),
  ADD KEY IsPlayedBy (PersonId),
  ADD KEY FKRole127269 (WealthId),
  ADD KEY FKRole409701 (PlaceOfResidenceId),
  ADD KEY Ingår i (GroupId),
  ADD KEY FKRole490822 (CampaignId),
  ADD KEY FKRole65667 (ImageId);

--
-- Index för tabell regsys_telegram
--
ALTER TABLE regsys_telegram
  ADD PRIMARY KEY (Id),
  ADD KEY FKTelegram875373 (LARPId);

--
-- Index för tabell regsys_titledeed
--
ALTER TABLE regsys_titledeed
  ADD PRIMARY KEY (Id),
  ADD KEY FKTitleDeed671521 (CampaignId);

--
-- Index för tabell regsys_titledeedresult
--
ALTER TABLE regsys_titledeedresult
  ADD KEY FKTitleDeedR124770 (TitleDeedId),
  ADD KEY FKTitleDeedR868815 (LARPId);

--
-- Index för tabell regsys_titledeed_role
--
ALTER TABLE regsys_titledeed_role
  ADD PRIMARY KEY (TitleDeedId,RoleId),
  ADD KEY FKTitleDeed_784593 (RoleId);

--
-- Index för tabell regsys_typeoffood
--
ALTER TABLE regsys_typeoffood
  ADD PRIMARY KEY (Id),
  ADD KEY FKTypeOfFood933238 (CampaignId);

--
-- Index för tabell regsys_user
--
ALTER TABLE regsys_user
  ADD PRIMARY KEY (Id);

--
-- Index för tabell regsys_wealth
--
ALTER TABLE regsys_wealth
  ADD PRIMARY KEY (Id),
  ADD KEY FKWealth22193 (CampaignId);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell regsys_campaign
--
ALTER TABLE regsys_campaign
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_experience
--
ALTER TABLE regsys_experience
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_group
--
ALTER TABLE regsys_group
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_house
--
ALTER TABLE regsys_house
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_housingrequest
--
ALTER TABLE regsys_housingrequest
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_image
--
ALTER TABLE regsys_image
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_intriguetype
--
ALTER TABLE regsys_intriguetype
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_larp
--
ALTER TABLE regsys_larp
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_larpertype
--
ALTER TABLE regsys_larpertype
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_normalallergytype
--
ALTER TABLE regsys_normalallergytype
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_npc
--
ALTER TABLE regsys_npc
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_npcgroup
--
ALTER TABLE regsys_npcgroup
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_officialtype
--
ALTER TABLE regsys_officialtype
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_paymentinformation
--
ALTER TABLE regsys_paymentinformation
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_person
--
ALTER TABLE regsys_person
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_placeofresidence
--
ALTER TABLE regsys_placeofresidence
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_prop
--
ALTER TABLE regsys_prop
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_registration
--
ALTER TABLE regsys_registration
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_resource
--
ALTER TABLE regsys_resource
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_role
--
ALTER TABLE regsys_role
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_telegram
--
ALTER TABLE regsys_telegram
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_titledeed
--
ALTER TABLE regsys_titledeed
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_typeoffood
--
ALTER TABLE regsys_typeoffood
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_user
--
ALTER TABLE regsys_user
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell regsys_wealth
--
ALTER TABLE regsys_wealth
  MODIFY Id int(11) NOT NULL AUTO_INCREMENT;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell regsys_experience
--
ALTER TABLE regsys_experience
  ADD CONSTRAINT FKExperience134107 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_group
--
ALTER TABLE regsys_group
  ADD CONSTRAINT FKGroup14482 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id),
  ADD CONSTRAINT FKGroup650928 FOREIGN KEY (WealthId) REFERENCES regsys_wealth (Id),
  ADD CONSTRAINT FKGroup886041 FOREIGN KEY (PlaceOfResidenceId) REFERENCES regsys_placeofresidence (Id),
  ADD CONSTRAINT FKGroup964147 FOREIGN KEY (PersonId) REFERENCES regsys_person (Id);

--
-- Restriktioner för tabell regsys_house
--
ALTER TABLE regsys_house
  ADD CONSTRAINT FKHouse702165 FOREIGN KEY (ImageId) REFERENCES regsys_image (Id);

--
-- Restriktioner för tabell regsys_housecaretaker
--
ALTER TABLE regsys_housecaretaker
  ADD CONSTRAINT FKHouseCaret167131 FOREIGN KEY (HouseId) REFERENCES regsys_house (Id),
  ADD CONSTRAINT FKHouseCaret326645 FOREIGN KEY (PersonId) REFERENCES regsys_person (Id),
  ADD CONSTRAINT FKHouseCaret557191 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id);

--
-- Restriktioner för tabell regsys_housing
--
ALTER TABLE regsys_housing
  ADD CONSTRAINT FKHousing135666 FOREIGN KEY (PersonId) REFERENCES regsys_person (Id),
  ADD CONSTRAINT FKHousing295180 FOREIGN KEY (HouseId) REFERENCES regsys_house (Id),
  ADD CONSTRAINT FKHousing905119 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id);

--
-- Restriktioner för tabell regsys_housingrequest
--
ALTER TABLE regsys_housingrequest
  ADD CONSTRAINT FKHousingReq101250 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_intriguetype
--
ALTER TABLE regsys_intriguetype
  ADD CONSTRAINT FKIntrigueTy623945 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_intriguetype_larp_group
--
ALTER TABLE regsys_intriguetype_larp_group
  ADD CONSTRAINT FKIntrigueTy460353 FOREIGN KEY (LARP_GroupGroupId,LARP_GroupLARPId) REFERENCES regsys_larp_group (GroupId, LARPId),
  ADD CONSTRAINT FKIntrigueTy716976 FOREIGN KEY (IntrigueTypeId) REFERENCES regsys_intriguetype (Id);

--
-- Restriktioner för tabell regsys_intriguetype_larp_role
--
ALTER TABLE regsys_intriguetype_larp_role
  ADD CONSTRAINT FKIntrigueTy819563 FOREIGN KEY (IntrigueTypeId) REFERENCES regsys_intriguetype (Id);

--
-- Restriktioner för tabell regsys_larp
--
ALTER TABLE regsys_larp
  ADD CONSTRAINT FKLARP267043 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_larpertype
--
ALTER TABLE regsys_larpertype
  ADD CONSTRAINT FKLarperType83591 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_larp_group
--
ALTER TABLE regsys_larp_group
  ADD CONSTRAINT FKLARP_Group374108 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id),
  ADD CONSTRAINT FKLARP_Group496361 FOREIGN KEY (HousingRequestId) REFERENCES regsys_housingrequest (Id),
  ADD CONSTRAINT FKLARP_Group836318 FOREIGN KEY (GroupId) REFERENCES regsys_group (Id);

--
-- Restriktioner för tabell regsys_larp_role
--
ALTER TABLE regsys_larp_role
  ADD CONSTRAINT FKLARP_Role146219 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id),
  ADD CONSTRAINT FKLARP_Role421832 FOREIGN KEY (RoleId) REFERENCES regsys_role (Id);

--
-- Restriktioner för tabell regsys_normalallergytype
--
ALTER TABLE regsys_normalallergytype
  ADD CONSTRAINT FKNormalAlle534322 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_normalallergytype_person
--
ALTER TABLE regsys_normalallergytype_person
  ADD CONSTRAINT FKNormalAlle861454 FOREIGN KEY (NormalAllergyTypeId) REFERENCES regsys_normalallergytype (Id),
  ADD CONSTRAINT FKNormalAlle960015 FOREIGN KEY (PersonId) REFERENCES regsys_person (Id);

--
-- Restriktioner för tabell regsys_npc
--
ALTER TABLE regsys_npc
  ADD CONSTRAINT regsys_npc_ibfk_1 FOREIGN KEY (PersonId) REFERENCES regsys_person (Id),
  ADD CONSTRAINT regsys_npc_ibfk_2 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id),
  ADD CONSTRAINT regsys_npc_ibfk_3 FOREIGN KEY (ImageId) REFERENCES regsys_image (Id),
  ADD CONSTRAINT regsys_npc_ibfk_4 FOREIGN KEY (NPCGroupId) REFERENCES regsys_npcgroup (Id);

--
-- Restriktioner för tabell regsys_npcgroup
--
ALTER TABLE regsys_npcgroup
  ADD CONSTRAINT regsys_npcgroup_ibfk_1 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id);

--
-- Restriktioner för tabell regsys_officialtype
--
ALTER TABLE regsys_officialtype
  ADD CONSTRAINT FKOfficialTy15169 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_officialtype_person
--
ALTER TABLE regsys_officialtype_person
  ADD CONSTRAINT FKOfficialTy127443 FOREIGN KEY (RegistrationId) REFERENCES regsys_registration (Id),
  ADD CONSTRAINT FKOfficialTy968435 FOREIGN KEY (OfficialTypeId) REFERENCES regsys_officialtype (Id);

--
-- Restriktioner för tabell regsys_paymentinformation
--
ALTER TABLE regsys_paymentinformation
  ADD CONSTRAINT FKPaymentInf462816 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id);

--
-- Restriktioner för tabell regsys_person
--
ALTER TABLE regsys_person
  ADD CONSTRAINT FKPerson239050 FOREIGN KEY (HouseId) REFERENCES regsys_house (Id),
  ADD CONSTRAINT FKPerson256526 FOREIGN KEY (ExperienceId) REFERENCES regsys_experience (Id),
  ADD CONSTRAINT FKPerson458285 FOREIGN KEY (TypeOfFoodId) REFERENCES regsys_typeoffood (Id),
  ADD CONSTRAINT FKPerson603198 FOREIGN KEY (UserId) REFERENCES regsys_user (Id),
  ADD CONSTRAINT FKPerson858970 FOREIGN KEY (LarperTypeId) REFERENCES regsys_larpertype (Id);

--
-- Restriktioner för tabell regsys_placeofresidence
--
ALTER TABLE regsys_placeofresidence
  ADD CONSTRAINT FKPlaceOfRes206849 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_prop
--
ALTER TABLE regsys_prop
  ADD CONSTRAINT FKProp122262 FOREIGN KEY (ImageId) REFERENCES regsys_image (Id),
  ADD CONSTRAINT FKProp434227 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id),
  ADD CONSTRAINT FKProp499335 FOREIGN KEY (RoleId) REFERENCES regsys_role (Id),
  ADD CONSTRAINT FKProp658852 FOREIGN KEY (GroupId) REFERENCES regsys_group (Id);

--
-- Restriktioner för tabell regsys_registration
--
ALTER TABLE regsys_registration
  ADD CONSTRAINT FKRegistrati709651 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id),
  ADD CONSTRAINT FKRegistrati831904 FOREIGN KEY (HousingRequestId) REFERENCES regsys_housingrequest (Id),
  ADD CONSTRAINT FKRegistrati940197 FOREIGN KEY (PersonId) REFERENCES regsys_person (Id);

--
-- Restriktioner för tabell regsys_resource
--
ALTER TABLE regsys_resource
  ADD CONSTRAINT FKResource516999 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_resource_titledeed
--
ALTER TABLE regsys_resource_titledeed
  ADD CONSTRAINT FKResource_T569064 FOREIGN KEY (ResourceId) REFERENCES regsys_resource (Id),
  ADD CONSTRAINT FKResource_T990336 FOREIGN KEY (TitleDeedId) REFERENCES regsys_titledeed (Id);

--
-- Restriktioner för tabell regsys_role
--
ALTER TABLE regsys_role
  ADD CONSTRAINT FKRole127269 FOREIGN KEY (WealthId) REFERENCES regsys_wealth (Id),
  ADD CONSTRAINT FKRole409701 FOREIGN KEY (PlaceOfResidenceId) REFERENCES regsys_placeofresidence (Id),
  ADD CONSTRAINT FKRole490822 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id),
  ADD CONSTRAINT FKRole65667 FOREIGN KEY (ImageId) REFERENCES regsys_image (Id),
  ADD CONSTRAINT Ingår i FOREIGN KEY (GroupId) REFERENCES regsys_group (Id),
  ADD CONSTRAINT IsPlayedBy FOREIGN KEY (PersonId) REFERENCES regsys_person (Id);

--
-- Restriktioner för tabell regsys_telegram
--
ALTER TABLE regsys_telegram
  ADD CONSTRAINT FKTelegram875373 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id);

--
-- Restriktioner för tabell regsys_titledeed
--
ALTER TABLE regsys_titledeed
  ADD CONSTRAINT FKTitleDeed671521 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_titledeedresult
--
ALTER TABLE regsys_titledeedresult
  ADD CONSTRAINT FKTitleDeedR124770 FOREIGN KEY (TitleDeedId) REFERENCES regsys_titledeed (Id),
  ADD CONSTRAINT FKTitleDeedR868815 FOREIGN KEY (LARPId) REFERENCES regsys_larp (Id);

--
-- Restriktioner för tabell regsys_titledeed_role
--
ALTER TABLE regsys_titledeed_role
  ADD CONSTRAINT FKTitleDeed_235065 FOREIGN KEY (TitleDeedId) REFERENCES regsys_titledeed (Id),
  ADD CONSTRAINT FKTitleDeed_784593 FOREIGN KEY (RoleId) REFERENCES regsys_role (Id);

--
-- Restriktioner för tabell regsys_typeoffood
--
ALTER TABLE regsys_typeoffood
  ADD CONSTRAINT FKTypeOfFood933238 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);

--
-- Restriktioner för tabell regsys_wealth
--
ALTER TABLE regsys_wealth
  ADD CONSTRAINT FKWealth22193 FOREIGN KEY (CampaignId) REFERENCES regsys_campaign (Id);
COMMIT;
