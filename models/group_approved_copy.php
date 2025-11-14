<?php

class GroupApprovedCopy extends BaseModel{
    
    public $Id;
    public $GroupId;
    public $Name;
    public $Friends;
    public $Enemies;
    public $Description;
    public $DescriptionForOthers;
    public $OtherInformation;
    public $WealthId;
    public $PlaceOfResidenceId;
    public $GroupTypeId;
    public $ShipTypeId;
    public $Colour;
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
        if (isset($arr['GroupId'])) $this->GroupId = $arr['GroupId'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Friends'])) $this->Friends = $arr['Friends'];
        if (isset($arr['Enemies'])) $this->Enemies = $arr['Enemies'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['DescriptionForOthers'])) $this->DescriptionForOthers = $arr['DescriptionForOthers'];
        if (isset($arr['OtherInformation'])) $this->OtherInformation = $arr['OtherInformation'];
        if (isset($arr['WealthId'])) $this->WealthId = $arr['WealthId'];
        if (isset($arr['PlaceOfResidenceId'])) $this->PlaceOfResidenceId = $arr['PlaceOfResidenceId'];
        if (isset($arr['GroupTypeId'])) $this->GroupTypeId = $arr['GroupTypeId'];
        if (isset($arr['ShipTypeId'])) $this->ShipTypeId = $arr['ShipTypeId'];
        if (isset($arr['Colour'])) $this->Colour = $arr['Colour'];
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
    
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_groupapprovedcopy (GroupId, Name,  
                         Friends, Description, DescriptionForOthers, Enemies, OtherInformation, 
                         WealthId, PlaceOfResidenceId, GroupTypeId, ShipTypeId, Colour, ApprovedByPersonId, ApprovedDate) 
                         VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?);");
        
        if (!$stmt->execute(array($this->GroupId, $this->Name,  
            $this->Friends, $this->Description, $this->DescriptionForOthers, $this->Enemies, $this->OtherInformation, $this->WealthId, 
            $this->PlaceOfResidenceId, $this->GroupTypeId, $this->ShipTypeId, $this->Colour, $this->ApprovedByPersonId, $this->ApprovedDate))) {
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
         
      public function getPlaceOfResidence() {
        if (is_null($this->PlaceOfResidenceId)) return null;
        return PlaceOfResidence::loadById($this->PlaceOfResidenceId);
     }
    
     
     public static function makeCopyOfApprovedGroup(Group $group) {
         $sql = "SELECT * FROM regsys_group WHERE Id = ?";
         $groupCopy =  GroupApprovedCopy::getOneObjectQuery($sql, array($group->Id));
         $groupCopy->GroupId = $group->Id;
         $groupCopy->create();
     }
     
     public static function getOldGroup($groupId) {
         $sql = "SELECT * FROM regsys_groupapprovedcopy WHERE GroupId =?";
         return static::getOneObjectQuery($sql, array($groupId));
     }
     
     public static function delete($id) {
         $groupCopy = GroupApprovedCopy::loadById($id);
         parent::delete($id);
     }
     
     
 }