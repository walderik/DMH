<?php

class IntrigueActor_CheckinProp extends BaseModel{
    
    public $Id;
    public $IntrigueActorId;
    public $IntriguePropId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueActorId'])) $this->IntrigueActorId = $arr['IntrigueActorId'];
        if (isset($arr['IntriguePropId'])) $this->IntriguePropId = $arr['IntriguePropId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor_checkinprop SET IntrigueActorId=?, IntriguePropId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntriguePropId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor_checkinprop (IntrigueActorId, IntriguePropId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntriguePropId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getIntrigueActor() {
        return IntrigueActor::loadById($this->IntrigueActorId);
    }
    
    public function getIntrigueProp() {
        return Intrigue_Letter::loadById($this->IntriguePropId);
    }
    
    public static function getAllCheckinPropsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_intrigueactor_checkinprop WHERE IntrigueActorId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function getAllCheckinPropsForIntrigueProp(Intrigue_Prop $intrigue_prop) {
        $sql = "SELECT * FROM regsys_intrigueactor_checkinprop WHERE IntriguePropId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue_prop->Id));
    }
    
    public static function loadByIds($propId, $intrigueActorId) {
        $sql = "SELECT regsys_intrigueactor_checkinprop.* FROM regsys_intrigueactor_checkinprop, regsys_intrigue_prop, regsys_intrigueactor WHERE ".
            "regsys_intrigueactor.Id = ?  AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_checkinprop.IntrigueActorId AND ".
            "regsys_intrigue_prop.Id = regsys_intrigueactor_checkinprop.IntriguePropId AND ".
            "regsys_intrigue_prop.PropId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue_prop.IntrigueId";
        return static::getOneObjectQuery($sql, array($intrigueActorId, $propId));
    }


}
