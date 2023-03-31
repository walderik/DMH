CREATE TABLE `regsys_campaign` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Abbreviation` varchar(255),
  `Description` varchar(255),
  `Icon` varchar(255),
  `Homepage` varchar(255),
  `Email` varchar(255),
  `Bankaccount` varchar(255),
  `MinimumAge` int,
  `MinimumAgeWithoutGuardian` int,
  PRIMARY KEY (`Id`)
);

CREATE TABLE `regsys_intriguetype` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `Active` tinyint,
  `SortOrder` int,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_intriguetype_larp_role` (
  `IntrigueTypeId` int,
  `LARP_RoleLARPid` int,
  `LARP_RoleRoleId` int,
  PRIMARY KEY (`IntrigueTypeId`, `LARP_RoleLARPid`, `LARP_RoleRoleId`),
  FOREIGN KEY (`IntrigueTypeId`) REFERENCES `regsys_intriguetype`(`Id`)
);

CREATE TABLE `regsys_officialtype` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `Active` tinyint,
  `SortOrder` int,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_image` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255),
  `file_mime` varchar(255),
  `file_data` longblob,
  `Photographer` varchar(255),
  PRIMARY KEY (`Id`)
);

CREATE TABLE `regsys_placeofresidence` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `Active` tinyint,
  `SortOrder` int,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_wealth` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `Active` tinyint,
  `SortOrder` int,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_typeoffood` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `Active` tinyint,
  `SortOrder` int,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_experience` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `Active` tinyint,
  `SortOrder` int,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_house` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `NumberOfBeds` varchar(255),
  `PositionInVillage` text(65535),
  `Description` text(65535),
  `ImageId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`ImageId`) REFERENCES `regsys_image`(`Id`)
);

CREATE TABLE `regsys_larpertype` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `Active` tinyint,
  `SortOrder` int,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_user` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Email` varchar(255),
  `Password` varchar(255),
  `IsAdmin` tinyint,
  `ActivationCode` varchar(255),
  `EmailChangeCode` varchar(255),
  `Blocked` tinyint,
  PRIMARY KEY (`Id`)
);

