<?php

class Person extends BaseModel{
    
    public $Id;
    public $Name;
    public $SocialSecurityNumber;
    public $PhoneNumber;
    public $EmergencyContact;
    public $Email;
    public $FoodAllergiesOther;
    public $OtherInformation;
    public $ExperienceId;
    public $UserId; # Användare som registrerat personen
    public $NotAcceptableIntrigues;
    public $HouseId; #Förvaltare av huset
    public $HousingComment;
    public $HealthComment;
    public $HasPermissionShowName = 1;
    public $WantIntriguesInPlainText = 0;
    public $IsSubscribed = 1;
    public $UnsubscribeCode;
    public $MembershipCheckedAt;
    public $IsMember;
    public $AdvertismentsCheckedAt;
    public $MailCheckedAt;
    public $LastMailSentAt;
    

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
        if (isset($arr['IntrigueIdeas'])) $this->IntrigueIdeas = $arr['IntrigueIdeas'];
        if (isset($arr['OtherInformation'])) $this->OtherInformation = $arr['OtherInformation'];
        if (isset($arr['ExperienceId'])) $this->ExperienceId = $arr['ExperienceId'];
        if (isset($arr['UserId'])) $this->UserId = $arr['UserId'];
        if (isset($arr['NotAcceptableIntrigues'])) $this->NotAcceptableIntrigues = $arr['NotAcceptableIntrigues'];
        if (isset($arr['HouseId'])) $this->HouseId = $arr['HouseId'];
        if (isset($arr['HousingComment'])) $this->HousingComment = $arr['HousingComment'];
        if (isset($arr['HealthComment'])) $this->HealthComment = $arr['HealthComment'];
        if (isset($arr['HasPermissionShowName'])) $this->HasPermissionShowName = $arr['HasPermissionShowName'];
        if (isset($arr['WantIntriguesInPlainText'])) $this->WantIntriguesInPlainText = $arr['WantIntriguesInPlainText'];
        if (isset($arr['IsSubscribed'])) $this->IsSubscribed = $arr['IsSubscribed'];
        if (isset($arr['UnsubscribeCode'])) $this->UnsubscribeCode = $arr['UnsubscribeCode'];
        if (isset($arr['MembershipCheckedAt'])) $this->MembershipCheckedAt = $arr['MembershipCheckedAt'];
        if (isset($arr['IsMember'])) $this->IsMember = $arr['IsMember'];
        if (isset($arr['AdvertismentsCheckedAt'])) $this->AdvertismentsCheckedAt = $arr['AdvertismentsCheckedAt'];
        if (isset($arr['MailCheckedAt'])) $this->MailCheckedAt = $arr['MailCheckedAt'];
        if (isset($arr['LastMailSentAt'])) $this->LastMailSentAt = $arr['LastMailSentAt'];
        
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
    
    public static function getPersonsWhoIsHouseCaretakers(LARP $larp) {
        if (!isset($larp)) return Array();
        $sql = "SELECT * FROM regsys_person WHERE Id IN ".
            "(SELECT regsys_registration.PersonId FROM regsys_registration, regsys_housecaretaker WHERE 
                regsys_registration.LarpId=? AND 
                regsys_registration.NotComing = 0 AND
                regsys_registration.PersonId = regsys_housecaretaker.PersonId) ".
            " ORDER BY ".static::$orderListBy.";";
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
    
    
    public static function personsAssignedToHouse(House $house, LARP $larp) {
        $sql = "SELECT * FROM regsys_person WHERE Id IN ".
        "(SELECT PersonId FROM regsys_housing WHERE HouseId=? AND LarpId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($house->Id, $larp->Id)); 
    }
    
