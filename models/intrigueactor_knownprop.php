<?php

class IntrigueActor_KnownProp extends BaseModel{
    
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
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor_knownprop SET IntrigueActorId=?, IntriguePropId=? WHERE Id = ?");
        
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
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor_knownprop (IntrigueActorId, IntriguePropId) VALUES (?,?)");
        
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
        return Intrigue_Prop::loadById($this->IntriguePropId);
    }
    
    public static function getAllKnownPropsForRole(Role $role, LARP $larp) {
        $sql = "SELECT regsys_intrigueactor_knownprop.* FROM regsys_intrigueactor_knownprop, regsys_intrigueactor, regsys_intrigue WHERE ".
            "regsys_intrigueactor_knownprop.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ? AND ".
            "regsys_intrigueactor.RoleId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $role->Id));
    }
    
    public static function getAllKnownPropsForGroup(Group $group, LARP $larp) {
        $sql = "SELECT regsys_intrigueactor_knownprop.* FROM regsys_intrigueactor_knownprop, regsys_intrigueactor, regsys_intrigue WHERE ".
            "regsys_intrigueactor_knownprop.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ? AND ".
            "regsys_intrigueactor.GroupId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $group->Id));
    }
    
    public static function getAllKnowninPropsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_intrigueactor_knownprop WHERE IntrigueActorId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function getAllKnownPropsForIntrigueProp(Intrigue_Prop $intrigue_prop) {
        $sql = "SELECT * FROM regsys_intrigueactor_knownprop WHERE IntriguePropId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue_prop->Id));
    }
    
    public static function loadByIds($propId, $intrigueActorId) {
        $sql = "SELECT regsys_intrigueactor_knownprop.* FROM regsys_intrigueactor_knownprop, regsys_intrigue_prop, regsys_intrigueactor WHERE ".
            "regsys_intrigueactor.Id = ?  AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_knownprop.IntrigueActorId AND ".
            "regsys_intrigue_prop.Id = regsys_intrigueactor_knownprop.IntriguePropId AND ".
            "regsys_intrigue_prop.PropId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue_prop.IntrigueId";
        return static::getOneObjectQuery($sql, array($intrigueActorId, $propId));
    }
}
