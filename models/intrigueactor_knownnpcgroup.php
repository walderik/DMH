<?php

class IntrigueActor_KnownNPCGroup extends BaseModel{
    
    public $Id;
    public $IntrigueActorId;
    public $IntrigueNPCGroupId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueActorId'])) $this->IntrigueActorId = $arr['IntrigueActorId'];
        if (isset($arr['IntrigueNPCGroupId'])) $this->IntrigueNPCGroupId = $arr['IntrigueNPCGroupId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor_knownnpcgroup SET IntrigueActorId=?, IntrigueNPCGroupId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntrigueNPCGroupId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor_knownnpcgroup (IntrigueActorId, IntrigueNPCGroupId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntrigueNPCGroupId))) {
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
    
    public function getIntrigueNPCGroup() {
        return Intrigue_NPCGroup::loadById($this->IntrigueNPCGroupId);
    }
    
    public static function getAllKnownNPCGroupsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_intrigueactor_knownnpcgroup WHERE IntrigueActorId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function getAllKnownNPCGroupsForIntrigueNPCGroup(Intrigue_NPCGroup $intrigue_npcgroup) {
        $sql = "SELECT * FROM regsys_intrigueactor_knownnpcgroup WHERE IntrigueNPCGroupId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue_npcgroup->Id));
    }
    
    
    public static function loadByIds($npcgroupId, $intrigueActorId) {
        $sql = "SELECT regsys_intrigueactor_knownnpcgroup.* FROM regsys_intrigueactor_knownnpcgroup, regsys_intrigue_npcgroup, regsys_intrigueactor WHERE ".
            "regsys_intrigueactor.Id = ?  AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_knownnpcgroup.IntrigueActorId AND ".
            "regsys_intrigue_npcgroup.Id = regsys_intrigueactor_knownnpcgroup.IntrigueNPCGroupId AND ".
            "regsys_intrigue_npcgroup.NPCGroupId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue_npcgroup.IntrigueId";
        return static::getOneObjectQuery($sql, array($intrigueActorId, $npcgroupId));
    }
}