    public static function getGroupMembersInHouse(Group $group, House $house, LARP $larp) {
        $sql = "SELECT * FROM regsys_person WHERE Id IN ".
            "(SELECT regsys_role.PersonId FROM regsys_housing, regsys_role, regsys_larp_role WHERE ".
            "regsys_housing.HouseId = ? AND ".
            "regsys_housing.LarpId = ? AND ".
            "regsys_role.PersonId = regsys_housing.PersonId AND ".
            "regsys_role.Id = regsys_larp_role.RoleId AND ".
            "regsys_larp_role.LarpId = regsys_housing.LarpId AND ".
            "regsys_larp_role.IsMainRole=1 AND ".
            "regsys_role.GroupId = ?".
            ") ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($house->Id, $larp->Id, $group->Id));
    }
 
    
    public static function getGroupMembers(Group $group, LARP $larp) {
        $sql = "SELECT * FROM regsys_person WHERE Id IN ".
            "(SELECT regsys_role.PersonId FROM regsys_role, regsys_larp_role WHERE ".
            "regsys_role.Id = regsys_larp_role.RoleId AND ".
            "regsys_larp_role.LarpId = ? AND ".
            "regsys_role.GroupId = ?".
            ") ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $group->Id));
    }

    
    public static function getPersonsForUser($userId) {
        $sql = "SELECT * FROM regsys_person WHERE UserId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($userId));
    }
    
