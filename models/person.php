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
    public $HouseId; #Förvaltare av huset

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
        if (isset($current_user)) {
            $person->UserId = $current_user->Id;
        }
        return $person;
    }
    
    public static function getHouseCaretakers(LARP $larp) {
        if (!isset($larp)) return Array();
        $sql = "SELECT * FROM regsys_person WHERE Id IN ".
            "(SELECT PersonId FROM regsys_registration WHERE LarpId=?) AND ".
            "HouseId Is NOT NULL ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function SSNAlreadyExists($ssn) {
        //Kollar om det redan finns en deltagare med det här personnumret
        if (!isset($ssn)) return false;
        
        $sql = "SELECT Id FROM regsys_person WHERE SocialSecurityNumber=?;";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($ssn))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return false;
            
        }
       
        $stmt = null;
        return true;
        
    }
    
    
    public static function getPersonsForUser($userId) {
        $sql = "SELECT * FROM regsys_person WHERE UserId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($userId));
    }
    
    public static function getAllRegistered($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT PersonId FROM ".
        "regsys_registration WHERE LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public static function getAllInterestedNPC($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id in (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ? AND NPCDesire <> '') ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public static function getAllToApprove($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id in (SELECT PersonId FROM ".
        "regsys_registration WHERE LarpId = ? AND Approved IS Null) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
 
    
    
    public static function findGuardian($guardianInfo, $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE (Name=? OR SocialSecurityNumber = ?) AND Id IN ".
            "(SELECT PersonId FROM regsys_registration WHERE LarpId = ?) ORDER BY ".static::$orderListBy.";";
        $persons = static::getSeveralObjectsqQuery($sql, array($guardianInfo, $guardianInfo, $larp->Id));
        foreach ($persons as $person) {
            if ($person->getAgeAtLarp($larp) >= 18) {
                $resultArray[] = $person;
            }
        }
        if (count($resultArray) == 0) return null;
        return $resultArray[0];
            
    }
    
    
    # Hämta anmälda deltagare i en grupp
    public static function getPersonsInGroupAtLarp($group, $larp) {
        if (is_null($group) || is_null($larp)) return Array();
        
        $sql="select * from regsys_person WHERE id IN ".
            "(SELECT DISTINCT regsys_role.PersonId ".
            "FROM regsys_role, regsys_larp_role WHERE ".
            "regsys_role.GroupId = ? AND ".
            "regsys_role.Id=regsys_larp_role.RoleId AND ".
            "regsys_larp_role.LarpId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
    }
    
    
    
    # Update an existing person in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_person SET Name=?, SocialSecurityNumber=?, PhoneNumber=?, EmergencyContact=?, Email=?,
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
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_person (Name, SocialSecurityNumber, PhoneNumber, EmergencyContact, Email,
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
        if (!isset($post['NormalAllergyTypeId'])) {
            return; 
        }
        foreach($post['NormalAllergyTypeId'] as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_normalallergytype_person (NormalAllergyTypeId, PersonId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;           
    }

    
    public function deleteAllNormalAllergyTypes() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_normalallergytype_person WHERE PersonId = ?;");
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
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT * FROM ".
            "regsys_normalallergytype_person WHERE PersonId = ? ORDER BY NormalAllergyTypeId;");
        
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
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT NormalAllergyTypeId FROM ".
            "regsys_normalallergytype_person WHERE PersonId = ? ORDER BY NormalAllergyTypeId;");
        
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
    
    public function getAliveRoles() {
        return Role::getAliveRolesForPerson($this->Id);
    }
    
    public function getRolesAtLarp($larp) {
        return Role::getRegistredRolesForPerson($this, $larp);
    }

    public function getMainRole($larp) {
        return Role::getMainRoleForPerson($this, $larp);
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
    
    public function isNeverRegistered() {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE PersonId=?;";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return true;
            
        }
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = null;
        
        
        if ($res[0]['Num'] == 0) return true;
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
        if ($registration->AmountToPay <= $registration->AmountPayed) {
            return true;
        }
        return false;
    }
    
    
    public function isMember(Larp $larp) {
        $registration = Registration::loadByIds($this->Id, $larp->Id);

        //Vi bryr oss inte om ifall personer är medlemmar om det inte är anmälda till ett lajv
        if (!isset($registration)) return false;
        

        //Vi har fått svar på att man har betalat medlemsavgift för året. Behöver inte kolla fler gånger.
        if ($registration->IsMember == 1) return true;
        
        //Kolla inte oftare än en gång per kvart
        if (isset($registration->MembershipCheckedAt) && (time()-strtotime($registration->MembershipCheckedAt) < 15*60)) return false;


        $year = substr($larp->StartDate, 0, 4);

        $val = check_membership($this->SocialSecurityNumber, $year);
        

        if ($val == 1) {
            $registration->IsMember=1;
        }
        else {
            $registration->IsMember = 0;
        }
        $now = new Datetime();
        $registration->MembershipCheckedAt = date_format($now,"Y-m-d H:i:s");
        $registration->update();

        if ($registration->IsMember == 1) return true;
        return false;
    }
    
    public static function getAllWithSingleAllergy(NormalAllergyType $allergy, LARP $larp) {
        if (is_null($allergy) OR is_null($larp)) return Array();

        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT regsys_normalallergytype_person.PersonId FROM ".
            "regsys_normalallergytype_person, regsys_registration, ".
            "(SELECT PersonId, count(NormalAllergyTypeId) AS amount FROM ".
            "regsys_normalallergytype_person GROUP BY PersonId) as Counted WHERE amount = 1 AND Counted.PersonId = ".
            "regsys_normalallergytype_person.PersonId AND ".
            "regsys_normalallergytype_person.NormalAllergyTypeId=? AND ".
            "regsys_registration.PersonId=regsys_normalallergytype_person.PersonId AND ".
            "regsys_registration.LARPId=?) ORDER BY ".static::$orderListBy.";";
        
        return static::getSeveralObjectsqQuery($sql, array($allergy->Id, $larp->Id));
    }
    
    public static function getAllWithMultipleAllergies(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT regsys_normalallergytype_person.PersonId FROM ".
            "regsys_normalallergytype_person, regsys_registration, ".
            "(SELECT PersonId, count(NormalAllergyTypeId) AS amount FROM ".
            "regsys_normalallergytype_person GROUP BY PersonId) AS Counted WHERE amount > 1 AND ".
            "Counted.PersonId = regsys_normalallergytype_person.PersonId AND ".
            "regsys_registration.PersonId = regsys_normalallergytype_person.PersonId AND ".
            "regsys_registration.LARPId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllWithoutAllergiesButWithComment(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT PersonId from regsys_registration WHERE LarpId =? AND PersonId NOT IN ".
            "(SELECT PersonId FROM regsys_normalallergytype_person)) AND FoodAllergiesOther !='' ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
     }
    
    
    
    public static function getAllOfficials(LARP $larp) { 
        if (is_null($larp)) return Array();
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT PersonId FROM regsys_registration WHERE IsOfficial=1 and LARPId=?) ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }


    public static function getAllOfficialsByType(OfficialType $officialtype, LARP $larp) {
        if (is_null($larp) or is_null($officialtype)) return Array();
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT PersonId from regsys_registration, regsys_officialtype_person ".
            "WHERE IsOfficial=1 and LARPId=? AND regsys_registration.Id = regsys_officialtype_person.RegistrationId ".
            "AND OfficialTypeId = ?) ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $officialtype->Id));
    }
    
    
    public static function getAllWhoWantToBeOffical(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT PersonId FROM regsys_registration WHERE IsOfficial=0 and LARPId=? AND Id IN ".
            "(SELECT RegistrationId FROM regsys_officialtype_person)) ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
}
