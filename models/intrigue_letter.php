<?php

class Intrigue_Letter extends BaseModel{
    
    public $Id;
    public $IntrigueId;
    public $LetterId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $house = static::newWithDefault();
        $house->setValuesByArray($post);
        return $house;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        if (isset($arr['LetterId'])) $this->LetterId = $arr['LetterId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigue_letter SET IntrigueId=?, LetterId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->LetterId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigue_letter (IntrigueId, LetterId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->LetterId))) {
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
    
    public function getLetter() {
        return Letter::loadById($this->LetterId);
    }
    
    public static function delete($id)
    {
        //TODO ta bort alla länkar
        parent::delete($id);
    }
    
    public static function getAllLettersForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigue_letter WHERE IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    
}
