<?php

class Intrigue_Prop extends BaseModel{
    
    public $Id;
    public $IntrigueId;
    public $PropId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $house = static::newWithDefault();
        $house->setValuesByArray($post);
        return $house;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        if (isset($arr['PropId'])) $this->PropId = $arr['PropId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigue_prop SET IntrigueId=?, PropId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->PropId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigue_prop (IntrigueId, PropId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->PropId))) {
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
    
    public function getProp() {
        return Prop::loadById($this->PropId);
    }
    
    public static function delete($id)
    {
        $intrigueProp = static::loadById($id);
        $checkin_props = $intrigueProp->getAllCheckinProps();
        foreach ($checkin_props as $checkin_prop) IntrigueActor_CheckinProp::delete($checkin_prop->Id);
        
        $known_props = $intrigueProp->getAllKnownProps();
        foreach ($known_props as $known_prop) IntrigueActor_KnownProp::delete($known_prop->Id);

        parent::delete($id);
    }
    
    public static function getAllPropsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigue_prop WHERE IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    public function getAllCheckinProps() {
        return IntrigueActor_CheckinProp::getAllCheckinPropsForIntrigueProp($this);
    }
    
    public function getAllKnownProps() {
        return IntrigueActor_KnownProp::getAllKnownPropsForIntrigueProp($this);
    }
    
    
}
