<?php

class Prop extends BaseModel{
    
    public $Id;
    public $Name;
    public $Description;
    public $StorageLocation;
    public $ImageId = null;
    public $GroupId = null;
    public $RoleId = null;
    public $CampaignId;
    public $Marking ="";
    public $Properties ="";
    
    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $role = static::newWithDefault();
        $role->setValuesByArray($post);
        return $role;
    }
    
    public function setValuesByArray($arr) {
        
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['StorageLocation'])) $this->StorageLocation = $arr['StorageLocation'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['GroupId'])) $this->GroupId = $arr['GroupId'];
        if (isset($arr['RoleId'])) $this->RoleId = $arr['RoleId'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['Marking'])) $this->Marking = $arr['Marking'];
        if (isset($arr['Properties'])) $this->Properties = $arr['Properties'];
        
        if (isset($this->GroupId) && $this->GroupId=='null') $this->GroupId = null;
        if (isset($this->RoleId) && $this->RoleId=='null') $this->RoleId = null;
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
        $stmt = $this->connect()->prepare("UPDATE regsys_prop SET Name=?, 
            Description=?, StorageLocation=?, ImageId=?, GroupId=?, RoleId=?,
            CampaignId=?, Marking=?, Properties=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->StorageLocation, 
            $this->ImageId, $this->GroupId, $this->RoleId, $this->CampaignId, $this->Marking, $this->Properties, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_prop (Name, Description, 
            StorageLocation, GroupId, RoleId, CampaignId, Marking, Properties) VALUES (?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->StorageLocation, 
            $this->GroupId, $this->RoleId, $this->CampaignId, $this->Marking, $this->Properties))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_prop WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public function hasImage() {
        if (isset($this->ImageId)) return true;
        return false;
    }
    
    public function getImage() {
        if (empty($this->ImageId)) return null;
        return Image::loadById($this->ImageId);
    }
    
    public static function getAllCheckinPropsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_prop WHERE Id IN (".
            "SELECT PropId FROM regsys_intrigueactor_checkinprop, regsys_intrigue_prop WHERE ".
            "regsys_intrigue_prop.Id = regsys_intrigueactor_checkinprop.IntriguePropId AND ".
            "regsys_intrigueactor_checkinprop.IntrigueActorId = ?)  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function getAllKnownPropsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_prop WHERE Id IN (".
            "SELECT PropId FROM regsys_intrigueactor_knownprop, regsys_intrigue_prop WHERE ".
            "regsys_intrigue_prop.Id = regsys_intrigueactor_knownprop.IntriguePropId AND ".
            "regsys_intrigueactor_knownprop.IntrigueActorId = ?)  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function getCheckinPropsForPerson(Person $person, LARP $larp) {
        $sql = "SELECT * FROM regsys_prop WHERE Id IN (".
            "SELECT PropId FROM regsys_intrigueactor_checkinprop, regsys_intrigue_prop, regsys_intrigueactor, regsys_intrigue, regsys_role WHERE ".
            "regsys_intrigue_prop.Id = regsys_intrigueactor_checkinprop.IntriguePropId AND ".
            "regsys_intrigueactor_checkinprop.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.RoleId = regsys_role.Id AND ".
            "regsys_role.PersonId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?".
            ")  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id, $larp->Id));
        
    }


    public static function getCheckinPropsForGroup(Group $group, LARP $larp) {
        $sql = "SELECT * FROM regsys_prop WHERE Id IN (".
            "SELECT PropId FROM regsys_intrigueactor_checkinprop, regsys_intrigue_prop, regsys_intrigueactor, regsys_intrigue WHERE ".
            "regsys_intrigue_prop.Id = regsys_intrigueactor_checkinprop.IntriguePropId AND ".
            "regsys_intrigueactor_checkinprop.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.GroupId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?".
            ")  ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
        
    }
    

}

