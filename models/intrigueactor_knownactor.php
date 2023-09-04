<?php

class IntrigueActor_KnownActor extends BaseModel{
    
    public $Id;
    public $IntrigueActorId;
    public $KnownIntrigueActorId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueActorId'])) $this->IntrigueActorId = $arr['IntrigueActorId'];
        if (isset($arr['KnownIntrigueActorId'])) $this->KnownIntrigueActorId = $arr['KnownIntrigueActorId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor_knownactor SET IntrigueActorId=?, KnownIntrigueActorId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->KnownIntrigueActorId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor_knownactor (IntrigueActorId, KnownIntrigueActorId) VALUES (?,?)");

        if (!$stmt->execute(array($this->IntrigueActorId, $this->KnownIntrigueActorId))) {
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
    
    public function getKnownIntrigueActor() {
        return IntrigueActor::loadById($this->KnownIntrigueActorId);
    }
    
    
    
    public static function getAllKnownIntrigueActorsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_intrigueactor_knownactor WHERE IntrigueActorId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function getAllWhoKnowsIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_intrigueactor_knownactor WHERE KnownIntrigueActorId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function loadByIds($actorId, $intrigueActorId, $isRoleActor) {
        if ($isRoleActor) {
        $sql = "SELECT regsys_intrigueactor_knownactor.* FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ka, regsys_intrigueactor WHERE ".
            "regsys_intrigueactor.Id = ?  AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
            "ka.Id = regsys_intrigueactor_knownactor.KnownIntrigueActorId AND ".
            "ka.RoleId = ? AND ".
            "regsys_intrigueactor.IntrigueId = ka.IntrigueId";
        }
        else {
            $sql = "SELECT regsys_intrigueactor_knownactor.* FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ka, regsys_intrigueactor WHERE ".
                "regsys_intrigueactor.Id = ?  AND ".
                "regsys_intrigueactor.Id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
                "ka.Id = regsys_intrigueactor_knownactor.KnownIntrigueActorId AND ".
                "ka.GroupId = ? AND ".
                "regsys_intrigueactor.IntrigueId = ka.IntrigueId";
            
        }
        return static::getOneObjectQuery($sql, array($intrigueActorId, $actorId));
    }
}
