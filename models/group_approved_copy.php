<?php

class GroupApprovedCopy extends BaseModel{
    
    public $Id;
    public $GroupId;
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
    public $ApprovedByUserId;
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
        if (isset($arr['IntrigueIdeas'])) $this->IntrigueIdeas = $arr['IntrigueIdeas'];
        if (isset($arr['OtherInformation'])) $this->OtherInformation = $arr['OtherInformation'];
        if (isset($arr['WealthId'])) $this->WealthId = $arr['WealthId'];
        if (isset($arr['PlaceOfResidenceId'])) $this->PlaceOfResidenceId = $arr['PlaceOfResidenceId'];
        if (isset($arr['GroupTypeId'])) $this->GroupTypeId = $arr['GroupTypeId'];
        if (isset($arr['ShipTypeId'])) $this->ShipTypeId = $arr['ShipTypeId'];
        if (isset($arr['Colour'])) $this->Colour = $arr['Colour'];
        if (isset($arr['ApprovedByUserId'])) $this->ApprovedByUserId = $arr['ApprovedByUserId'];
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
                         Friends, Description, DescriptionForOthers, Enemies, IntrigueIdeas, OtherInformation, 
                         WealthId, PlaceOfResidenceId, GroupTypeId, ShipTypeId, Colour, ApprovedByUserId, ApprovedDate) 
                         VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->GroupId, $this->Name,  
            $this->Friends, $this->Description, $this->DescriptionForOthers, $this->Enemies, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthId, 
            $this->PlaceOfResidenceId, $this->GroupTypeId, $this->ShipTypeId, $this->Colour, $this->ApprovedByUserId, $this->ApprovedDate))) {
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
         
     public function getIntrigueTypes(){
         return IntrigueType::getIntrigeTypesForApprovedGroupCopy($this->Id);
     }
     
     
     public function getPlaceOfResidence() {
        if (is_null($this->PlaceOfResidenceId)) return null;
        return PlaceOfResidence::loadById($this->PlaceOfResidenceId);
     }
    
     
     public function saveAllIntrigueTypes($idArr) {
         if (!isset($idArr)) {
             return;
         }
         foreach($idArr as $Id) {
             $stmt = $this->connect()->prepare("INSERT INTO regsys_intriguetype_groupapprovedcopy (IntrigueTypeId, GroupId) VALUES (?,?);");
             if (!$stmt->execute(array($Id, $this->Id))) {
                 $stmt = null;
                 header("location: ../participant/index.php?error=stmtfailed");
                 exit();
             }
         }
         $stmt = null;
     }
     
     public function deleteAllIntrigueTypes() {
         $stmt = $this->connect()->prepare("DELETE FROM regsys_intriguetype_groupapprovedcopy WHERE GroupId = ?;");
         if (!$stmt->execute(array($this->Id))) {
             $stmt = null;
             header("location: ../participant/index.php?error=stmtfailed");
             exit();
         }
         $stmt = null;
     }
     

      
     public static function makeCopyOfApprovedGroup(Group $group) {
         $sql = "SELECT * FROM regsys_group WHERE Id = ?";
         $groupCopy =  GroupApprovedCopy::getOneObjectQuery($sql, array($group->Id));
         $groupCopy->GroupId = $group->Id;
         $groupCopy->create();
         

         $groupCopy->saveAllIntrigueTypes($group->getSelectedIntrigueTypeIds());
     }
     
     public static function getOldGroup($groupId) {
         $sql = "SELECT * FROM regsys_groupapprovedcopy WHERE GroupId =?";
         return static::getOneObjectQuery($sql, array($groupId));
     }
     
     public static function delete($id) {
         $groupCopy = GroupApprovedCopy::loadById($id);
         $groupCopy->deleteAllIntrigueTypes();
         parent::delete($id);
     }
     
     
 }