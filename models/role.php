<?php

class Role extends BaseModel{
    
    public $Id;
    public $Name;
    public $Profession;
    public $Description;
    public $DescriptionForGroup;
    public $DescriptionForOthers;
    public $PreviousLarps;
    public $ReasonForBeingInSlowRiver;
    public $ReligionId;
    public $Religion;
    public $BeliefId;
    public $DarkSecret;
    public $DarkSecretIntrigueIdeas;
    public $IntrigueSuggestions;
    public $NotAcceptableIntrigues;
    public $OtherInformation; 
    public $PersonId;
    public $GroupId;
    public $WealthId;
    public $PlaceOfResidenceId;
    public $RaceId;
    public $RoleFunctionComment;
    public $Photo;
    public $Birthplace;
    public $CharactersWithRelations;
    public $CampaignId;
    public $ImageId;
    public $IsDead = 0;
    public $OrganizerNotes;
    public $NoIntrigue = 0; //"Myslajvare"
    public $LarperTypeId;
    public $TypeOfLarperComment;
    public $RaceComment;
    public $AbilityComment;
    public $IsApproved = 0;
    public $ApprovedByPersonId;
    public $ApprovedDate;
    public $CreatorPersonId;
    

    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $role = static::newWithDefault();
        $role->setValuesByArray($post);
        return $role;
    }
    
    public function setValuesByArray($arr) {

        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Profession'])) $this->Profession = $arr['Profession'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['DescriptionForGroup'])) $this->DescriptionForGroup = $arr['DescriptionForGroup'];
        if (isset($arr['DescriptionForOthers'])) $this->DescriptionForOthers = $arr['DescriptionForOthers'];
        if (isset($arr['PreviousLarps'])) $this->PreviousLarps = $arr['PreviousLarps'];
        if (isset($arr['ReasonForBeingInSlowRiver'])) $this->ReasonForBeingInSlowRiver = $arr['ReasonForBeingInSlowRiver'];
        if (isset($arr['ReligionId'])) $this->ReligionId = $arr['ReligionId'];
        if (isset($arr['Religion'])) $this->Religion = $arr['Religion'];
        if (isset($arr['BeliefId'])) $this->BeliefId = $arr['BeliefId'];
        if (isset($arr['DarkSecret'])) $this->DarkSecret = $arr['DarkSecret'];
        if (isset($arr['DarkSecretIntrigueIdeas'])) $this->DarkSecretIntrigueIdeas = $arr['DarkSecretIntrigueIdeas'];
        if (isset($arr['IntrigueSuggestions'])) $this->IntrigueSuggestions = $arr['IntrigueSuggestions'];
        if (isset($arr['NotAcceptableIntrigues'])) $this->NotAcceptableIntrigues = $arr['NotAcceptableIntrigues'];
        if (isset($arr['OtherInformation'])) $this->OtherInformation = $arr['OtherInformation'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['GroupId'])) $this->GroupId = $arr['GroupId'];
        if (isset($arr['WealthId'])) $this->WealthId = $arr['WealthId'];
        if (isset($arr['PlaceOfResidenceId'])) $this->PlaceOfResidenceId = $arr['PlaceOfResidenceId'];
        if (isset($arr['RaceId'])) $this->RaceId = $arr['RaceId'];
        if (isset($arr['RoleFunctionComment'])) $this->RoleFunctionComment = $arr['RoleFunctionComment'];
        if (isset($arr['Birthplace'])) $this->Birthplace = $arr['Birthplace'];
        if (isset($arr['CharactersWithRelations'])) $this->CharactersWithRelations = $arr['CharactersWithRelations'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['IsDead'])) $this->IsDead = $arr['IsDead'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['NoIntrigue'])) $this->NoIntrigue = $arr['NoIntrigue'];
        if (isset($arr['LarperTypeId'])) $this->LarperTypeId = $arr['LarperTypeId'];
        if (isset($arr['TypeOfLarperComment'])) $this->TypeOfLarperComment = $arr['TypeOfLarperComment'];
        if (isset($arr['RaceComment'])) $this->RaceComment = $arr['RaceComment'];
        if (isset($arr['AbilityComment'])) $this->AbilityComment = $arr['AbilityComment'];
        if (isset($arr['IsApproved'])) $this->IsApproved = $arr['IsApproved'];
        if (isset($arr['ApprovedByPersonId'])) $this->ApprovedByPersonId = $arr['ApprovedByPersonId'];
        if (isset($arr['ApprovedDate'])) $this->ApprovedDate = $arr['ApprovedDate'];
        if (isset($arr['CreatorPersonId'])) $this->CreatorPersonId = $arr['CreatorPersonId'];
        
        if (isset($this->PersonId) && $this->PersonId=='null') $this->PersonId = null;
        if (isset($this->ReligionId) && $this->ReligionId=='null') $this->ReligionId = null;
        if (isset($this->BeliefId) && $this->ReligionId=='null') $this->ReligionId = null;
        if (isset($this->LarperTypeId) && $this->LarperTypeId=='null') $this->LarperTypeId = null;
        if (isset($this->PlaceOfResidenceId) && $this->PlaceOfResidenceId=='null') $this->PlaceOfResidenceId = null;
        if (isset($this->RaceId) && $this->RaceId=='null') $this->RaceId = null;
        if (isset($this->WealthId) && $this->WealthId=='null') $this->WealthId = null;
        if (isset($this->GroupId) && $this->GroupId=='null') $this->GroupId = null;
        if (isset($this->ImageId) && $this->ImageId=='null') $this->ImageId = null;
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $newOne = new self();
        $newOne->CampaignId = $current_larp->CampaignId;
        return $newOne;
    }
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_role SET Name=?, Profession=?, Description=?,
                              DescriptionForGroup=?, DescriptionForOthers=?,
                              PreviousLarps=?, ReasonForBeingInSlowRiver=?, ReligionId=?, Religion=?, BeliefId=?, DarkSecret=?,
                              DarkSecretIntrigueIdeas=?, IntrigueSuggestions=?, NotAcceptableIntrigues=?, OtherInformation=?,
                              PersonId=?, GroupId=?, WealthId=?, PlaceOfResidenceId=?, RaceId=?, RoleFunctionComment=?, Birthplace=?, 
                              CharactersWithRelations=?, CampaignId=?, ImageId=?, IsDead=?, OrganizerNotes=?, 
                              NoIntrigue=?, LarperTypeId=?, TypeOfLarperComment=?, RaceComment=?, AbilityComment=?, IsApproved=?, ApprovedByPersonId=?, ApprovedDate=?, CreatorPersonId=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->Profession, $this->Description, 
            $this->DescriptionForGroup, $this->DescriptionForOthers, $this->PreviousLarps, 
            $this->ReasonForBeingInSlowRiver, $this->ReligionId, $this->Religion, $this->BeliefId, $this->DarkSecret, $this->DarkSecretIntrigueIdeas,
            $this->IntrigueSuggestions, $this->NotAcceptableIntrigues, $this->OtherInformation, $this->PersonId, 
            $this->GroupId, $this->WealthId, $this->PlaceOfResidenceId, $this->RaceId,  
            $this->RoleFunctionComment, $this->Birthplace, $this->CharactersWithRelations, $this->CampaignId, $this->ImageId, $this->IsDead, 
            $this->OrganizerNotes, $this->NoIntrigue, $this->LarperTypeId, $this->TypeOfLarperComment, 
            $this->RaceComment, $this->AbilityComment, $this->IsApproved, $this->ApprovedByPersonId, $this->ApprovedDate, $this->CreatorPersonId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() { 
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_role (Name, Profession, Description, 
                                                            DescriptionForGroup, DescriptionForOthers, PreviousLarps,
                                                            ReasonForBeingInSlowRiver, ReligionId, Religion, BeliefId, DarkSecret, DarkSecretIntrigueIdeas,
                                                            IntrigueSuggestions, NotAcceptableIntrigues, OtherInformation, PersonId,
                                                            GroupId, WealthId, PlaceOfResidenceId, RaceId,  
                                                            RoleFunctionComment, Birthplace, CharactersWithRelations, CampaignId, ImageId, 
                                    IsDead, OrganizerNotes, NoIntrigue, LarperTypeId, TypeOfLarperComment, RaceComment, AbilityComment, IsApproved, ApprovedByPersonId, ApprovedDate, CreatorPersonId) 
                                    VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?);");

        if (!$stmt->execute(array($this->Name, $this->Profession, $this->Description, 
            $this->DescriptionForGroup, $this->DescriptionForOthers,$this->PreviousLarps,
            $this->ReasonForBeingInSlowRiver, $this->ReligionId, $this->Religion, $this->BeliefId, $this->DarkSecret, $this->DarkSecretIntrigueIdeas,
            $this->IntrigueSuggestions, $this->NotAcceptableIntrigues, $this->OtherInformation, $this->PersonId,
            $this->GroupId, $this->WealthId, $this->PlaceOfResidenceId, $this->RaceId, 
            $this->RoleFunctionComment, $this->Birthplace, $this->CharactersWithRelations, $this->CampaignId, $this->ImageId, 
            $this->IsDead, $this->OrganizerNotes, $this->NoIntrigue, $this->LarperTypeId, $this->TypeOfLarperComment,
            $this->RaceComment, $this->AbilityComment, $this->IsApproved, $this->ApprovedByPersonId, $this->ApprovedDate, $this->CreatorPersonId
        ))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    
    public function getGroup() {
        return Group::loadById($this->GroupId);
    }
    
    public function getPerson() {
        if (isset($this->PersonId)) return Person::loadById($this->PersonId);
        else return null;
    }
    
    public function getCreator() {
        if (isset($this->CreatorPersonId)) return Person::loadById($this->CreatorPersonId);
        elseif (isset($this->PersonId)) return Person::loadById($this->PersonId);
        else return null;
    }
    
    public function getCampaign() {
        return Campaign::loadById($this->CampaignId);
    }

    public function isRegistered(LARP $larp) {
        if (LARP_Role::isRegistered($this->Id, $larp->Id) || Reserve_LARP_Role::isReserve($this->Id, $larp->Id)) return true;
        return false;
    } 

    public function userMayEdit(LARP $larp) {
        return LARP_Role::userMayEdit($this->Id, $larp->Id);
    }
    
    public function getReligion() {
        if (is_null($this->ReligionId)) return null;
        return Religion::loadById($this->ReligionId);
    }
    
    public function getBelief() {
        if (is_null($this->BeliefId)) return null;
        return Belief::loadById($this->BeliefId);
    }
    
    public function getWealth() {
        if (is_null($this->WealthId)) return null;
        return Wealth::loadById($this->WealthId);
    }
    
    public function getPlaceOfResidence() {
        if (is_null($this->PlaceOfResidenceId)) return null;
        return PlaceOfResidence::loadById($this->PlaceOfResidenceId);
    }
    
    public function getRace() {
        if (is_null($this->RaceId)) return null;
        return Race::loadById($this->RaceId);
    }
    
     public function isApproved() {
        if ($this->IsApproved == 1) return true;
        return false;
    }
    
    public function isPC() {
        return !$this->isNPC();
    }
    
    public function isNPC() {
        if (empty($this->PersonId)) return true;
        return false;
    }
    
    public function hasIntrigue(LARP $larp) {
        $larp_role = LARP_Role::loadByIds($this->Id, $larp->Id);
        if (isset($larp_role->Intrigue) && $larp_role->Intrigue != "") return true;
        $intrigues = Intrigue::getAllIntriguesForRole($this->Id, $larp->Id);
        if (!empty($intrigues)) return true;
        return false;
        
    }
    
    public function intrigueWords(LARP $larp) {
        $wordCount = 0;
        $larp_role = LARP_Role::loadByIds($this->Id, $larp->Id);
        if (isset($larp_role->Intrigue) && $larp_role->Intrigue != "") {
            $wordCount += str_word_count($larp_role->Intrigue);
        }
        $intrigues = Intrigue::getAllIntriguesForRole($this->Id, $larp->Id);
        foreach ($intrigues as $intrigue) {
            $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $this);
            $wordCount += str_word_count($intrigueActor->IntrigueText);
        }
        return $wordCount;
    }
    
    public function getLarperType() {
        if (is_null($this->LarperTypeId)) return null;
        return LarperType::loadById($this->LarperTypeId);
    }
    
    
    public function getRegistration(LARP $larp) {
        return Registration::loadByIds($this->PersonId, $larp->Id);
    }
    
    public function isMain(LARP $larp) {
        $larp_role = LARP_Role::loadByIds($this->Id, $larp->Id);
        if (!empty($larp_role)) return $larp_role->IsMainRole;
 
        $reserve_larp_role = Reserve_LARP_Role::loadByIds($this->Id, $larp->Id);
        if (!empty($reserve_larp_role)) return $reserve_larp_role->IsMainRole;
        
        return false;
        
    }
    
    public function hasImage() {
        if (isset($this->ImageId)) return true;
        return false;
    }
    
    public function getImage() {
        if (empty($this->ImageId)) return null;
        return Image::loadById($this->ImageId);
    }
    
    public function isMysLajvare() {
        if ($this->NoIntrigue == 1) return true;
        return false;

    }
    
    
    public function getPreviousLarps() {
        return LARP::getPreviousLarpsRole($this->Id);
    }
  
    public function getOldApprovedRole() {
        return RoleApprovedCopy::getOldRole($this->Id);
    }
    
    
    
    public static function getRolesForPerson($personId, $campaignId) {
        if (is_null($personId)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE PersonId = ? AND CampaignId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($personId, $campaignId));
    }
    
    public static function getAllRolesForPerson($personId) {
        if (is_null($personId)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE PersonId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($personId));
    }
    
    public static function getAliveRolesForPerson($personId, $campaignId) {
        if (is_null($personId)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE PersonId = ? AND IsDead=0 AND CampaignId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($personId, $campaignId));
    }
    
    # Hämta de karaktärer en person har anmält till ett lajv
    public static function getRegistredRolesForPerson(Person $person, LARP $larp) {
        if (is_null($person) || is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role, regsys_larp_role WHERE ".
        "regsys_role.PersonId = ? AND ".
        "regsys_role.Id=regsys_larp_role.RoleId AND ".
        "regsys_larp_role.LarpId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id, $larp->Id));
    }

    public static function getUnregistredRolesForPerson(Person $person, LARP $larp) {
        if (is_null($person) || is_null($larp)) return Array();
        $sql = "SELECT regsys_role.* FROM regsys_role WHERE ".
        "regsys_role.PersonId = ? AND ".
        "regsys_role.CampaignId=? AND ".
        "regsys_role.Id NOT IN (SELECT RoleId FROM regsys_larp_role WHERE ".
        "regsys_larp_role.LarpId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id, $larp->CampaignId, $larp->Id));
    }
   
    # Hämta de karaktärer en person på reservlistan har anmält till ett lajv
    public static function getReserveRegistredRolesForPerson(Person $person, LARP $larp) {
        if (is_null($person) || is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role, regsys_reserve_larp_role WHERE ".
            "regsys_role.PersonId = ? AND ".
            "regsys_role.Id=regsys_reserve_larp_role.RoleId AND ".
            "regsys_reserve_larp_role.LarpId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id, $larp->Id));
    }
    
    # Hämta huvudkaraktären för en person har anmält till ett lajv
    public static function getMainRoleForPerson(Person $person, LARP $larp) {
        if (is_null($person) || is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role, regsys_larp_role WHERE ".
            "regsys_role.PersonId = ? AND ".
            "regsys_role.Id=regsys_larp_role.RoleId AND ".
            "regsys_larp_role.IsMainRole = 1 AND ".
            "regsys_larp_role.LarpId=?;";
        return static::getOneObjectQuery($sql, array($person->Id, $larp->Id));
    }

    public static function getAllInCampaign($campaignId) {
        $sql = "SELECT * FROM regsys_role WHERE CampaignId=? ".
            "ORDER BY GroupId, Name;";
        return static::getSeveralObjectsqQuery($sql, array($campaignId));
    }
    
    public static function getAllRoles(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE Id IN ".
            "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
            "regsys_larp_role.larpid = regsys_registration.larpid AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_larp_role.larpid=?) ".
            "ORDER BY GroupId, Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllMainRoles(LARP $larp, $includeNotComing) {
        if (is_null($larp)) return Array();
        if ($includeNotComing) {
            $sql = "SELECT * FROM regsys_role WHERE Id IN ".
                "(SELECT RoleId FROM regsys_larp_role WHERE larpid=? AND IsMainRole=1) ".
                "ORDER BY GroupId, Name;";
        }
        else {
            $sql = "SELECT * FROM regsys_role WHERE Id IN ".
                "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
                "regsys_larp_role.RoleId = regsys_role.Id AND ".
                "regsys_larp_role.LarpId = regsys_registration.LarpId AND ".
                "regsys_role.PersonId = regsys_registration.PersonId AND ".
                "regsys_registration.NotComing = 0 AND ".
                "regsys_larp_role.larpid=? AND ".
                "IsMainRole=1) ".
                "ORDER BY GroupId, Name;";
        }
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllMainRolesNoMyslajvare(LARP $larp) {
        if (is_null($larp)) return Array();

        $sql = "SELECT * FROM regsys_role WHERE Id IN ".
            "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_larp_role.LarpId = regsys_registration.LarpId AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_larp_role.larpid=? AND ".
            "regsys_role.NoIntrigue = 0 AND ".
            "IsMainRole=1) ".
            "ORDER BY GroupId, Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllUnregisteredRoles(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE Id NOT IN ".
            "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
            "regsys_larp_role.larpid = regsys_registration.larpid AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_larp_role.larpid=?) AND ".
            "CampaignId = ? AND IsDead=0 ORDER BY PersonId, Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $larp->CampaignId));
    }
    
    public static function getAllMainRolesInGroup(Group $group, LARP $larp) {
        if (is_null($group) or is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE Id IN ".
            "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
            "regsys_larp_role.larpid = regsys_registration.larpid AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "groupId =? AND ".
            "regsys_larp_role.larpid=? AND IsMainRole=1) ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
    }
    
 
    public static function getAllComingApprovedMainRolesInGroup(Group $group, LARP $larp) {
        if (is_null($group) or is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE Id IN ".
            "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
            "regsys_role.PersonId IS NOT NULL AND ".
            "regsys_larp_role.larpid = regsys_registration.larpid AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.SpotAtLARP = 1 AND ".
            "groupId =? AND ".
            "regsys_larp_role.larpid=? AND IsMainRole=1) AND ".
            "IsApproved = 1 ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
    }
    
    public static function getAllNonMainRolesInGroup(Group $group, LARP $larp) {
        if (is_null($group) or is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE Id IN ".
            "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
            "regsys_role.PersonId IS NOT NULL AND ".
            "regsys_larp_role.larpid = regsys_registration.larpid AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "groupId =? AND ".
            "regsys_larp_role.larpid=? AND IsMainRole=0) ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
    }
    
    
    public static function getAllMainRolesWithoutGroup(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE Id IN ".
            "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
            "regsys_role.PersonId IS NOT NULL AND ".
            "regsys_larp_role.larpid = regsys_registration.larpid AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "groupId IS NULL AND ".
            "regsys_larp_role.larpid=? AND IsMainRole=1) ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
 
    
    public static function getAllComingApprovedMainRolesWithoutGroup(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE Id IN ".
            "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
            "regsys_role.PersonId IS NOT NULL AND ".
            "regsys_larp_role.larpid = regsys_registration.larpid AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.SpotAtLARP = 1 AND ".
            "groupId IS NULL AND ".
            "regsys_larp_role.larpid=? AND IsMainRole=1) AND ".
            "IsApproved = 1 ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public static function getAllNonMainRolesWithoutGroup(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE Id IN ".
            "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
            "regsys_role.PersonId IS NOT NULL AND ".
            "regsys_larp_role.larpid = regsys_registration.larpid AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 0 AND ".
            "groupId IS NULL AND ".
            "regsys_larp_role.larpid=? AND IsMainRole=0) ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public static function getAllNotMainRolesNoMyslavare(LARP $larp) {
        if (is_null($larp)) return Array();

            $sql = "SELECT * FROM regsys_role WHERE Id IN ".
                "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
                "regsys_role.PersonId IS NOT NULL AND ".
                "regsys_larp_role.LarpId = regsys_registration.LarpId AND ".
                "regsys_larp_role.RoleId = regsys_role.Id AND ".
                "regsys_role.PersonId = regsys_registration.PersonId AND ".
                "regsys_registration.NotComing = 0 AND ".
                "regsys_larp_role.larpid=? AND ".
                "regsys_role.NoIntrigue = 0 AND ".
                "IsMainRole=0) ".
                "ORDER BY GroupId, Name;";  
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllNotMainRoles(LARP $larp, $includeNotComing) {
        if (is_null($larp)) return Array();
        if ($includeNotComing) {
            $sql = "SELECT * FROM regsys_role WHERE Id IN ".
                "(SELECT RoleId FROM regsys_larp_role WHERE ".
                "larpId =? AND IsMainRole=0) ORDER BY GroupId;";
        }
        else {
            $sql = "SELECT * FROM regsys_role WHERE Id IN ".
                "(SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE ".
                "regsys_role.PersonId IS NOT NULL AND ".
                "regsys_larp_role.LarpId = regsys_registration.LarpId AND ".
                "regsys_larp_role.RoleId = regsys_role.Id AND ".
                "regsys_role.PersonId = regsys_registration.PersonId AND ".
                "regsys_registration.NotComing = 0 AND ".
                "regsys_larp_role.larpid=? AND ".
                "IsMainRole=0) ".
                "ORDER BY GroupId, Name;";
        }
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllUnregisteredRolesInGroup(Group $group, LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE GroupId=? AND ".
            "regsys_role.PersonId IS NOT NULL AND ".
            "(Id NOT IN ".
            "(SELECT RoleId FROM regsys_larp_role WHERE ".
            "regsys_larp_role.larpid = ?) OR ID IN ".
            "(SELECT regsys_role.Id FROM regsys_role, regsys_registration, regsys_larp_role WHERE ".
            "regsys_registration.larpid = regsys_larp_role.larpid AND ".
            "regsys_role.Id = regsys_larp_role.RoleId AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.NotComing = 1 AND ".
            "regsys_larp_role.larpid=?)) ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id, $larp->Id));
    }

    public static function getAllNPCsInGroup(Group $group) {
        $sql = "SELECT * FROM regsys_role WHERE GroupId=? AND PersonId IS NULL  ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($group->Id));
    }
    
    
    public static function getTitledeedOwners(Titledeed $titledeed) {
        $sql = "SELECT * FROM regsys_role WHERE Id IN ".
            "(SELECT RoleId FROM regsys_titledeed_role WHERE ".
            "TitledeedId =?) ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
        
    }
    
    public function groupIsRegisteredApproved(Larp $larp) {
        if (!isset($this->GroupId)) return true;
        $group = $this->GetGroup();
        if ($group->isRegistered($larp) && $group->isApproved()) return true;
        return false;
    }
    
    public function isNeverRegistered() {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_larp_role WHERE RoleId=?;";
        
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
    
    public function is_trading(LARP $larp) {
        $campaign = $larp->getCampaign();
        if (!$campaign->is_dmh()) return false;
        if ($this->isMysLajvare()) return false;
        if ($this->WealthId > 3) return true;
        
        //Äger verksamhet
        $titledeeds = Titledeed::getAllForRole($this);
        if (!empty($titledeeds)) return true;
        
        $intrigtyper = commaStringFromArrayObject($this->getIntrigueTypes());
        return (str_contains($intrigtyper, 'Handel'));
    }
    
    public function lastLarp() {
        return LARP::lastLarpRole($this);
    }
    
    # Hämta intrigtyperna
    public function getIntrigueTypes(){
        return IntrigueType::getIntrigeTypesForRole($this->Id);
    }
    
    
    
    
    public function getSelectedIntrigueTypeIds() {
        $stmt = $this->connect()->prepare("SELECT IntrigueTypeId FROM  regsys_intriguetype_role WHERE RoleId = ? ORDER BY IntrigueTypeId;");
        
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
            $resultArray[] = $row['IntrigueTypeId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    public function saveAllIntrigueTypes($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_intriguetype_role (IntrigueTypeId, RoleId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllIntrigueTypes() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_intriguetype_role WHERE RoleId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    # Hämta intrigtyperna
    public function getAbilities(){
        return Ability::getAbilitiesForRole($this->Id);
    }
    
    public function getSelectedAbilityIds() {
        $stmt = $this->connect()->prepare("SELECT AbilityId FROM  regsys_ability_role WHERE RoleId = ? ORDER BY AbilityId;");
        
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
            $resultArray[] = $row['AbilityId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    public function saveAllAbilities($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_ability_role (AbilityId, RoleId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllAbilities() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_ability_role WHERE RoleId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public function getRoleFunctions(){
        return RoleFunction::getRoleFunctionsForRole($this->Id);
    }
    
    public function getSelectedRoleFunctionIds() {
        $stmt = $this->connect()->prepare("SELECT RoleFunctionId FROM  regsys_rolefunction_role WHERE RoleId = ? ORDER BY RoleFunctionId;");
        
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
            $resultArray[] = $row['RoleFunctionId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    public function saveAllRoleFunctions($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_rolefunction_role (RoleFunctionId, RoleId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllRoleFunctions() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_rolefunction_role WHERE RoleId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public function getAllKnownNPCGroups(LARP $larp) {
        return IntrigueActor_KnownNPCGroup::getAllKnownNPCGroupsForRole($this, $larp);
    }

    public function getAllKnownNPCs(LARP $larp) {
        return IntrigueActor_KnownNPC::getAllKnownNPCsForRole($this, $larp);
    }
 
    public function getAllKnownProps(LARP $larp) {
        return IntrigueActor_KnownProp::getAllKnownPropsForRole($this, $larp);
    }
    
    public function getAllKnownPdfs(LARP $larp) {
        return IntrigueActor_KnownPdf::getAllKnownPdfsForRole($this, $larp);
    }
    
    public function getAllCheckinLetters(LARP $larp) {
        return IntrigueActor_CheckinLetter::getAllCheckinLettersForRole($this, $larp);
    }
    
    public function getAllCheckinTelegrams(LARP $larp) {
        return IntrigueActor_CheckinTelegram::getAllCheckinTelegramsForRole($this, $larp);
    }
    
    public function getAllCheckinProps(LARP $larp) {
        return IntrigueActor_CheckinProp::getAllCheckinPropsForRole($this, $larp);
    }
    

    public function getAllKnownGroups(LARP $larp) {
        return Group::getAllKnownGroupsForRole($this, $larp);
    }
    
    public function getAllKnownRoles(LARP $larp) {
        return Role::getAllKnownRolesForRole($this, $larp);
    }
    
    public static function getAllKnownRolesForRole(Role $role, LARP $larp) {
        $sql = "SELECT * FROM regsys_role WHERE Id IN (".
            "SELECT iak.RoleId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
            "ias.RoleId = ? AND ".
            "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
            "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
            "ias.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($role->Id, $larp->Id));
    }
    
    public static function getAllKnownRolesForGroup(Group $group, LARP $larp) {
        $sql = "SELECT * FROM regsys_role WHERE Id IN (".
            "SELECT iak.RoleId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
            "ias.GroupId = ? AND ".
            "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
            "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
            "ias.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
    }
    
    
    public static function getAllKnownRolesForSubdivision(Subdivision $subdivision, LARP $larp) {
        $sql = "SELECT * FROM regsys_role WHERE Id IN (".
            "SELECT iak.RoleId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
            "ias.SubdivisionId = ? AND ".
            "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
            "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
            "ias.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($subdivision->Id, $larp->Id));
    }
    
    public static function getAllToApprove($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_role WHERE Id IN ".
        "(SELECT RoleId FROM regsys_role, regsys_larp_role, regsys_registration WHERE ".
        "regsys_larp_role.LarpId = ? AND ".
        "regsys_larp_role.RoleId = regsys_role.Id AND ".
        "regsys_role.PersonId = regsys_registration.PersonId AND ".
        "regsys_larp_role.LarpId = regsys_registration.LarpId AND ".
        "regsys_registration.NotComing=0".
        ") AND IsApproved = 0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }

    public function getViewLink() {
        $vrole = "<a href='view_role.php?id={$this->Id}'>{$this->Name}</a>";

        if ($this->IsDead) {
            $vrole .= " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
        }

        return $vrole;
    }
    
    public function getLink() {
        return "view_role.php?id=$this->Id";
    }
    
    
    public function getEditLinkPen($isAdmin) {
        global $current_larp;
        if($isAdmin) {
            return "<a href='edit_role.php?id=" . $this->Id . "'><i class='fa-solid fa-pen' title='Redigera karaktären'></i></a>";
        }
        else {
            $larpBefore = $current_larp->LarpBeforeThisWithRegistration($this);
            if ($this->isPC() && !$this->isRegistered($current_larp) && !empty($larpBefore)) {
                return "<i class='fa-solid fa-pen' title='Får inte redigeras pga anmälan till $larpBefore->Name' style='text-decoration: line-through;'></i> ";
            }
            return "<a href='role_form.php?operation=update&id=$this->Id'><i class='fa-solid fa-pen'></i></a>";
        }
    }
    
    public function approve($larp, $person) {
        $oldCopy = $this->getOldApprovedRole();
        if (isset($oldCopy)) RoleApprovedCopy::delete($oldCopy->Id);
        
        $this->IsApproved = 1;
        $this->ApprovedByPersonId = $person->Id;
        $now = new Datetime();
        $this->ApprovedDate = date_format($now,"Y-m-d H:i:s");
        $this->update();
        
        BerghemMailer::send_role_approval_mail($this, $larp, $person->Id);
    }
    
    public function unapprove($larp, $sendMail, $person) {
        $this->IsApproved = 0;
        $this->ApprovedByPersonId = null;
        $this->ApprovedDate = null;
        $this->update();
        
        $senderId = NULL;
        if (isset($person)) $senderId = $person->Id;
        
        if ($sendMail) BerghemMailer::send_role_unapproval_mail($this, $larp, $senderId);
        
    }
    
    public function deleteAllSubdivisionMembership() {
         $stmt = $this->connect()->prepare("DELETE FROM regsys_subdivisionmember WHERE RoleId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    
    }
    
    public static function delete($id)
    {
        $role = static::loadById($id);
        
        $role->deleteAllAbilities();
        $role->deleteAllIntrigueTypes();
        $role->deleteAllRoleFunctions();
        $role->deleteAllSubdivisionMembership();
        
        parent::delete($id);
    }
    
    
    public static function getAllWithTypeValue($larpId, $typeName, $valueId) {
        return static::getAllWithTypeValues($larpId, $typeName, array($valueId));
     }
    
    public static function getAllWithTypeValues($larpId, $typeName, $valueIds) {
        $larp = LARP::loadById($larpId);
        
        $roles_at_larp_SQL = "SELECT RoleId FROM regsys_larp_role, regsys_registration, regsys_role WHERE regsys_larp_role.LarpId=? AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_larp_role.LarpId = regsys_registration.LarpId AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = regsys_registration.PersonId";
        
        
        $placeholders = rtrim(str_repeat('?,', count($valueIds)), ',');
         
        switch ($typeName) {
            case "Wealth":
                $sql = "SELECT * FROM regsys_role WHERE WealthId IN ($placeholders) AND Id IN ($roles_at_larp_SQL) ORDER BY Name";
                break;
            case "PlaceOfResidence":
                $sql = "SELECT * FROM regsys_role WHERE PlaceOfResidenceId IN ($placeholders) AND Id IN ($roles_at_larp_SQL) ORDER BY Name";
                break;
            case "LarperType":
                $sql = "SELECT * FROM regsys_role WHERE LarperTypeId IN ($placeholders) AND Id IN ($roles_at_larp_SQL) ORDER BY Name";
                break;
            case "IntrigueType":
                $sql = "SELECT * FROM regsys_role WHERE Id IN (SELECT RoleId FROM regsys_intriguetype_role WHERE IntrigueTypeId IN ($placeholders)) AND Id IN ($roles_at_larp_SQL) ORDER BY Name";
                break;
            case "Race":
                $sql = "SELECT * FROM regsys_role WHERE RaceId IN ($placeholders) AND Id IN ($roles_at_larp_SQL) ORDER BY Name";
                break;
            case "Ability":
                $sql = "SELECT * FROM regsys_role WHERE Id IN (SELECT RoleId FROM regsys_ability_role WHERE AbilityId IN ($placeholders)) AND Id IN ($roles_at_larp_SQL) ORDER BY Name";
                break;
            case "Religion":
                $sql = "SELECT * FROM regsys_role WHERE ReligionId IN ($placeholders) AND Id IN ($roles_at_larp_SQL) ORDER BY Name";
                break;
            case "RoleFunction":
                $sql = "SELECT * FROM regsys_role WHERE Id IN (SELECT RoleId FROM regsys_rolefunction_role WHERE RoleFunctionId IN ($placeholders)) AND Id IN ($roles_at_larp_SQL) ORDER BY Name";
                break;
        }
        $params = $valueIds;
        $params[] = $larp->Id;
        return static::getSeveralObjectsqQuery($sql, $params);
    }
    
    
    
    
    
    public function hasRegisteredWhatHappened(LARP $larp) {
        $larp_role = LARP_Role::loadByIds($this->Id, $larp->Id);
        if (!empty($larp_role->WhatHappened) OR !empty($larp_role->WhatHappendToOthers) || !empty($larp_role->WhatHappensAfterLarp)) return true;
        
        $intrigues = Intrigue::getAllIntriguesForRole($this->Id, $larp->Id);
        foreach ($intrigues as $intrigue) {
            $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $this);
            if (!empty($intrigueActor->WhatHappened)) return true;
        }
        return false;
    }
    
    public function inSubdivisionInIntrigue(Intrigue $intrigue) {
        //Kolla om rollen finns med i en gruppering som finns i intrigen
        $sql = "SELECT count(*) as Num FROM regsys_intrigueactor, regsys_subdivision, regsys_subdivisionmember WHERE ".
            "regsys_intrigueactor.IntrigueId = ? AND ".
            "regsys_intrigueactor.SubdivisionId =  regsys_subdivisionmember.SubdivisionId AND ".
            "regsys_subdivisionmember.RoleId = ? ";
        if (static::existsQuery($sql, array($intrigue->Id, $this->Id))) return true;
        return false;
    }
    
}