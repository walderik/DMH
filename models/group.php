<?php

class Group extends BaseModel{
    
    public $Id;
    public $Name;
    public $Friends;
    public $Enemies;
    public $Description;
    public $IntrigueIdeas;
    public $OtherInformation;
    public $WealthId;
    public $PlaceOfResidenceId;
    public $PersonId; # Gruppansvarig
    public $CampaignId;
    public $IsDead = 0;
    
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
        if (isset($arr['IntrigueIdeas'])) $this->IntrigueIdeas = $arr['IntrigueIdeas'];
        if (isset($arr['OtherInformation'])) $this->OtherInformation = $arr['OtherInformation'];
        if (isset($arr['WealthId'])) $this->WealthId = $arr['WealthId'];
        if (isset($arr['PlaceOfResidenceId'])) $this->PlaceOfResidenceId = $arr['PlaceOfResidenceId'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['IsDead'])) $this->IsDead = $arr['IsDead'];
        
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $newOne = new self();
        $newOne->CampaignId = $current_larp->CampaignId;
        return $newOne;
    }
    
    public static function getRegistered($larp) {
        global $tbl_prefix;
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM `".$tbl_prefix."group` WHERE Id IN (SELECT GroupId from ".$tbl_prefix."larp_group where LARPId = ?);";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
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
    
    # Update an existing group in db
    public function update() {
        global $tbl_prefix;
        
        $stmt = $this->connect()->prepare("UPDATE `".$tbl_prefix."group` SET Name=?, Friends=?, Enemies=?,
                                                                  Description=?, IntrigueIdeas=?, OtherInformation=?,
                                                                  WealthId=?, PlaceOfResidenceId=?, PersonId=?, CampaignId=?, IsDead=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Friends, $this->Enemies,
            $this->Description, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthId, $this->PlaceOfResidenceId, $this->PersonId, 
            $this->CampaignId, $this->IsDead, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    
    # Create a new group in db
    public function create() {
        global $tbl_prefix;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO `".$tbl_prefix."group` (Name,  
                         Friends, Description, Enemies, IntrigueIdeas, OtherInformation, 
                         WealthId, PlaceOfResidenceId, PersonId, CampaignId, IsDead) 
                         VALUES (?,?,?,?,?, ?,?,?,?,?, ?);");
        
        if (!$stmt->execute(array($this->Name,  
            $this->Friends, $this->Description, $this->Enemies, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthId, 
            $this->PlaceOfResidenceId, $this->PersonId, $this->CampaignId, $this->IsDead))) {
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
    
    public function getPlaceOfResidence() {
        if (is_null($this->PlaceOfResidenceId)) return null;
        return PlaceOfResidence::loadById($this->PlaceOfResidenceId);
    }
    
     public function getPerson() {
         return Person::loadById($this->PersonId);
     }

     public function getCampaign() {
         return Campaign::loadById($this->CampaignId);
     }
     
     public function isRegistered($larp) {
         return LARP_Group::isRegistered($this->Id, $larp->Id);

     }
     
     public function hasIntrigue(LARP $larp) {
         $larp_group = LARP_Group::loadByIds($this->Id, $larp->Id);
         if (isset($larp_group->Intrigue) && $larp_group->Intrigue != "") return true;
         return false;
         
     }
     
     public static function getAllRegistered(LARP $larp) {
         global $tbl_prefix;
         if (is_null($larp)) return Array();
         $sql = "SELECT * FROM `".$tbl_prefix."group` WHERE Id IN (SELECT GroupId FROM regsys_larp_group WHERE larpId =?) ORDER BY ".static::$orderListBy.";";
         $stmt = static::connectStatic()->prepare($sql);
         
         if (!$stmt->execute(array($larp->Id))) {
             $stmt = null;
             header("location: ../participant/index.php?error=stmtfailed");
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
     
     public static function getGroupsForPerson($personId) {
         global $tbl_prefix;
         $sql = "SELECT * FROM `".$tbl_prefix."group` WHERE PersonId = ? ORDER BY ".static::$orderListBy.";";
         $stmt = static::connectStatic()->prepare($sql);
         
         if (!$stmt->execute(array($personId))) {
             $stmt = null;
             header("location: ../participant/index.php?error=stmtfailed");
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
     
     public function isNeverRegistered() {
         global $tbl_prefix;
         
         
         
         $sql = "SELECT COUNT(*) AS Num FROM `".$tbl_prefix."larp_group` WHERE GroupId=?;";
         
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
     
     
    
}