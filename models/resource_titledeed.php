<?php

class Resource_Titledeed extends BaseModel{
    
    public $Id;
    public $ResourceId;
    public $TitledeedId;
    public $Quantity = 0;
    public $QuantityForUpgrade = 0;
    
    public static $orderListBy = 'ResourceId';
    
    
    public static function newFromArray($post){
        $newOne = static::newWithDefault();
        $newOne->setValuesByArray($post);
        return $newOne;
    }
    
    public function setValuesByArray($arr) {
        
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['ResourceId'])) $this->ResourceId = $arr['ResourceId'];
        if (isset($arr['TitledeedId'])) $this->TitledeedId = $arr['TitledeedId'];
        if (isset($arr['Quantity'])) $this->Quantity = $arr['Quantity'];
        if (isset($arr['QuantityForUpgrade'])) $this->QuantityForUpgrade = $arr['QuantityForUpgrade'];
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        $newOne = new self();
        return $newOne;
    }
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_resource_titledeed SET Quantity=?, 
            QuantityForUpgrade=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Quantity, $this->QuantityForUpgrade, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_resource_titledeed (ResourceId, TitledeedId, 
            Quantity, QuantityForUpgrade) VALUES (?,?,?,?);");
        
        if (!$stmt->execute(array($this->ResourceId, $this->TitledeedId, $this->Quantity, 
            $this->QuantityForUpgrade))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public static function allForTitledeed(Titledeed $titledeed) {
        $sql = "SELECT * FROM regsys_resource_titledeed WHERE TitledeedId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
    }
    
    public function getResource() {
        return Resource::loadById($this->ResourceId);
    }
    
    public static function getResourceAmountForPlace($resourceId, $placeId) {
        $sql = "SELECT sum(Quantity) as Num FROM regsys_resource_titledeed WHERE ResourceId = ? AND TitledeedId IN (".
            "SELECT Id FROM regsys_titledeed WHERE TitledeedPlaceId=?)";
        return static::countQuery($sql, array($resourceId, $placeId));
    }
    
    public static function loadByIds($resourceId, $titledeedId) {
        $sql = "SELECT * FROM regsys_resource_titledeed WHERE ResourceId = ? AND TitledeedId = ?";
        return static::getOneObjectQuery($sql, array($resourceId, $titledeedId));
    }
    
    
    public static function TitleDeedProcuces(Titledeed $titledeed) {
        if (is_null($titledeed)) return Array();
        $sql = "SELECT * FROM regsys_resource_titledeed WHERE TitledeedId = ? AND Quantity > 0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
        
    }
    
    public static function TitleDeedRequires(Titledeed $titledeed) {
        if (is_null($titledeed)) return Array();
        $sql = "SELECT * FROM regsys_resource_titledeed WHERE TitledeedId = ? AND Quantity < 0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
        
    }

    public static function TitleDeedRequiresForUpgrade(Titledeed $titledeed) {
        if (is_null($titledeed)) return Array();
        $sql = "SELECT * FROM regsys_resource_titledeed WHERE TitledeedId = ? AND QuantityForUpgrade > 0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($titledeed->Id));
        
    }
    
}