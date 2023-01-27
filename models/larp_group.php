<?php

class LARP_Group extends BaseModel{
    
    public $Id;
    public $GroupId;
    public $LARPId;
    public $WantIntrigue = true;
    public $Intrigue;
    public $HousingRequestId;

    public static $orderListBy = 'GroupId';
    
    public static function newFromArray($post){
        $larp_group = static::newWithDefault();
        if (isset($post['Id']))   $larp_group->Id = $post['Id'];
        if (isset($post['GroupId'])) $larp_group->GroupId = $post['GroupId'];
        if (isset($post['LARPId'])) $larp_group->LARPId = $post['LARPId'];
        if (isset($post['WantIntrigue'])) $larp_group->WantIntrigue = $post['WantIntrigue'];
        if (isset($post['Intrigue'])) $larp_group->Intrigue = $post['Intrigue'];
        if (isset($post['HousingRequestId'])) $larp_group->HousingRequestId = $post['HousingRequestId'];      
        return $larp_group;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE `larp_group` SET GroupId=?, LARPId=?, WantIntrigue=?, Intrigue=?, HousingRequestId=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->GroupId, $this->LARPId, $this->WantIntrigue, $this->Intrigue, $this->HousingRequestId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;    
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO `larp_group` (GroupId, LARPId, WantIntrigue, Intrigue, HousingRequestId) VALUES (?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->GroupId, $this->LARPId, $this->WantIntrigue, $this->Intrigue, $this->HousingRequestId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    # returnera en array med alla grupper som är anmälda till lajvet
    public static function getRegistered($larpId) {
        if (is_null($larpId)) return Array();
        $sql = "SELECT * FROM `larp_group` WHERE LARPId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larpId))) {
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



}