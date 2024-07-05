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
    
    
    public function isVisibleToParticipants() {
        if ($this->IsVisibleToParticipants==1) return true;
        return false;
    }
    
    public function canSeeOtherParticipants() {
        if ($this->CanSeeOtherParticipants==1) return true;
        return false;
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
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_subdivision WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
}