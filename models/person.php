<?php

class Person extends BaseModel{
    
    public $Id;
    public $Name;
    public $SocialSecurityNumber;
    public $PhoneNumber;
    public $EmergencyContact;
    public $Email;
    public $FoodAllergiesOther;
    public $TypeOfLarperComment;
    public $OtherInformation;
    public $ExperienceId;
    public $TypeOfFoodId;
    public $LarperTypeId;
    public $UserId; # Användare som registrerat personen
    public $NotAcceptableIntrigues;
    public $HouseId;

    public static $orderListBy = 'Name';
    

    public static function newFromArray($post){

        $person = static::newWithDefault();
        $person->setValuesByArray($post);
        return $person;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['SocialSecurityNumber'])) {
            $ssn = $arr['SocialSecurityNumber'];
            if (strpos($ssn, "-") == false) {
                $ssn = substr($ssn, 0, 8) . "-" . substr($ssn, 8);
            }
            $this->SocialSecurityNumber = $ssn;
        }
        if (isset($arr['PhoneNumber'])) $this->PhoneNumber = $arr['PhoneNumber'];
        if (isset($arr['EmergencyContact'])) $this->EmergencyContact = $arr['EmergencyContact'];
        if (isset($arr['Email'])) $this->Email = $arr['Email'];
        if (isset($arr['FoodAllergiesOther'])) $this->FoodAllergiesOther = $arr['FoodAllergiesOther'];
        if (isset($arr['TypeOfLarperComment'])) $this->TypeOfLarperComment = $arr['TypeOfLarperComment'];
        if (isset($arr['IntrigueIdeas'])) $this->IntrigueIdeas = $arr['IntrigueIdeas'];
        if (isset($arr['OtherInformation'])) $this->OtherInformation = $arr['OtherInformation'];
        if (isset($arr['ExperienceId'])) $this->ExperienceId = $arr['ExperienceId'];
        if (isset($arr['TypeOfFoodId'])) $this->TypeOfFoodId = $arr['TypeOfFoodId'];
        if (isset($arr['LarperTypeId'])) $this->LarperTypeId = $arr['LarperTypeId'];
        if (isset($arr['UserId'])) $this->UserId = $arr['UserId'];
        if (isset($arr['NotAcceptableIntrigues'])) $this->NotAcceptableIntrigues = $arr['NotAcceptableIntrigues'];
        if (isset($arr['HouseId'])) $this->HouseId = $arr['HouseId'];
        
        if (isset($this->HouseId) && $this->HouseId=='null') $this->HouseId = null;
        
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        Global $current_user;
        
        $person = new self();
        $person->UserId = $current_user->Id;
        return $person;
    }
    
    public static function getPersonsForUser($userId) {
        global $tbl_prefix;
        $sql = "SELECT * FROM ".$tbl_prefix.strtolower(static::class)." WHERE UserId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($userId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }
    
    public static function getAllRegistered($larp) {
        global $tbl_prefix;
        if (is_null($larp)) return array();
        $sql = "SELECT * from `".$tbl_prefix."person` WHERE Id in (SELECT PersonId FROM `".$tbl_prefix."Registration` WHERE LarpId = ?)  ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
        
    }
    
    public static function getAllToApprove($larp) {
        global $tbl_prefix;
        if (is_null($larp)) return array();
        $sql = "SELECT * from `".$tbl_prefix."person` WHERE Id in (SELECT PersonId FROM `".$tbl_prefix."Registration` WHERE LarpId = ? AND Approved IS Null)  ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
        
    }
    
    
    # Hämta anmälda deltagare i en grupp
    public static function getPersonsInGroupAtLarp($group, $larp) {
        global $tbl_prefix;
        if (is_null($group) || is_null($larp)) return Array();
        
        $sql="select * from `".$tbl_prefix."person` WHERE id IN (SELECT ".$tbl_prefix."role.PersonId FROM `".$tbl_prefix."role`, ".$tbl_prefix."larp_role WHERE `".$tbl_prefix."role`.GroupId = ? AND `".$tbl_prefix."role`.Id=".$tbl_prefix."larp_role.RoleId AND ".$tbl_prefix."larp_role.LarpId=?) ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($group->Id, $larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }
    
    
    
    # Update an existing person in db
    public function update() {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("UPDATE ".$tbl_prefix.strtolower(static::class)." SET Name=?, SocialSecurityNumber=?, PhoneNumber=?, EmergencyContact=?, Email=?,
                                                                  FoodAllergiesOther=?, TypeOfLarperComment=?, OtherInformation=?, ExperienceId=?,
                                                                  TypeOfFoodId=?, LarperTypeId=?, UserId=?, NotAcceptableIntrigues=?, HouseId=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->SocialSecurityNumber, $this->PhoneNumber, $this->EmergencyContact, $this->Email,
            $this->FoodAllergiesOther, $this->TypeOfLarperComment, $this->OtherInformation, $this->ExperienceId,
            $this->TypeOfFoodId, $this->LarperTypeId, $this->UserId, $this->NotAcceptableIntrigues, $this->HouseId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
        }
        $stmt = null;  
        
    }
    
    # Create a new person in db
    public function create() {
        global $tbl_prefix;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO ".$tbl_prefix.strtolower(static::class)." (Name, SocialSecurityNumber, PhoneNumber, EmergencyContact, Email,
                                                                    FoodAllergiesOther, TypeOfLarperComment, OtherInformation, ExperienceId,
                                                                    TypeOfFoodId, LarperTypeId, UserId, NotAcceptableIntrigues, HouseId) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->SocialSecurityNumber, $this->PhoneNumber, $this->EmergencyContact, $this->Email, 
                $this->FoodAllergiesOther, $this->TypeOfLarperComment, $this->OtherInformation, $this->ExperienceId, 
                $this->TypeOfFoodId, $this->LarperTypeId, $this->UserId, $this->NotAcceptableIntrigues, $this->HouseId))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    # Spara den här relationen
    public function saveAllNormalAllergyTypes($post) {
        global $tbl_prefix;
        if (!isset($post['NormalAllergyTypeId'])) {
            return; 
        }
        foreach($post['NormalAllergyTypeId'] as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO ".$tbl_prefix."NormalAllergyType_Person (NormalAllergyTypeId, PersonId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;           
    }

    
    public function deleteAllNormalAllergyTypes() {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("DELETE FROM ".$tbl_prefix."NormalAllergyType_Person WHERE PersonId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function getExperience() {
        if (is_null($this->ExperienceId)) return null;
        return Experience::loadById($this->ExperienceId);
    }
    
    
    public function getTypeOfFood() {
        if (is_null($this->TypeOfFoodId)) return null;
        return TypeOfFood::loadById($this->TypeOfFoodId);
    }
    
    public function getLarperType() {
        if (is_null($this->LarperTypeId)) return null;
        return LarperType::loadById($this->LarperTypeId);
    }
    
    public function getUser() {
        return User::loadById($this->UserId);
    }
    
    public function getHouse() {
        return House::loadById($this->HouseId);
    }
    
    public function getRegistration(LARP $larp) {
        return Registration::loadByIds($this->Id, $larp->Id);
    }
    
    public function getNormalAllergyTypes() {
        global $tbl_prefix;
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT * FROM ".$tbl_prefix."NormalAllergyType_Person where PersonId = ? ORDER BY NormalAllergyTypeId;");
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = NormalAllergyType::loadById($row['NormalAllergyTypeId']);
        }
        $stmt = null;
        return $resultArray;
    }
    
    public function getSelectedNormalAllergyTypeIds() {
        global $tbl_prefix;
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT NormalAllergyTypeId FROM ".$tbl_prefix."NormalAllergyType_Person where PersonId = ? ORDER BY NormalAllergyTypeId;");
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = $row['NormalAllergyTypeId'];
        }
        $stmt = null;

        return $resultArray;
    }

    public function getRoles() {
        return Role::getRolesForPerson($this->Id);
    }
    
    public function getRolesAtLarp($larp) {
        return Role::getRegistredRolesForPerson($this, $larp);
    }
    
    
    public function getGroups() {
        return Group::getGroupsForPerson($this->Id);
    }
    
    public function getAgeAtLarp(LARP $larp) {
        $date = $larp->StartDate;
        $birthday = DateTime::createFromFormat('Ymd', substr($this->SocialSecurityNumber, 0, 7));
        $larpStartDate = DateTime::createFromFormat('Y-m-d', substr($date, 0, 9));
        $interval = date_diff($birthday, $larpStartDate);  
        return $interval->format('%Y');
    }
    
    public function isRegistered(LARP $larp) {
        $registration = Registration::loadByIds($this->Id, $larp->Id);
        if (isset($registration)) {
            return true;
        }        
        return false;
    }
    

    public function isApproved(LARP $larp) {
        $registration = Registration::loadByIds($this->Id, $larp->Id);
        if (!isset($registration)) {
            return false;
        }
        return $registration->isApproved();
    }
    
    public function hasPayed($larp) {
        $registration = Registration::loadByIds($this->Id, $larp->Id);
        if (!isset($registration)) {
            return false;
        }
        if ($registration->Payed == 1) {
            return true;
        }
        return false;
    }
    
    
    public function isMember($date) {
        $year = substr($date, 0, 4);
        
        $val = check_membership($this->SocialSecurityNumber, $year);
        if ($val == 1) return true;
        return false;
    }
    
    public static function getAllWithSingleAllergy(NormalAllergyType $allergy, LARP $larp) {
        global $tbl_prefix;
        if (is_null($allergy) OR is_null($larp)) return Array();

        $sql="select * from `".$tbl_prefix."person` WHERE id IN (Select ".$tbl_prefix."normalallergytype_person.PersonId FROM ".$tbl_prefix."normalallergytype_person, ".$tbl_prefix."Registration, (SELECT PersonId, count(NormalAllergyTypeId) AS amount FROM ".$tbl_prefix."normalallergytype_person GROUP BY PersonId) as Counted WHERE amount = 1 AND Counted.PersonId = ".$tbl_prefix."normalallergytype_person.PersonId and ".$tbl_prefix."normalallergytype_person.NormalAllergyTypeId=? AND ".$tbl_prefix."Registration.PersonId=".$tbl_prefix."normalallergytype_person.PersonId AND ".$tbl_prefix."Registration.LARPId=?) ORDER BY ".static::$orderListBy.";";

        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($allergy->Id, $larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
        
    }
    
    public static function getAllWithMultipleAllergies(LARP $larp) {
        global $tbl_prefix;

        if (is_null($larp)) return Array();
        $sql="select * from `".$tbl_prefix."person` WHERE id IN (Select ".$tbl_prefix."normalallergytype_person.PersonId FROM ".$tbl_prefix."normalallergytype_person, ".$tbl_prefix."Registration, (SELECT PersonId, count(NormalAllergyTypeId) AS amount FROM ".$tbl_prefix."normalallergytype_person GROUP BY PersonId) as Counted WHERE amount > 1 AND Counted.PersonId = ".$tbl_prefix."normalallergytype_person.PersonId AND ".$tbl_prefix."Registration.PersonId=".$tbl_prefix."normalallergytype_person.PersonId AND ".$tbl_prefix."Registration.LARPId=?) ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);

        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
        
    }
    
    public static function getAllWithoutAllergiesButWithComment(LARP $larp) {
        global $tbl_prefix;
        

        
        $sql="select * from ".$tbl_prefix."person WHERE id IN ".
            "(SELECT PersonId from ".$tbl_prefix."Registration WHERE LarpId =? AND PersonId NOT IN ".
            "(SELECT PersonId FROM ".$tbl_prefix."normalallergytype_person)) AND FoodAllergiesOther !='' ".
            "ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);

        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
        
    }
    
    
    
    public static function getAllOfficials(LARP $larp) {
        global $tbl_prefix;
        
        
        
        $sql="select * from ".$tbl_prefix."person WHERE id IN ".
            "(SELECT PersonId from ".$tbl_prefix."Registration WHERE IsOfficial=1 and LARPId=?) ".
            "ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
        
    }


    public static function getAllWhoWantToBeOffical(LARP $larp) {
        global $tbl_prefix;
        
        
        
        $sql="select * from ".$tbl_prefix."person WHERE id IN ".
            "(SELECT PersonId from ".$tbl_prefix."Registration WHERE IsOfficial=0 and LARPId=? AND Id IN (SELECT RegistrationId FROM ".$tbl_prefix."OfficialType_Person)) ".
            "ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
        
    }
}
