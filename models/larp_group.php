<?php

class LARP_Group extends BaseModel{
    
    public $Id;
    public $GroupId;
    public $LARPId;
    public $WantIntrigue = true;
    public $Intrigue;
    public $ApproximateNumberOfMembers;
    public $HousingRequestId;
    public $NeedFireplace = 0;
    public $RemainingIntrigues;

    public static $orderListBy = 'GroupId';
    
    public static function newFromArray($post){
        $larp_group = static::newWithDefault();
        if (isset($post['Id']))   $larp_group->Id = $post['Id'];
        if (isset($post['GroupId'])) $larp_group->GroupId = $post['GroupId'];
        if (isset($post['LARPId'])) $larp_group->LARPId = $post['LARPId'];
        if (isset($post['NeedFireplace'])) $larp_group->NeedFireplace = $post['NeedFireplace'];
        if (isset($post['WantIntrigue'])) $larp_group->WantIntrigue = $post['WantIntrigue'];
        if (isset($post['Intrigue'])) $larp_group->Intrigue = $post['Intrigue'];
        if (isset($post['HousingRequestId'])) $larp_group->HousingRequestId = $post['HousingRequestId'];      
        if (isset($post['ApproximateNumberOfMembers'])) $larp_group->ApproximateNumberOfMembers = $post['ApproximateNumberOfMembers'];
        if (isset($post['RemainingIntrigues'])) $larp_group->RemainingIntrigues = $post['RemainingIntrigues'];          return $larp_group;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public static function isRegistered($groupId, $larpId) {
        global $tbl_prefix;
        $sql = "SELECT * FROM `".$tbl_prefix."larp_group` WHERE GroupId = ? AND LARPId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($groupId, $larpId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            return false;
        }
        
        return true;
    }
    
    public static function loadByIds($groupId, $larpId)
    {
        global $tbl_prefix;
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $stmt = static::connectStatic()->prepare("SELECT * FROM `".$tbl_prefix."larp_group` WHERE GroupId = ? AND LARPId = ?");
        
        if (!$stmt->execute(array($groupId, $larpId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return null;
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = $rows[0];
        $stmt = null;
        
        return static::newFromArray($row);
    }
    
        
    # Update an existing object in db
    public function update() {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("UPDATE `".$tbl_prefix."larp_group` SET GroupId=?, LARPId=?, WantIntrigue=?, Intrigue=?, HousingRequestId=?, RemainingIntrigues=? , ApproximateNumberOfMembers=?, NeedFireplace=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->GroupId, $this->LARPId, $this->WantIntrigue, $this->Intrigue, $this->HousingRequestId, $this->RemainingIntrigues, $this->ApproximateNumberOfMembers, $this->NeedFireplace, $this->Id))) {
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
        $stmt = $connection->prepare("INSERT INTO `".$tbl_prefix."larp_group` (GroupId, LARPId, WantIntrigue, Intrigue, HousingRequestId, RemainingIntrigues, ApproximateNumberOfMembers, NeedFireplace) VALUES (?,?,?,?,?, ?,?,?);");
        
        if (!$stmt->execute(array($this->GroupId, $this->LARPId, $this->WantIntrigue, $this->Intrigue, $this->HousingRequestId, $this->RemainingIntrigues, $this->ApproximateNumberOfMembers, $this->NeedFireplace))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    

    public function saveAllIntrigueTypes($post) {
        global $tbl_prefix;
        if (!isset($post['IntrigueTypeId'])) {
            return;
        }
        foreach($post['IntrigueTypeId'] as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO ".$tbl_prefix."IntrigueType_LARP_Group (IntrigueTypeId, LARP_GroupGroupId, LARP_GroupLARPId) VALUES (?,?, ?);");
            if (!$stmt->execute(array($Id, $this->GroupId, $this->LARPId))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllIntrigueTypes() {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("DELETE FROM ".$tbl_prefix."IntrigueType_LARP_Group WHERE LARP_GroupGroupId = ? AND LARP_GroupLARPId = ?;");
        if (!$stmt->execute(array($this->GroupId, $this->LARPId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }

    public function getSelectedIntrigueTypeIds() {
        global $tbl_prefix;
       
        $stmt = $this->connect()->prepare("SELECT IntrigueTypeId FROM  `".$tbl_prefix."intriguetype_larp_group` WHERE LARP_GroupGroupId = ? AND LARP_GroupLARPId = ? ORDER BY IntrigueTypeId;");
        
        if (!$stmt->execute(array($this->GroupId, $this->LARPId))) {
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
    
    public function getIntrigueTypes(){
        return IntrigueType::getIntrigeTypesForLarpAndGroup($this->LARPId, $this->GroupId);
    }
}