<?php

class Prop extends BaseModel{
    
    public $Id;
    public $Name;
    public $Description;
    public $StorageLocation;
    public $Image = 0;
    public $GroupId = null;
    public $RoleId = null;
    public $CampaignId;
    
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
        if (isset($arr['GroupId'])) $this->GroupId = $arr['GroupId'];
        if (isset($arr['RoleId'])) $this->RoleId = $arr['RoleId'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];

        if (isset($this->GroupId) && $this->GroupId=='null') $this->GroupId = null;
        if (isset($this->RoleId) && $this->RoleId=='null') $this->RoleId = null;
        
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
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("UPDATE `".$tbl_prefix."prop` SET Name=?, 
            Description=?, StorageLocation=?, GroupId=?, RoleId=?,
            CampaignId=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->StorageLocation, 
            $this->GroupId, $this->RoleId, $this->CampaignId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        global $tbl_prefix;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO `".$tbl_prefix."prop` (Name, Description, 
            StorageLocation, GroupId, RoleId, CampaignId) VALUES (?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->StorageLocation, 
            $this->GroupId, $this->RoleId, $this->CampaignId))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public static function allByCampaign() {
        global $tbl_prefix, $current_larp;
        
        $sql = "SELECT * FROM ".$tbl_prefix.strtolower(static::class)." WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($current_larp->CampaignId))) {
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
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }
    
    
    
}

