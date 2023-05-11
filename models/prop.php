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
    
    
    
}

