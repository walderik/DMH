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
        
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $newOne = new self();
        $newOne->CampaignId = $current_larp->CampaignId;
        return $newOne;
    }
    
    public static function getRegistered($larp) {

        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_group WHERE IsDead=0 AND Id IN ".
            "(SELECT GroupId from regsys_larp_group where LARPId = ?);";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    # Update an existing group in db
    public function update() {
       
        $stmt = $this->connect()->prepare("UPDATE regsys_group SET Name=?, Friends=?, Enemies=?,
                                                                  Description=?, DescriptionForOthers=?, IntrigueIdeas=?, OtherInformation=?,
                                                                  WealthId=?, PlaceOfResidenceId=?, PersonId=?, CampaignId=?, IsDead=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Friends, $this->Enemies,
            $this->Description, $this->DescriptionForOthers, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthId, $this->PlaceOfResidenceId, $this->PersonId, 
            $this->CampaignId, $this->IsDead, $this->Id))) {
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
                         WealthId, PlaceOfResidenceId, PersonId, CampaignId, IsDead) 
                         VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?);");
        
        if (!$stmt->execute(array($this->Name,  
            $this->Friends, $this->Description, $this->DescriptionForOthers, $this->Enemies, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthId, 
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
         if (is_null($larp)) return Array();
         $sql = "SELECT * FROM regsys_group WHERE Id IN ".
            "(SELECT GroupId FROM regsys_larp_group WHERE larpId =?) ".
            "ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($larp->Id));
     }
     
     public static function getGroupsForPerson($personId) {
         $sql = "SELECT * FROM regsys_group WHERE PersonId = ? ORDER BY ".static::$orderListBy.";";
         return static::getSeveralObjectsqQuery($sql, array($personId));
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
     
     
    
}