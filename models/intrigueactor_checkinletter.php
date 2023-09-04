<?php

class IntrigueActor_CheckinLetter extends BaseModel{
    
    public $Id;
    public $IntrigueActorId;
    public $IntrigueLetterId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueActorId'])) $this->IntrigueActorId = $arr['IntrigueActorId'];
        if (isset($arr['IntrigueLetterId'])) $this->IntrigueLetterId = $arr['IntrigueLetterId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor_checkinletter SET IntrigueActorId=?, IntrigueLetterId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntrigueLetterId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor_checkinletter (IntrigueActorId, IntrigueLetterId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntrigueLetterId))) {
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
    
    public function getIntrigueLetter() {
        return Intrigue_Letter::loadById($this->IntrigueLetterId);
    }
    
    public static function getAllCheckinLettersForRole(Role $role, LARP $larp) {
        $sql = "SELECT regsys_intrigueactor_checkinletter.* FROM regsys_intrigueactor_checkinletter, regsys_intrigueactor, regsys_intrigue WHERE ".
            "regsys_intrigueactor_checkinletter.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ? AND ".
            "regsys_intrigueactor.RoleId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $role->Id));
    }
    
    public static function getAllCheckinLettersForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_intrigueactor_checkinletter WHERE IntrigueActorId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function getAllCheckinLettersForIntrigueLetter(Intrigue_Letter $intrigue_letter) {
        $sql = "SELECT * FROM regsys_intrigueactor_checkinletter WHERE IntrigueLetterId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue_letter->Id));
    }
    
    public static function loadByIds($letterId, $intrigueActorId) {
        $sql = "SELECT regsys_intrigueactor_checkinletter.* FROM regsys_intrigueactor_checkinletter, regsys_intrigue_letter, regsys_intrigueactor WHERE ".
            "regsys_intrigueactor.Id = ?  AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_checkinletter.IntrigueActorId AND ".
            "regsys_intrigue_letter.Id = regsys_intrigueactor_checkinletter.IntrigueLetterId AND ".
            "regsys_intrigue_letter.LetterId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue_letter.IntrigueId";
        return static::getOneObjectQuery($sql, array($intrigueActorId, $letterId));
    }
    
    
}
