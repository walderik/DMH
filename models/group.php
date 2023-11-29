<?php

class Group extends BaseModel{
    
    public $Id;
    public $Name;
    public $Friends;
    public $Enemies;
    public $Description;
    public $DescriptionForOthers;
    public $IntrigueIdeas;
    public $OtherInformation;
    public $WealthId;
    public $PlaceOfResidenceId;
    public $PersonId; # Gruppansvarig
    public $CampaignId;
    public $IsDead = 0;
    public $OrganizerNotes;
    public $ImageId;
    public $IsApproved = 0;
    
//     public static $tableName = 'group';
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $group = static::newWithDefault();
        $group->setValuesByArray($post);
        return $group;
    }
     
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Friends'])) $this->Friends = $arr['Friends'];
        if (isset($arr['Enemies'])) $this->Enemies = $arr['Enemies'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['DescriptionForOthers'])) $this->DescriptionForOthers = $arr['DescriptionForOthers'];
        if (isset($arr['IntrigueIdeas'])) $this->IntrigueIdeas = $arr['IntrigueIdeas'];
        if (isset($arr['OtherInformation'])) $this->OtherInformation = $arr['OtherInformation'];
        if (isset($arr['WealthId'])) $this->WealthId = $arr['WealthId'];
        if (isset($arr['PlaceOfResidenceId'])) $this->PlaceOfResidenceId = $arr['PlaceOfResidenceId'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['IsDead'])) $this->IsDead = $arr['IsDead'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['IsApproved'])) $this->IsApproved = $arr['IsApproved'];
        
        if (isset($this->ImageId) && $this->ImageId=='null') $this->ImageId = null;
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $newOne = new self();
        $newOne->CampaignId = $current_larp->CampaignId;
        return $newOne;
    }
    
    # Update an existing group in db
    public function update() {
       
        $stmt = $this->connect()->prepare("UPDATE regsys_group SET Name=?, Friends=?, Enemies=?,
                                                                  Description=?, DescriptionForOthers=?, IntrigueIdeas=?, OtherInformation=?,
                                                                  WealthId=?, PlaceOfResidenceId=?, PersonId=?, CampaignId=?, IsDead=?, OrganizerNotes=?, ImageId=?, IsApproved=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Friends, $this->Enemies,
            $this->Description, $this->DescriptionForOthers, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthId, $this->PlaceOfResidenceId, $this->PersonId, 
            $this->CampaignId, $this->IsDead, $this->OrganizerNotes, $this->ImageId, $this->IsApproved, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    
    # Create a new group in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_group (Name,  
                         Friends, Description, DescriptionForOthers, Enemies, IntrigueIdeas, OtherInformation, 
                         WealthId, PlaceOfResidenceId, PersonId, CampaignId, IsDead, OrganizerNotes, ImageId, IsApproved) 
                         VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name,  
            $this->Friends, $this->Description, $this->DescriptionForOthers, $this->Enemies, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthId, 
            $this->PlaceOfResidenceId, $this->PersonId, $this->CampaignId, $this->IsDead, $this->OrganizerNotes, $this->ImageId, $this->IsApproved))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $this->Id = $connection->lastInsertId();
        $stmt = null;
     }
    
     public function getWealth() {
        if (is_null($this->WealthId)) return null;
        return Wealth::loadById($this->WealthId);
     }
    
     public function hasImage() {
         if (isset($this->ImageId)) return true;
         return false;
     }
     
     public function isApproved() {
         if ($this->IsApproved == 1) return true;
         return false;
     }
     
     
     
     public function is_trading(LARP $larp) {
         $campaign = $larp->getCampaign();
         if (!$campaign->is_dmh()) return false;
         if ($this->WealthId > 2) return true;
         $larp_group = LARP_Group::loadByIds($this->Id, $larp->Id);
         
         //Äger lagfart
         $titledeeds = Titledeed::getAllForGroup($this);
         if (!empty($titledeeds)) return true;
         
         $intrigtyper = commaStringFromArrayObject($this->getIntrigueTypes());
         return (str_contains($intrigtyper, 'Handel'));
     }
     
     public function getIntrigueTypes(){
         return IntrigueType::getIntrigeTypesForGroup($this->Id);
     }
     
     
     public function getPlaceOfResidence() {
        if (is_null($this->PlaceOfResidenceId)) return null;
        return PlaceOfResidence::loadById($this->PlaceOfResidenceId);
     }
    
     # Ansvarig för gruppen
     public function getPerson() {
         return Person::loadById($this->PersonId);
     }

     public function getCampaign() {
         return Campaign::loadById($this->CampaignId);
     }
     
     public function isRegistered($larp) {
         return LARP_Group::isRegistered($this->Id, $larp->Id);

     }
     
     public function userMayEdit(LARP $larp) {
         return LARP_Group::userMayEdit($this->Id, $larp->Id);
         
     }
     
     public function hasIntrigue(LARP $larp) {
         $larp_group = LARP_Group::loadByIds($this->Id, $larp->Id);
         if (!empty($larp_group->Intrigue)) return true;
         $intrigues = Intrigue::getAllIntriguesForGroup($this->Id, $larp->Id);
         if (!empty($intrigues)) return true;
         return false;
     }
     
     public function intrigueWords(LARP $larp) {
         $wordCount = 0;
         $larp_group = LARP_Group::loadByIds($this->Id, $larp->Id);
         if (!empty($larp_group->Intrigue)) {
             $wordCount += str_word_count($larp_group->Intrigue);
         }
         $intrigues = Intrigue::getAllIntriguesForGroup($this->Id, $larp->Id);
         foreach ($intrigues as $intrigue) {
             $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $this);
             $wordCount += str_word_count($intrigueActor->IntrigueText);
         }
         return $wordCount;
     }
     
     public static function getAllToApprove($larp) {
         if (is_null($larp)) return array();
         $sql = "SELECT * from regsys_group WHERE Id in (SELECT GroupId FROM ".
             "regsys_larp_group WHERE LarpId = ?) AND IsApproved = 0 ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($larp->Id));
     }
     
     
     
     public static function getAllRegistered($larp) {
         
         if (is_null($larp)) return Array();
         $sql = "SELECT * FROM regsys_group WHERE IsDead=0 AND Id IN ".
             "(SELECT GroupId from regsys_larp_group where LARPId = ?) ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($larp->Id));
     }
     
     public static function getAllRegisteredApproved($larp) {
         
         if (is_null($larp)) return Array();
         $sql = "SELECT * FROM regsys_group WHERE IsDead=0 AND IsApproved=1 AND Id IN ".
             "(SELECT GroupId from regsys_larp_group where LARPId = ?) ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($larp->Id));
     }
     
     public static function getAllInCampaign($campaignId) {
         $sql = "SELECT * FROM regsys_group WHERE CampaignId=? ".
             "ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($campaignId));
     }
          
     public static function getAllGroupsForPerson($personId) {
         $sql = "SELECT * FROM regsys_group WHERE PersonId = ? ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($personId));
     }
     
     public static function getGroupsForPerson($personId, $campaignId) {
         $sql = "SELECT * FROM regsys_group WHERE PersonId = ? AND CampaignId = ? ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($personId, $campaignId));
     }
     
     public function isNeverRegistered() {         
         $sql = "SELECT COUNT(*) AS Num FROM regsys_larp_group WHERE GroupId=?;";
         
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
     
     public static function getTitledeedOwners(Titledeed $titledeed) {
         $sql = "SELECT * FROM regsys_group WHERE Id IN ".
             "(SELECT GroupId FROM regsys_titledeed_group WHERE ".
             "TitledeedId =?) ORDER BY Name;";
         return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
         
     }
     
     public function saveAllIntrigueTypes($post) {
         if (!isset($post['IntrigueTypeId'])) {
             return;
         }
         foreach($post['IntrigueTypeId'] as $Id) {
             $stmt = $this->connect()->prepare("INSERT INTO regsys_intriguetype_group (IntrigueTypeId, GroupId) VALUES (?,?);");
             if (!$stmt->execute(array($Id, $this->Id))) {
                 $stmt = null;
                 header("location: ../participant/index.php?error=stmtfailed");
                 exit();
             }
         }
         $stmt = null;
     }
     
     public function deleteAllIntrigueTypes() {
         $stmt = $this->connect()->prepare("DELETE FROM regsys_intriguetype_group WHERE GroupId = ?;");
         if (!$stmt->execute(array($this->Id))) {
             $stmt = null;
             header("location: ../participant/index.php?error=stmtfailed");
             exit();
         }
         $stmt = null;
     }
     
     public function getSelectedIntrigueTypeIds() {
         $stmt = $this->connect()->prepare("SELECT IntrigueTypeId FROM regsys_intriguetype_group WHERE GroupId = ? ORDER BY IntrigueTypeId;");
         
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
     
     public static function getGroupsInHouse(House $house, LARP $larp) {
         $sql = "SELECT * FROM regsys_group WHERE Id IN ".
             "(SELECT regsys_role.GroupId FROM regsys_housing, regsys_role, regsys_larp_role WHERE ". 
            "regsys_housing.HouseId = ? AND ".
            "regsys_housing.LarpId = ? AND ".
            "regsys_role.PersonId = regsys_housing.PersonId AND ".
            "regsys_role.Id = regsys_larp_role.RoleId AND ".
            "regsys_larp_role.LarpId = regsys_housing.LarpId AND ".
            "regsys_larp_role.IsMainRole=1".
            ") ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($house->Id, $larp->Id));
         
     }
     
     public function getAllKnownRoles(LARP $larp) {
         return Role::getAllKnownRolesForGroup($this, $larp);
     }
     
     public function getAllKnownGroups(LARP $larp) {
         return Group::getAllKnownGroupsForGroup($this, $larp);
     }
     
     public function getAllKnownNPCGroups(LARP $larp) {
         return IntrigueActor_KnownNPCGroup::getAllKnownNPCGroupsForGroup($this, $larp);
     }
     
     public function getAllKnownNPCs(LARP $larp) {
         return IntrigueActor_KnownNPC::getAllKnownNPCsForGroup($this, $larp);
     }
     
     public function getAllKnownProps(LARP $larp) {
         return IntrigueActor_KnownProp::getAllKnownPropsForGroup($this, $larp);
     }
     
     public function getAllKnownPdfs(LARP $larp) {
         return IntrigueActor_KnownPdf::getAllKnownPdfsForGroup($this, $larp);
     }
     
     public function getAllCheckinLetters(LARP $larp) {
         return IntrigueActor_CheckinLetter::getAllCheckinLettersForGroup($this, $larp);
     }
     
     public function getAllCheckinTelegrams(LARP $larp) {
         return IntrigueActor_CheckinTelegram::getAllCheckinTelegramsForGroup($this, $larp);
     }
     
     public function getAllCheckinProps(LARP $larp) {
         return IntrigueActor_CheckinProp::getAllCheckinPropsForGroup($this, $larp);
     }
     
     
     public static function getAllKnownGroupsForRole(Role $role, LARP $larp) {
         $sql = "SELECT * FROM regsys_group WHERE Id IN (".
             "SELECT iak.GroupId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
             "ias.RoleId = ? AND ".
             "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
             "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
             "ias.IntrigueId = regsys_intrigue.Id AND ".
             "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($role->Id, $larp->Id));
     }
     
     public static function getAllKnownGroupsForGroup(Group $group, LARP $larp) {
         $sql = "SELECT * FROM regsys_group WHERE Id IN (".
             "SELECT iak.GroupId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
             "ias.GroupId = ? AND ".
             "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
             "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
             "ias.IntrigueId = regsys_intrigue.Id AND ".
             "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
     }
     
     public static function getAllUnregisteredGroups(LARP $larp) {
         if (is_null($larp)) return Array();
         $sql = "SELECT * FROM regsys_group WHERE Id NOT IN ".
             "(SELECT GroupId FROM regsys_larp_group, regsys_group WHERE ".
             "regsys_larp_group.larpid = ?) AND ".
             "CampaignId = ? ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($larp->Id, $larp->CampaignId));
     }
     
     public function lastLarp() {
         return LARP::lastLarpGroup($this);
     }
     
     
     
     
     
}