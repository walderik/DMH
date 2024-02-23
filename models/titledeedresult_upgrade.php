<?php

class Titledeedresult_Upgrade extends BaseModel{
    
    public $Id;
    public $TitledeedResultId;
    public $ResourceId;
    public $QuantityForUpgrade;
    public $NeedsMet = 0;


    public static $orderListBy = 'RoleId';
    
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['TitledeedResultId'])) $this->TitledeedResultId = $arr['TitledeedResultId'];
        if (isset($arr['ResourceId'])) $this->ResourceId = $arr['ResourceId'];
        if (isset($arr['QuantityForUpgrade'])) $this->QuantityForUpgrade = $arr['QuantityForUpgrade'];
        if (isset($arr['NeedsMet'])) $this->NeedsMet = $arr['NeedsMet'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_titledeedresult_upgrade SET NeedsMet=? WHERE Id=?;");
        
        if (!$stmt->execute(array($this->NeedsMet, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;    
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_titledeedresult_upgrade (TitledeedResultId, ResourceId, QuantityForUpgrade, 
            NeedsMet) VALUES (?,?,?,?);");
        
        if (!$stmt->execute(array($this->TitledeedResultId, $this->ResourceId, $this->QuantityForUpgrade, $this->NeedsMet))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public static function getAllUpgradeResults(TitledeedResult $titledeedresult) {
        $sql = "SELECT * FROM regsys_titledeedresult_upgrade WHERE TitledeedResultId = ?;";
        return static::getSeveralObjectsqQuery($sql, array($titledeedresult->Id));
    }
    
    public static function createMoneyUpgradeResult($titledeedresultId, $amount, $isMet) {
        $upgradeResult = Titledeedresult_Upgrade::newWithDefault();
        $upgradeResult->TitledeedResultId = $titledeedresultId;
        $upgradeResult->ResourceId = null;
        $upgradeResult->QuantityForUpgrade = $amount;
        $upgradeResult->NeedsMet = $isMet;
        $upgradeResult->create();
    }
    
    public static function createUpgradeResult($titledeedresultId, $resouceId, $amount, $isMet) {
        $upgradeResult = Titledeedresult_Upgrade::newWithDefault();
        $upgradeResult->TitledeedResultId = $titledeedresultId;
        $upgradeResult->ResourceId = $resouceId;
        $upgradeResult->QuantityForUpgrade = $amount;
        $upgradeResult->NeedsMet = $isMet;
        $upgradeResult->create();
    }
    
    
   
}