<?php

class Subdivision extends BaseModel{
    
    public $Id;
    public $Name;
    public $Description;
    public $OrganizerNotes;
    public $CampaignId;
    public $IsVisibleToParticipants = 0;
    public $CanSeeOtherParticipants = 0;
    
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['IsVisibleToParticipants'])) $this->IsVisibleToParticipants = $arr['IsVisibleToParticipants'];
        if (isset($arr['CanSeeOtherParticipants'])) $this->CanSeeOtherParticipants = $arr['CanSeeOtherParticipants'];
    }
    
        
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $object = new self();
        $object->CampaignId = $current_larp->CampaignId;
        return $object;
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_subdivision SET Name=?, Description=?, OrganizerNotes=?, IsVisibleToParticipants=?, CanSeeOtherParticipants=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->OrganizerNotes, $this->IsVisibleToParticipants, $this->CanSeeOtherParticipants, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_subdivision (Name, Description, OrganizerNotes, IsVisibleToParticipants, CanSeeOtherParticipants, CampaignId) VALUES (?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->OrganizerNotes, $this->IsVisibleToParticipants, $this->CanSeeOtherParticipants, $this->CampaignId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    public function hasImage() {
        return false;
    }
    
    public function isVisibleToParticipants() {
        if ($this->IsVisibleToParticipants==1) return true;
        return false;
    }
    
    public function canSeeOtherParticipants() {
        if ($this->CanSeeOtherParticipants==1) return true;
        return false;
    }
    
    public function getViewLink() {
        return "<a href='view_subdivision.php?id=$this->Id'>$this->Name</a>";
    }
    
    public function getEditLinkPen() {
        return "<a href='subdivision_form.php?operation=update&id=$this->Id'><i class='fa-solid fa-pen'></i></a>";
    }
    
    public function addMembers($roleIds) {
        //Ta reda på vilka som inte redan är kopplade till skolan
        $exisitingIds = array();
        $members = $this->getAllMembers();
        foreach ($members as $member) {
            $exisitingIds[] = $member->Id;
        }
        
        $newRoleIds = array_diff($roleIds,$exisitingIds);
        //Koppla magier till skolan
        foreach ($newRoleIds as $roleId) {
            $this->addMember($roleId);
        }
    }
    
    private function addMember($roleId) {
        
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "resys_subdivisionmember (SubdivisionId, RoleId) VALUES (?,?);");
        if (!$stmt->execute(array($this->Id, $roleId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    public function removeMember($roleId) {
        $stmt = $this->connect()->prepare("DELETE FROM resys_subdivisionmember WHERE SubdivisionId=? AND RoleId = ?;");
        if (!$stmt->execute(array($this->Id, $roleId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public function getAllMembers() {
        $sql = "SELECT * FROM regsys_role WHERE Id IN (".
            "SELECT RoleId FROM resys_subdivisionmember WHERE SubdivisionId=?) ORDER BY ".Role::$orderListBy.";";
        return Role::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    
    public function getAllRegisteredMembers(LARP $larp) {
        $sql = "SELECT regsys_role.* FROM regsys_role, regsys_larp_role WHERE Id IN (".
            "SELECT RoleId FROM resys_subdivisionmember WHERE SubdivisionId=?) AND ".
            "regsys_role.Id = regsys_larp_role.RoleId AND ".
            "regsys_larp_role.LarpId = ? ".
            "ORDER BY ".Role::$orderListBy.";";
        return Role::getSeveralObjectsqQuery($sql, array($this->Id, $larp->Id));
    }
    
    public static function allForRole(Role $role) {
        if (is_null($role)) return Array();
        $sql = "SELECT * FROM regsys_subdivision WHERE Id IN (SELECT SubdivisionId FROM  resys_subdivisionmember WHERE RoleId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($role->Id));
    }
    
    public static function allVisibleForRole(Role $role) {
        if (is_null($role)) return Array();
        $sql = "SELECT * FROM regsys_subdivision WHERE IsVisibleToParticipants=1 AND Id IN (SELECT SubdivisionId FROM  resys_subdivisionmember WHERE RoleId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($role->Id));
    }
    
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_subdivision WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
    
    public function getAllKnownRoles(LARP $larp) {
        return Role::getAllKnownRolesForSubdivision($this, $larp);
    }
    
    public function getAllKnownGroups(LARP $larp) {
        return Group::getAllKnownGroupsForSubdivision($this, $larp);
    }
    
    public function getAllKnownNPCGroups(LARP $larp) {
        return IntrigueActor_KnownNPCGroup::getAllKnownNPCGroupsForSubdivision($this, $larp);
    }
    
    public function getAllKnownNPCs(LARP $larp) {
        return IntrigueActor_KnownNPC::getAllKnownNPCsForSubdivision($this, $larp);
    }
    
    public function getAllKnownProps(LARP $larp) {
        return IntrigueActor_KnownProp::getAllKnownPropsForSubdivision($this, $larp);
    }
    
    public function getAllKnownPdfs(LARP $larp) {
        return IntrigueActor_KnownPdf::getAllKnownPdfsForSubdivision($this, $larp);
    }
    
    public function getAllCheckinLetters(LARP $larp) {
        return IntrigueActor_CheckinLetter::getAllCheckinLettersForSubdivision($this, $larp);
    }
    
    public function getAllCheckinTelegrams(LARP $larp) {
        return IntrigueActor_CheckinTelegram::getAllCheckinTelegramsForSubdivision($this, $larp);
    }
    
    public function getAllCheckinProps(LARP $larp) {
        return IntrigueActor_CheckinProp::getAllCheckinPropsForSubdivision($this, $larp);
    }
    
    
    public static function getAllKnownSubdivisionsForRole(Role $role, LARP $larp) {
        $sql = "SELECT * FROM regsys_subdivision WHERE Id IN (".
            "SELECT iak.SubdivisionId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
            "ias.RoleId = ? AND ".
            "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
            "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
            "ias.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($role->Id, $larp->Id));
    }
    
    public static function getAllKnownSubdivisionsForGroup(Group $group, LARP $larp) {
        $sql = "SELECT * FROM regsys_subdivision WHERE Id IN (".
            "SELECT iak.SubdivisionId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
            "ias.GroupId = ? AND ".
            "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
            "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
            "ias.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
    }
    
    
}