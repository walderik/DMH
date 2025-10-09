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
    public $GroupTypeId;
    public $ShipTypeId;
    public $Colour;
    public $PersonId; # Gruppansvarig
    public $CampaignId;
    public $IsDead = 0;
    public $OrganizerNotes;
    public $ImageId;
    public $IsVisibleToParticipants = 1;
    public $IsApproved = 0;
    public $ApprovedByPersonId;
    public $ApprovedDate;
    
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
        if (isset($arr['GroupTypeId'])) $this->GroupTypeId = $arr['GroupTypeId'];
        if (isset($arr['ShipTypeId'])) $this->ShipTypeId = $arr['ShipTypeId'];
        if (isset($arr['Colour'])) $this->Colour = $arr['Colour'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['IsDead'])) $this->IsDead = $arr['IsDead'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['IsVisibleToParticipants'])) $this->IsVisibleToParticipants = $arr['IsVisibleToParticipants'];
        if (isset($arr['IsApproved'])) $this->IsApproved = $arr['IsApproved'];
        if (isset($arr['ApprovedByPersonId'])) $this->ApprovedByPersonId = $arr['ApprovedByPersonId'];
        if (isset($arr['ApprovedDate'])) $this->ApprovedDate = $arr['ApprovedDate'];
        
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
                    Description=?, DescriptionForOthers=?, IntrigueIdeas=?, OtherInformation=?, WealthId=?, PlaceOfResidenceId=?, 
                    GroupTypeId=?, ShipTypeId=?, Colour=?, PersonId=?, 
                    CampaignId=?, IsDead=?, OrganizerNotes=?, ImageId=?, IsVisibleToParticipants=?, IsApproved=?, ApprovedByPersonId=?, ApprovedDate=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Friends, $this->Enemies,
            $this->Description, $this->DescriptionForOthers, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthId, $this->PlaceOfResidenceId, 
            $this->GroupTypeId, $this->ShipTypeId, $this->Colour, $this->PersonId, 
            $this->CampaignId, $this->IsDead, $this->OrganizerNotes, $this->ImageId, $this->IsVisibleToParticipants, $this->IsApproved, $this->ApprovedByPersonId, $this->ApprovedDate, $this->Id))) {
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
                         WealthId, PlaceOfResidenceId, GroupTypeId, ShipTypeId, Colour, PersonId, CampaignId, 
                         IsDead, OrganizerNotes, ImageId, IsVisibleToParticipants, IsApproved, ApprovedByPersonId, ApprovedDate) 
                         VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name,  
            $this->Friends, $this->Description, $this->DescriptionForOthers, $this->Enemies, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthId, 
            $this->PlaceOfResidenceId, $this->GroupTypeId, $this->ShipTypeId, $this->Colour, $this->PersonId, $this->CampaignId, 
            $this->IsDead, $this->OrganizerNotes, $this->ImageId, $this->IsVisibleToParticipants, $this->IsApproved, $this->ApprovedByPersonId, $this->ApprovedDate))) {
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
    
     public function getGroupType() {
         if (is_null($this->GroupTypeId)) return null;
         return GroupType::loadById($this->GroupTypeId);
     }
     
     public function getShipType() {
         if (is_null($this->ShipTypeId)) return null;
         return ShipType::loadById($this->ShipTypeId);
     }
         
     public function hasImage() {
         if (isset($this->ImageId)) return true;
         return false;
     }
     
     public function getImage() {
         if (empty($this->ImageId)) return null;
         return Image::loadById($this->ImageId);
     }
     
     public function isApproved() {
         if ($this->IsApproved == 1) return true;
         return false;
     }

     public function isVisibleToParticipants() {
         if ($this->IsVisibleToParticipants == 1) return true;
         return false;
     }
     
     public function getOldApprovedGroup() {
         return GroupApprovedCopy::getOldGroup($this->Id);
     }
     
     
     public function is_trading(LARP $larp) {
         $campaign = $larp->getCampaign();
         if (!$campaign->is_dmh()) return false;
         if ($this->WealthId > 3) return true;
         $larp_group = LARP_Group::loadByIds($this->Id, $larp->Id);
         
         //Äger verksamhet
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
         if (GroupType::isInUse($larp))
             $sql = "SELECT DISTINCT regsys_group.* FROM regsys_group, regsys_grouptype WHERE (GroupTypeId IS NULL OR GroupTypeId = regsys_grouptype.Id) AND IsDead=0 AND regsys_group.Id IN ".
                 "(SELECT GroupId from regsys_larp_group where LARPId = ?) ORDER BY regsys_grouptype.SortOrder,".static::$orderListBy.";";
         else 
             $sql = "SELECT * FROM regsys_group WHERE IsDead=0 AND regsys_group.Id IN ".
             "(SELECT GroupId from regsys_larp_group where LARPId = ?) ORDER BY ".static::$orderListBy.";";
   
         return static::getSeveralObjectsqQuery($sql, array($larp->Id));
     }
     
     public static function getAllRegisteredApproved($larp) {
         if (is_null($larp)) return Array();
         if (GroupType::isInUse($larp))
             $sql = "SELECT DISTINCT regsys_group.* FROM regsys_group, regsys_grouptype WHERE (GroupTypeId IS NULL OR GroupTypeId = regsys_grouptype.Id) AND IsDead=0 AND IsApproved=1 AND regsys_group.Id IN ".
             "(SELECT GroupId from regsys_larp_group where LARPId = ?) ORDER BY regsys_grouptype.SortOrder,".static::$orderListBy.";";
         else
             $sql = "SELECT * FROM regsys_group WHERE IsDead=0 AND IsApproved=1 AND Id IN ".
                 "(SELECT GroupId from regsys_larp_group where LARPId = ?) ORDER BY ".static::$orderListBy.";";
             return static::getSeveralObjectsqQuery($sql, array($larp->Id));
     }
     
     public static function getAllInCampaign($campaignId) {
         $sql = "SELECT * FROM regsys_group WHERE CampaignId=? ".
             "ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($campaignId));
     }
          
     public static function getAllHiddenGroups($campaignId) {
         $sql = "SELECT * FROM regsys_group WHERE CampaignId=? AND IsVisibleToParticipants=0 ".
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
     
     
     public static function getAllRegisteredGroupsForPerson($personId, $larp) {
         
         if (is_null($larp)) return Array();
         $sql = "SELECT * FROM regsys_group WHERE PersonId = ? ". 
            "AND CampaignId = ? AND regsys_group.Id IN ".
            "(SELECT GroupId from regsys_larp_group where LARPId = ?) ORDER BY ".static::$orderListBy.";";
          return static::getSeveralObjectsqQuery($sql, array($personId, $larp->CampaignId, $larp->Id));
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
     
     public function saveAllIntrigueTypes($idArr) {
         if (!isset($idArr)) {
             return;
         }
         foreach($idArr as $Id) {
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
     
     public function getPreviousLarps() {
         return LARP::getPreviousLarpsGroup($this->Id);
     }
     
     public function getAllKnownRoles(LARP $larp) {
         return Role::getAllKnownRolesForGroup($this, $larp);
     }

     public function countAllRolesInGroup(LARP $larp) {
        $mainRoles = Role::getAllMainRolesInGroup($this, $larp);
        $nonMainRoles = Role::getAllNonMainRolesInGroup($this, $larp);

        $totalRoles = count($mainRoles) + count($nonMainRoles);
        return $totalRoles;
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
     
     public static function getAllKnownGroupsForSubdivision(Subdivision $subdivision, LARP $larp) {
         $sql = "SELECT * FROM regsys_group WHERE Id IN (".
             "SELECT iak.GroupId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
             "ias.SubdivisionId = ? AND ".
             "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
             "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
             "ias.IntrigueId = regsys_intrigue.Id AND ".
             "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($subdivision->Id, $larp->Id));
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
     
     public function hasRegisteredWhatHappened(LARP $larp) {
         $larp_group = LARP_Group::loadByIds($this->Id, $larp->Id);
         if (!empty($larp_group->WhatHappened) OR !empty($larp_group->WhatHappendToOthers)) return true;
         
         $intrigues = Intrigue::getAllIntriguesForGroup($this->Id, $larp->Id);
         foreach ($intrigues as $intrigue) {
             $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $this);
             if (!empty($intrigueActor->WhatHappened)) return true;
         }
         return false;
     }
     
     public function approve($larp, $person) {
         $oldCopy = $this->getOldApprovedGroup();
         if (isset($oldCopy)) GroupApprovedCopy::delete($oldCopy->Id);
         
         $this->IsApproved = 1;
         $this->ApprovedByPersonId = $person->Id;
         $now = new Datetime();
         $this->ApprovedDate = date_format($now,"Y-m-d H:i:s");
         $this->update();
         
         BerghemMailer::send_group_approval_mail($this, $larp, $person->Id);
     }
     
     public function unapprove($larp, $sendMail, $person) {
         $this->IsApproved = 0;
         $this->ApprovedByPersonId = null;
         $this->ApprovedDate = null;
         $this->update();
         
         $senderId = NULL;
         if (isset($person)) $senderId = $person->Id;
         
         if ($sendMail) BerghemMailer::send_group_unapproval_mail($this, $larp, $senderId);
     }
     
    public function getViewLink() {
        $vgroup = "<a href='view_group.php?id=$this->Id'>$this->Name</a>";

        if ($this->IsDead) {
            $vgroup .= " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
        }

        return $vgroup;
    }

    public function getLink() {
        return "view_group.php?id=$this->Id";
    }
    
    
    public function getEditLinkPen($isAdmin) {
        if($isAdmin) {
            return "<a href='edit_group.php?id=" . $this->Id . "'><i class='fa-solid fa-pen' title='Redigera gruppen'></i></a>";
        }
        else {
            return "<a href='group_form.php?operation=update&id=$this->Id'><i class='fa-solid fa-pen'></i></a>";
        }
    }
}