CREATE TABLE `regsys_person` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `SocialSecurityNumber` varchar(255),
  `PhoneNumber` varchar(255),
  `EmergencyContact` text(65535),
  `Email` varchar(255),
  `FoodAllergiesOther` text(65535),
  `TypeOfLarperComment` text(65535),
  `OtherInformation` text(65535),
  `ExperienceId` int,
  `TypeOfFoodId` int,
  `LarperTypeId` int,
  `UserId` int,
  `NotAcceptableIntrigues` varchar(255),
  `HouseId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`TypeOfFoodId`) REFERENCES `regsys_typeoffood`(`Id`),
  FOREIGN KEY (`ExperienceId`) REFERENCES `regsys_experience`(`Id`),
  FOREIGN KEY (`HouseId`) REFERENCES `regsys_house`(`Id`),
  FOREIGN KEY (`LarperTypeId`) REFERENCES `regsys_larpertype`(`Id`),
  FOREIGN KEY (`UserId`) REFERENCES `regsys_user`(`Id`)
);

CREATE TABLE `regsys_group` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Friends` text(65535),
  `Description` text(65535),
  `DescriptionForOthers` mediumtext,
  `Enemies` text(65535),
  `IntrigueIdeas` text(65535),
  `OtherInformation` text(65535),
  `WealthId` int,
  `PlaceOfResidenceId` int,
  `PersonId` int,
  `CampaignId` int,
  `IsDead` tinyint,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`PlaceOfResidenceId`) REFERENCES `regsys_placeofresidence`(`Id`),
  FOREIGN KEY (`WealthId`) REFERENCES `regsys_wealth`(`Id`),
  FOREIGN KEY (`PersonId`) REFERENCES `regsys_person`(`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_housingrequest` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `Active` tinyint,
  `SortOrder` int,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_larp` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `TagLine` varchar(255),
  `StartDate` datetime,
  `EndDate` datetime,
  `MaxParticipants` int,
  `LatestRegistrationDate` date,
  `StartTimeLARPTime` datetime,
  `EndTimeLARPTime` datetime,
  `DisplayIntrigues` tinyint,
  `CampaignId` int,
  `RegistrationOpen` tinyint,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_larp_group` (
  `GroupId` int,
  `LARPId` int,
  `WantIntrigue` tinyint,
  `Intrigue` text(65535),
  `RemainingIntrigues` text(65535),
  `ApproximateNumberOfMembers` int,
  `HousingRequestId` int,
  `NeedFireplace` tinyint,
  PRIMARY KEY (`GroupId`, `LARPId`),
  FOREIGN KEY (`LARPId`) REFERENCES `regsys_larp`(`Id`),
  FOREIGN KEY (`GroupId`) REFERENCES `regsys_group`(`Id`),
  FOREIGN KEY (`HousingRequestId`) REFERENCES `regsys_housingrequest`(`Id`)
);

CREATE TABLE `regsys_normalallergytype` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `Active` tinyint,
  `SortOrder` int,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_normalallergytype_person` (
  `NormalAllergyTypeId` int,
  `PersonId` int,
  PRIMARY KEY (`NormalAllergyTypeId`, `PersonId`),
  FOREIGN KEY (`PersonId`) REFERENCES `regsys_person`(`Id`),
  FOREIGN KEY (`NormalAllergyTypeId`) REFERENCES `regsys_normalallergytype`(`Id`)
);

CREATE TABLE `regsys_titledeed` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Location` varchar(255),
  `Tradeable` tinyint,
  `IsTradingPost` tinyint,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_resource` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `UnitSingular` varchar(255),
  `UnitPlural` varchar(255),
  `PriceSlowRiver` int,
  `PriceJunkCity` int,
  `IsRare` tinyint,
  `CampaignId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_resource_titledeed` (
  `ResourceId` int,
  `TitleDeedId` int,
  `Quantity` int,
  `QuantityForUpgrade` int,
  PRIMARY KEY (`ResourceId`, `TitleDeedId`),
  FOREIGN KEY (`TitleDeedId`) REFERENCES `regsys_titledeed`(`Id`),
  FOREIGN KEY (`ResourceId`) REFERENCES `regsys_resource`(`Id`)
);

CREATE TABLE `regsys_registration` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `LARPId` int,
  `PersonId` int,
  `Approved` date,
  `RegisteredAt` datetime,
  `PaymentReference` varchar(255),
  `AmountToPay` int,
  `AmountPayed` int,
  `Payed` date,
  `IsMember` tinyint,
  `MembershipCheckedAt` datetime,
  `NotComing` tinyint,
  `ToBeRefunded` int,
  `RefundDate` date,
  `IsOfficial` tinyint,
  `NPCDesire` varchar(255),
  `HousingRequestId` int,
  `GuardianId` int,
  `NotComingReason` varchar(255),
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`HousingRequestId`) REFERENCES `regsys_housingrequest`(`Id`),
  FOREIGN KEY (`LARPId`) REFERENCES `regsys_larp`(`Id`),
  FOREIGN KEY (`PersonId`) REFERENCES `regsys_person`(`Id`)
);

CREATE TABLE `regsys_housing` (
  `LARPId` int,
  `PersonId` int,
  `HouseId` int,
  FOREIGN KEY (`LARPId`) REFERENCES `regsys_larp`(`Id`),
  FOREIGN KEY (`HouseId`) REFERENCES `regsys_house`(`Id`),
  FOREIGN KEY (`PersonId`) REFERENCES `regsys_person`(`Id`)
);

CREATE TABLE `regsys_role` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `IsNPC` tinyint,
  `Profession` varchar(255),
  `Description` mediumtext,
  `PreviousLarps` mediumtext,
  `ReasonForBeingInSlowRiver` text(65535),
  `Religion` varchar(255),
  `DarkSecret` text(65535),
  `DarkSecretIntrigueIdeas` varchar(255),
  `IntrigueSuggestions` text(65535),
  `NotAcceptableIntrigues` varchar(255),
  `OtherInformation` text(65535),
  `PersonId` int,
  `GroupId` int,
  `WealthId` int,
  `PlaceOfResidenceId` int,
  `Birthplace` varchar(255),
  `CharactersWithRelations` text(65535),
  `CampaignId` int,
  `ImageId` int,
  `DescriptionForGroup` mediumtext,
  `DescriptionForOthers` mediumtext,
  `IsDead` tinyint,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`WealthId`) REFERENCES `regsys_wealth`(`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`),
  FOREIGN KEY (`GroupId`) REFERENCES `regsys_group`(`Id`),
  FOREIGN KEY (`PersonId`) REFERENCES `regsys_person`(`Id`),
  FOREIGN KEY (`PlaceOfResidenceId`) REFERENCES `regsys_placeofresidence`(`Id`),
  FOREIGN KEY (`ImageId`) REFERENCES `regsys_image`(`Id`)
);

CREATE TABLE `regsys_larp_role` (
  `LARPId` int,
  `RoleId` int,
  `Intrigue` text(65535),
  `WhatHappened` text(65535),
  `WhatHappendToOthers` text(65535),
  `StartingMoney` int,
  `EndingMoney` int,
  `Result` text(65535),
  `UserMayEdit` tinyint,
  `IsMainRole` tinyint,
  PRIMARY KEY (`LARPId`, `RoleId`),
  FOREIGN KEY (`RoleId`) REFERENCES `regsys_role`(`Id`),
  FOREIGN KEY (`LARPId`) REFERENCES `regsys_larp`(`Id`)
);

CREATE TABLE `regsys_prop` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` text(65535),
  `StorageLocation` varchar(255),
  `GroupId` int,
  `RoleId` int,
  `CampaignId` int,
  `ImageId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`ImageId`) REFERENCES `regsys_image`(`Id`),
  FOREIGN KEY (`RoleId`) REFERENCES `regsys_role`(`Id`),
  FOREIGN KEY (`GroupId`) REFERENCES `regsys_group`(`Id`),
  FOREIGN KEY (`CampaignId`) REFERENCES `regsys_campaign`(`Id`)
);

CREATE TABLE `regsys_paymentinformation` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `LARPId` int,
  `FromDate` date,
  `ToDate` date,
  `FromAge` int,
  `ToAge` int,
  `Cost` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`LARPId`) REFERENCES `regsys_larp`(`Id`)
);

CREATE TABLE `regsys_housecaretaker` (
  `AdditionalPlayers` int,
  `LARPId` int,
  `HouseId` int,
  `PersonId` int,
  FOREIGN KEY (`LARPId`) REFERENCES `regsys_larp`(`Id`),
  FOREIGN KEY (`HouseId`) REFERENCES `regsys_house`(`Id`),
  FOREIGN KEY (`PersonId`) REFERENCES `regsys_person`(`Id`)
);

CREATE TABLE `regsys_intriguetype_larp_group` (
  `LARP_GroupGroupId` int,
  `LARP_GroupLARPId` int,
  `IntrigueTypeId` int,
  PRIMARY KEY (`LARP_GroupGroupId`, `LARP_GroupLARPId`, `IntrigueTypeId`),
  FOREIGN KEY (`IntrigueTypeId`) REFERENCES `regsys_intriguetype`(`Id`),
  FOREIGN KEY (`LARP_GroupLARPId`) REFERENCES `regsys_larp_group`(`LARPId`),
  FOREIGN KEY (`LARP_GroupGroupId`) REFERENCES `regsys_larp_group`(`GroupId`)
);

CREATE TABLE `regsys_titledeed_role` (
  `TitleDeedId` int,
  `RoleId` int,
  PRIMARY KEY (`TitleDeedId`, `RoleId`),
  FOREIGN KEY (`TitleDeedId`) REFERENCES `regsys_titledeed`(`Id`),
  FOREIGN KEY (`RoleId`) REFERENCES `regsys_role`(`Id`)
);

CREATE TABLE `regsys_telegram` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Deliverytime` datetime,
  `Sender` varchar(255),
  `SenderCity` varchar(255),
  `Reciever` varchar(255),
  `RecieverCity` varchar(255),
  `Message` text(65535),
  `OrganizerNotes` text(65535),
  `LARPId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`LARPId`) REFERENCES `regsys_larp`(`Id`)
);

CREATE TABLE `regsys_officialtype_person` (
  `OfficialTypeId` int,
  `RegistrationId` int,
  PRIMARY KEY (`OfficialTypeId`, `RegistrationId`),
  FOREIGN KEY (`RegistrationId`) REFERENCES `regsys_registration`(`Id`),
  FOREIGN KEY (`OfficialTypeId`) REFERENCES `regsys_officialtype`(`Id`)
);

CREATE TABLE `regsys_NPCGroup` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` varchar(255),
  `Time` varchar(255),
  `LARPId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`LARPId`) REFERENCES `regsys_larp`(`Id`)
);

CREATE TABLE `regsys_NPC` (
  `Id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255),
  `Description` varchar(255),
  `Time` varchar(255),
  `PersonId` int,
  `NPCGroupId` int,
  `LARPId` int,
  `ImageId` int,
  PRIMARY KEY (`Id`),
  FOREIGN KEY (`PersonId`) REFERENCES `regsys_person`(`Id`),
  FOREIGN KEY (`LARPId`) REFERENCES `regsys_larp`(`Id`),
  FOREIGN KEY (`ImageId`) REFERENCES `regsys_image`(`Id`),
  FOREIGN KEY (`NPCGroupId`) REFERENCES `regsys_NPCGroup`(`Id`)
);

