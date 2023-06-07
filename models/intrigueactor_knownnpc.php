<?php

class IntrigueActor_KnownNPC extends BaseModel{
    
    public $Id;
    public $IntrigueActorId;
    public $IntrigueNPCId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueActorId'])) $this->IntrigueActorId = $arr['IntrigueActorId'];
        if (isset($arr['IntrigueNPCId'])) $this->IntrigueNPCId = $arr['IntrigueNPCId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor_knownnpc SET IntrigueActorId=?, IntrigueNPCId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntrigueNPCId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor_knownnpc (IntrigueActorId, IntrigueNPCId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntrigueNPCId))) {
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
    
    public function getIntrigueNPC() {
        return Intrigue_NPC::loadById($this->IntrigueNPCId);
    }
    
    public static function getAllKnownNPCsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_intrigueactor_knownnpc WHERE IntrigueActorId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function loadByIds($npcId, $intrigueActorId) {
        $sql = "SELECT regsys_intrigueactor_knownnpc.* FROM regsys_intrigueactor_knownnpc, regsys_intrigue_npc, regsys_intrigueactor WHERE ".
            "regsys_intrigueactor.Id = ?  AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_knownnpc.IntrigueActorId AND ".
            "regsys_intrigue_npc.Id = regsys_intrigueactor_knownnpc.IntrigueNPCId AND ".
            "regsys_intrigue_npc.NPCId = ? AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue_npc.IntrigueId";
        return static::getOneObjectQuery($sql, array($intrigueActorId, $npcId));
    }
}
