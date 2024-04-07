<?php

class Intrigue_Vision extends BaseModel{
    
    public $Id;
    public $IntrigueId;
    public $VisionId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $house = static::newWithDefault();
        $house->setValuesByArray($post);
        return $house;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        if (isset($arr['VisionId'])) $this->VisionId = $arr['VisionId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigue_vision SET IntrigueId=?, VisionId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->VisionId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigue_vision (IntrigueId, VisionId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->VisionId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getIntrigue() {
        return Intrigue::loadById($this->IntrigueId);
    }
    
    public function getVision() {
        return Vision::loadById($this->VisionId);
    }
    
    public function getAllIntrigues() {
        return Intrigue::getAllIntriguesForIntrigueVision($this);
    }
    
    public static function getAllIntrigueVisionsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigue_vision WHERE IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    
}