    public static function getAllRegistered($larp, $evenNotComing) {
        if (is_null($larp)) return array();
        $NotComingStr = "AND NotComing = 0";
        if ($evenNotComing) $NotComingStr = "";
        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT PersonId FROM ".
        "regsys_registration WHERE LarpId = ? $NotComingStr) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllWithGuardians($larp) {
        if (is_null($larp)) return array();

        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ? AND GuardianId IS NOT NULL) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }

    public static function getAllGuardians($larp) {
        if (is_null($larp)) return array();
        
        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT GuardianId FROM ".
            "regsys_registration WHERE LarpId = ? AND GuardianId IS NOT NULL AND NotComing = 0) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function countNotComingGuardians($larp) {
        if (is_null($larp)) return array();
        
        $sql = "SELECT count(regsys_person.Id) as Num from regsys_person, regsys_registration WHERE ".
            "regsys_person.Id = regsys_registration.PersonId AND ".
            "regsys_registration.LarpId = ? AND ".
            "regsys_registration.NotComing = 1 AND ".
            "regsys_person.Id IN ".
            "(SELECT GuardianId FROM regsys_registration WHERE ".
            "LarpId = ? AND GuardianId IS NOT NULL AND NotComing = 0) ".
            "ORDER BY ".static::$orderListBy.";";
        return static::countQuery($sql, array($larp->Id, $larp->Id));
    }
    
    
    
    public static function getAllRegisteredPartTime($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ? AND LarpPartNotAttending IS NOT NULL) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllRegisteredNoFee($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ? AND AmountToPay IS NULL) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllReserves($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT PersonId FROM ".
            "regsys_reserve_registration WHERE LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public static function getAllToBeRefunded($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ? AND IsToBeRefunded=1 AND RefundDate IS NULL) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllRefunded($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ? AND RefundDate IS NOT NULL) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    
    public static function getAllRegisteredWithoutHousing($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id IN (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ?  AND NotComing = 0 AND PersonId NOT IN ".
            "(SELECT PersonId from regsys_housing WHERE LarpId=?)) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $larp->Id));
    }
    
    
    # Hämta anmälda personer som har en huvudkaraktär i en grupp
    public static function getPersonsInGroup($group, $larp) {
        if (is_null($group) || is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_person WHERE Id IN ".
            "(SELECT regsys_registration.PersonId FROM regsys_registration, regsys_group, regsys_role, regsys_larp_role WHERE ".
            "regsys_registration.PersonId = regsys_role.PersonId AND ".
            "regsys_registration.LarpId = regsys_larp_role.LarpId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_role.GroupId = ? AND ".
            "regsys_role.Id=regsys_larp_role.RoleId AND ".
            "regsys_larp_role.IsMainRole=1 AND ".
            "regsys_larp_role.LarpId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
    }
    
    # Hämta anmälda personer som har en huvudkaraktär i en grupp
    public static function getPersonsInGroupWithoutHousing($group, $larp) {
        $all_group_members = static::getPersonsInGroup($group, $larp);
        $group_members = array();
        foreach ($all_group_members as $group_member) {
            if (!$group_member->hasHousing($larp)) {
                $group_members[] = $group_member;
            }
        }
        return $group_members;
    }
    
    # En function som letar bland alla personer och returnerar en array av personer om något hittas
    public static function searchPersons($search) {
        $sql = "SELECT * from regsys_person WHERE `name` LIKE ? OR `SocialSecurityNumber` regexp ? OR `SocialSecurityNumber` regexp ? ORDER BY ".static::$orderListBy.";";
        $persons = static::getSeveralObjectsqQuery($sql, array("%$search%", "^$search", "^19$search"));
        return $persons;
    }
    
    
    public function getGuardianFor(LARP $larp) {
        $sql = "SELECT * from regsys_person WHERE Id in (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ? AND GuardianId = ? AND NotComing = 0) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $this->Id));
    }
    
    public function getGuardian(LARP $larp) {
        $sql = "SELECT * from regsys_person WHERE Id in (SELECT GuardianId FROM ".
            "regsys_registration WHERE LarpId = ? AND PersonId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getOneObjectQuery($sql, array($larp->Id, $this->Id));
    }
    
    
    
    public static function getAllInterestedNPC($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE Id in (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ? AND NPCDesire <> '' AND NotComing = 0) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public static function findGuardian($guardianInfo, $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_person WHERE (Name=? OR SocialSecurityNumber = ?) AND Id IN ".
            "(SELECT PersonId FROM regsys_registration WHERE LarpId = ? AND NotComing = 0) ORDER BY ".static::$orderListBy.";";
        $persons = static::getSeveralObjectsqQuery($sql, array($guardianInfo, $guardianInfo, $larp->Id));
        foreach ($persons as $person) {
            if ($person->getAgeAtLarp($larp) >= 18) {
                $resultArray[] = $person;
            }
        }
        if (empty($resultArray) || count($resultArray) == 0) return null;
        return $resultArray[0];
            
    }
    
    
    # Hämta anmälda deltagare i en grupp
    public static function getPersonsInGroupAtLarp($group, $larp) {
        if (is_null($group) || is_null($larp)) return Array();

        $sql="select * from regsys_person WHERE id IN ".
            "(SELECT DISTINCT regsys_role.PersonId ".
            "FROM regsys_role, regsys_larp_role, regsys_registration WHERE ".
            "regsys_role.GroupId = ? AND ".
            "regsys_role.Id=regsys_larp_role.RoleId AND ".
            "regsys_larp_role.LarpId = regsys_registration.LarpId AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_larp_role.LarpId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
    }
    
    
    
    # Update an existing person in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_person SET Name=?, SocialSecurityNumber=?, PhoneNumber=?, 
            EmergencyContact=?, Email=?,
            FoodAllergiesOther=?, OtherInformation=?, ExperienceId=?,
            UserId=?, NotAcceptableIntrigues=?, HouseId=?, HousingComment=?, HealthComment=?, 
            HasPermissionShowName=?, WantIntriguesInPlainText=?, IsSubscribed=?, UnsubscribeCode=?,
            MembershipCheckedAt=?, IsMember=?, AdvertismentsCheckedAt=?, MailCheckedAt=?, LastMailSentAt=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->SocialSecurityNumber, $this->PhoneNumber, 
            $this->EmergencyContact, $this->Email,
            $this->FoodAllergiesOther, $this->OtherInformation, $this->ExperienceId,
            $this->UserId, $this->NotAcceptableIntrigues, $this->HouseId, $this->HousingComment, $this->HealthComment, 
            $this->HasPermissionShowName, $this->WantIntriguesInPlainText, $this->IsSubscribed, $this->UnsubscribeCode, 
            $this->MembershipCheckedAt, $this->IsMember, $this->AdvertismentsCheckedAt, 
            $this->MailCheckedAt, $this->LastMailSentAt, $this->Id))) {
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
                FoodAllergiesOther, OtherInformation, ExperienceId,
                UserId, NotAcceptableIntrigues, HouseId, HousingComment, HealthComment, 
                HasPermissionShowName, WantIntriguesInPlainText, IsSubscribed, UnsubscribeCode,
                MembershipCheckedAt, IsMember, AdvertismentsCheckedAt, MailCheckedAt, LastMailSentAt) 
            VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->SocialSecurityNumber, $this->PhoneNumber, $this->EmergencyContact, $this->Email, 
                $this->FoodAllergiesOther, $this->OtherInformation, $this->ExperienceId, 
                $this->UserId, $this->NotAcceptableIntrigues, $this->HouseId, $this->HousingComment, $this->HealthComment, 
                $this->HasPermissionShowName, $this->WantIntriguesInPlainText, $this->IsSubscribed, $this->UnsubscribeCode,
            $this->MembershipCheckedAt, $this->IsMember, $this->AdvertismentsCheckedAt, $this->MailCheckedAt, $this->LastMailSentAt
        ))) {
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
    
    
    public function getUser() {
        return User::loadById($this->UserId);
    }

    public function getRegistration(LARP $larp) {
        return Registration::loadByIds($this->Id, $larp->Id);
    }
    
    public function getReserveRegistration(LARP $larp) {
        return Reserve_Registration::loadByIds($this->Id, $larp->Id);
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

    public function getAllRoles() {
        return Role::getAllRolesForPerson($this->Id);
    }
    
    public function getRoles(Larp $larp) {
        return Role::getRolesForPerson($this->Id, $larp->CampaignId);
    }
    
    public function getAliveRoles(LARP $larp) {
        return Role::getAliveRolesForPerson($this->Id, $larp->CampaignId);
    }
    
    public function getRolesAtLarp($larp) {
        return Role::getRegistredRolesForPerson($this, $larp);
    }

    public function getUnregisteredRolesAtLarp($larp) {
        return Role::getUnregistredRolesForPerson($this, $larp);
    }
    
    public function getNPCAssignmentsAtLarp($larp) {
        return NPC_assignment::getAssignmentsForPerson($this, $larp);
    }
    
    public function getReleasedNPCAssignmentsAtLarp($larp) {
        return NPC_assignment::getReleasedAssignmentForPerson($this, $larp);
    }
    
    
    
    public function getUnregisteredAliveGroups(LARP $larp) {
        $unregistered_groups = Array();
        $groups = Group::getGroupsForPerson($this->Id, $larp->CampaignId);
        foreach ($groups as $group) {
            if (!$group->isRegistered($larp) && $group->IsDead==0) {
                array_push($unregistered_groups,$group);
            }
        }
        return $unregistered_groups;
    }
    
    
    

    public function getReserveRolesAtLarp($larp) {
        return Role::getReserveRegistredRolesForPerson($this, $larp);
    }
    
    public function getMainRole($larp) {
        return Role::getMainRoleForPerson($this, $larp);
    }
    
    
    public function getAllGroups() {
        return Group::getAllGroupsForPerson($this->Id);
    }
    
    public function getGroups(Larp $larp) {
        return Group::getGroupsForPerson($this->Id, $larp->CampaignId);
    }
    
    public function getAllRegisteredGroups(Larp $larp) {
        return Group::getAllRegisteredGroupsForPerson($this->Id, $larp);
    }
    
    public function getAgeAtLarp(LARP $larp) {
        return getAge(substr($this->SocialSecurityNumber, 0, 8), $larp->StartDate);
    }
    
    public function getAgeNow() {
        return getAge(substr($this->SocialSecurityNumber, 0, 8), date("Ymd"));
    }
    
    public function isRegistered(LARP $larp) {
        $registration = Registration::loadByIds($this->Id, $larp->Id);
        if (isset($registration)) {
            return true;
        }        
        return false;
    }

    public function isReserve(LARP $larp) {
        $reserve_registration = Reserve_Registration::loadByIds($this->Id, $larp->Id);
        if (isset($reserve_registration)) {
            return true;
        }
        return false;
    }
    
    public function isNotComing(LARP $larp) {
        $registration = Registration::loadByIds($this->Id, $larp->Id);
        if (isset($registration)) {
            return $registration->isNotComing();
        }
        return false;
    }
    
    public function hasPermissionShowName() {
        if ($this->HasPermissionShowName == 1) return true;
        return false;
    }
    
    public function wantIntriguesInPlainText() {
        if ($this->WantIntriguesInPlainText == 1) return true;
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
    

    public function isApprovedCharacters(LARP $larp) {
        $roles = Role::getRegistredRolesForPerson($this, $larp);
        foreach ($roles as $role) {
            if (!$role->isApproved()) return false;
        }
        return true;
    }
    
    public function hasPayed($larp) {
        $registration = Registration::loadByIds($this->Id, $larp->Id);
        if (!isset($registration)) {
            return false;
        }
        return $registration->hasPayed();
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
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.LARPId=?) ORDER BY ".static::$orderListBy.";";
        
        return static::getSeveralObjectsqQuery($sql, array($allergy->Id, $larp->Id));
    }
    
    public static function getAllWithSingleAllergyWithoutComment(NormalAllergyType $allergy, LARP $larp) {
        if (is_null($allergy) OR is_null($larp)) return Array();
        
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT regsys_normalallergytype_person.PersonId FROM ".
            "regsys_normalallergytype_person, regsys_registration, ".
            "(SELECT PersonId, count(NormalAllergyTypeId) AS amount FROM ".
            "regsys_normalallergytype_person GROUP BY PersonId) as Counted WHERE amount = 1 AND Counted.PersonId = ".
            "regsys_normalallergytype_person.PersonId AND ".
            "regsys_normalallergytype_person.NormalAllergyTypeId=? AND ".
            "regsys_registration.PersonId=regsys_normalallergytype_person.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.LARPId=?) AND FoodAllergiesOther = '' ORDER BY ".static::$orderListBy.";";
        
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
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.LARPId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllWithMultipleAllergiesWithoutComment(LARP $larp) {
        if (is_null($larp)) return Array();
        
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT regsys_normalallergytype_person.PersonId FROM ".
            "regsys_normalallergytype_person, regsys_registration, ".
            "(SELECT PersonId, count(NormalAllergyTypeId) AS amount FROM ".
            "regsys_normalallergytype_person GROUP BY PersonId) AS Counted WHERE amount > 1 AND ".
            "Counted.PersonId = regsys_normalallergytype_person.PersonId AND ".
            "regsys_registration.PersonId = regsys_normalallergytype_person.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.LARPId=?) AND FoodAllergiesOther = '' ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllWithoutAllergiesButWithComment(LARP $larp) {
        if (is_null($larp)) return Array();

        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT PersonId from regsys_registration WHERE LarpId =? AND NotComing = 0 AND PersonId NOT IN ".
            "(SELECT PersonId FROM regsys_normalallergytype_person)) AND FoodAllergiesOther !='' ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
     }
    
     public static function getAllWithAllergyComment(LARP $larp) {
         if (is_null($larp)) return Array();
         
         $sql="SELECT * FROM regsys_person WHERE id IN ".
             "(SELECT PersonId from regsys_registration WHERE LarpId =? AND NotComing = 0) AND FoodAllergiesOther !='' ".
             "ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($larp->Id));
     }
     
    
    public static function getAllOfficials(LARP $larp) { 
        if (is_null($larp)) return Array();
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT PersonId FROM regsys_registration WHERE IsOfficial=1 and LARPId=? AND NotComing = 0) ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }


    public static function getAllOfficialsByType(OfficialType $officialtype, LARP $larp) {
        if (is_null($larp) or is_null($officialtype)) return Array();
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT PersonId from regsys_registration, regsys_officialtype_person ".
            "WHERE IsOfficial=1 and LARPId=? AND NotComing = 0 AND regsys_registration.Id = regsys_officialtype_person.RegistrationId ".
            "AND OfficialTypeId = ?) ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $officialtype->Id));
    }
    
    public static function getAllWhoWantToBeOfficialsByType(OfficialType $officialtype, LARP $larp) {
        if (is_null($larp) or is_null($officialtype)) return Array();
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT PersonId from regsys_registration, regsys_officialtype_person ".
            "WHERE IsOfficial=0 and LARPId=? AND NotComing = 0 AND regsys_registration.Id = regsys_officialtype_person.RegistrationId ".
            "AND OfficialTypeId = ?) ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $officialtype->Id));
    }
    
    
    public static function getAllWhoWantToBeOffical(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql="SELECT * FROM regsys_person WHERE id IN ".
            "(SELECT PersonId FROM regsys_registration WHERE IsOfficial=0 and LARPId=? AND NotComing = 0 AND Id IN ".
            "(SELECT RegistrationId FROM regsys_officialtype_person)) ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public function hasHousing(LARP $larp) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_housing WHERE LARPId=? AND PersonId=?";
        return static::existsQuery($sql, array($larp->Id, $this->Id));
    }
    
    public function housesOf() {
        return House::housesOf($this);
    }
    
    public function getFullHousingComment(LARP $larp) {
        $comments = array();
        if (!empty($this->HousingComment)) $comments[] = $this->HousingComment;
        $registration = $this->getRegistration($larp);
        if (!empty($registration->LarpHousingComment)) $comments[] = $registration->LarpHousingComment;
        return implode(' ', $comments);
    }
    
    public function getHouseAtLarp(Larp $larp) {
        return House::getHouseAtLarp($this, $larp);
    }
    
    public function isSubscribed() {
        return $this->IsSubscribed == 1;
    }
    
    public function getUnsubscribeCode() {
        if (empty($this->UnsubscribeCode)) {
            $this->UnsubscribeCode = bin2hex(random_bytes(20));
            $this->update();
        }
        return $this->UnsubscribeCode;
    }
    
    public static function getAllWithAccessToLarp(LARP $larp) {
        $campaingPersons = Person::getAllWithAccessToCampaign($larp->getCampaign());
        $onlyLarp = Person::getAllWithAccessOnlyToLarp($larp);
        return array_merge($campaingPersons, $onlyLarp);
    }
    
    public static function getAllWithAccessToCampaign(Campaign $campaign) {
        if (is_null($campaign)) return null;
        
        $sql = "SELECT * FROM regsys_person WHERE Id IN (SELECT PersonId from regsys_access_control_campaign WHERE CampaignId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($campaign->Id));
    }
    
    public static function getAllWithAccessOnlyToLarp(LARP $larp) {
        if (is_null($larp)) return null;
        
        $sql = "SELECT * FROM regsys_person WHERE Id IN (SELECT PersonId from regsys_access_control_larp WHERE LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public static function getAllWithOtherAccess() {
        $sql = "SELECT * FROM regsys_person WHERE Id IN (SELECT PersonId FROM regsys_access_control_other WHERE 1) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array());
    }
    
    
    public function getOtherAccess() {
        $sql = "SELECT Permission FROM regsys_access_control_other WHERE PersonId = ? ORDER BY Permission;";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return false;   
        }
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        
        $resPermissions = array();
        foreach ($res as $item)  $resPermissions[] = $item['Permission'];
        return $resPermissions;

        if ($res[0]['Num'] == 0) return false;
        return true;
    }
    
    
    public function isMemberAtLarp(Larp $larp) {
        $year = substr($larp->StartDate, 0, 4);
        
        $current_year = date("Y");
        if ($year > $current_year) return false;
        
        return $this->IsMember();
    }
    
    
    public function isMember() {
        //Kolla så att vi inte har bytt år.
        $last_checked_year = substr($this->MembershipCheckedAt, 0, 4);
        $current_year = date("Y");
        
        if (($current_year > $last_checked_year) && $this->IsMember == 1) {
            $this->IsMember = 0;
        }
        //Vi har fått svar på att man har betalat medlemsavgift för året. Behöver inte kolla fler gånger.
        if ($this->IsMember == 1) return true;
        
        //Kolla inte oftare än en gång per kvart
        if (isset($this->MembershipCheckedAt) && (time()-strtotime($this->MembershipCheckedAt) < 15*60)) return false;
        
        $val = check_membership($this->SocialSecurityNumber, $current_year);
        
        
        if ($val == 1) {
            $this->IsMember=1;
        }
        else {
            $this->IsMember = 0;
        }
        $now = new Datetime();
        $this->MembershipCheckedAt = date_format($now,"Y-m-d H:i:s");
        $this->update();
        if ($this->IsMember == 1) return true;
        return false;
    }
    
    public static function allParticipants($larpIds) {
        $sql = "SELECT * FROM regsys_person WHERE ";
        $larpsql = array();
        foreach($larpIds as $larpId) {
            $larpsql[] = " Id IN (SELECT PersonId FROM regsys_registration WHERE LarpId = ? AND NotComing=0) ";
        }
        $sql .= implode(" AND ", $larpsql);
        $sql .= " ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, $larpIds);
    }
    
    
    public function isMemberSubdivision($subdivision) {
        //Kollar om personen har en karaktär som är med i grupperingen
        if (!isset($subdivision)) return false;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_role, regsys_subdivisionmember WHERE ".
            "regsys_subdivisionmember.SubdivisionId=? AND ".
            "regsys_role.Id=regsys_subdivisionmember.RoleId AND ".
            "regsys_role.PersonId = ?;";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($subdivision->Id, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return false;
            
        }
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = null;
        
        
        if ($res[0]['Num'] == 0) return false;
        return true;
    }
    
    
    public function hasEditRightToHouse(House $house) {
        if (AccessControl::hasAccessOther($this, AccessControl::HOUSES)) return true;
        
        if ($house->getHousecaretakerForPerson($this)) return true;

        if (AccessControl::hasAccessOther($this, AccessControl::ADMIN)) return true;
        return false;
    }
    
    public function isMemberGroup($group) {
        //Kollar om personen har en karaktär som är med i gruppen
        if (!isset($group)) return false;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_role WHERE ".
            "regsys_role.GroupId=? AND ".
            "regsys_role.PersonId = ?;";
        return static::existsQuery($sql, array($group->Id, $this->Id));
    }

    
    public function hasNPCInGroup(Group $group, Larp $larp) {
        //Kollar om personen har en karaktär som är med i gruppen
        if (!isset($group)) return false;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_role, regsys_npc_assignment WHERE ".
            "regsys_role.GroupId=? AND ".
            "regsys_npc_assignment.RoleId = regsys_role.Id AND ".
            "regsys_npc_assignment.LarpId = ? AND ".
            "regsys_npc_assignment.PersonId = ?;";
        return static::existsQuery($sql, array($group->Id, $larp->Id, $this->Id));
    }
    
    public function isGroupLeader($group) {
        if ($group->PersonId == $this->Id) return true;
        return false;
    }
    
    public function isComing(Larp $larp) {
        if (is_null($larp)) return null;
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE ".
            "regsys_registration.PersonId = ? AND ".
            "regsys_registration.SpotAtLARP = 1 AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.LarpId=?;";
        return static::existsQuery($sql, array($this->Id, $larp->Id));
        
    }
    
    public function getAdvertismentsAtLarp($larp) {
        return Advertisment::allBySelectedPersonIdAndLARP($this->Id, $larp);
    }
    
    public function getTelegramsAtLarp($larp) {
        return Telegram::allBySelectedPersonIdAndLARP($this->Id, $larp);
    }
    
    public function getLettersAtLarp($larp) {
        return Letter::allBySelectedPersonIdAndLARP($this->Id, $larp);
    }
    
    public function getRumoursAtLarp($larp) {
        return Rumour::allBySelectedPersonIdAndLARP($this->Id, $larp);
    }
    
    public static function allManualMemberships() {
        $current_year = date("Y");
        $sql = "SELECT * FROM regsys_person WHERE MembershipCheckedAt = '".$current_year."-01-01 00:00:00' ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array());
    }
    
    public function setManualMembership() {
        if ($this->isMember()) return;
        $current_year = date("Y");
        $this->IsMember = true;
        $this->MembershipCheckedAt = $current_year."-01-01 00:00:00";
        $this->update();
    }
 
    public function removeManualMembership() {
        $current_year = date("Y");
        if ($this->MembershipCheckedAt == $current_year."-01-01 00:00:00") {
            $this->IsMember = false;
            $this->MembershipCheckedAt = NULL;
            $this->update();
        }
    }
    
    public function getViewLink($print_age = true) {
        Global $current_larp, $current_person;

        if (!isset($current_larp)) return "<a href='view_person.php?id={$this->Id}'>{$this->Name}</a>";
         
        $print_age = (bool)$print_age;

        $isRegistered = true;
        $registration = Registration::loadByIds($this->Id, $current_larp->Id);
        if (isset($registration)) {
            $title =  $registration->isNotComing() ? 'Avbokad' : 'Kommer'; 
        } 
        else {
            $reserveregistration = Reserve_Registration::loadByIds($this->Id, $current_larp->Id);
            if (isset($reserveregistration)) $title = "På reservlistan";
            else {
                $isRegistered = false;
                $title = "Inte anmäld till lajvet";
            }
        }

        if ($isRegistered) $vperson = "<a href='view_person.php?id={$this->Id}' title='$title'>{$this->Name}</a>";
        else $vperson = $this->Name;
        
        if ($print_age) $vperson .= " (".$this->getAgeAtLarp($current_larp)." år)"; 
        
        # Visa namnet genomstruket om personen i fråga inte kommer på aktuellt lajv
        if ($title != 'Kommer') {
            if ($_SESSION['navigation'] == Navigation::PARTICIPANT) {
                return "$vperson  ".showStatusIcon(false, NULL, NULL, $title);
            }
            return "<s>$vperson</s>  ".showStatusIcon(false, NULL, NULL, $title);
        }
        
        return $vperson;
    }
    
    public function changeUser(User $user) {
        $this->UserId = $user->Id;
        $this->update();
        BerghemMailer::send_user_changed($this, $user);
    }
    
}
