<?php

class Intrigue_NPCGroup extends BaseModel{
    
    public $Id;
    public $IntrigueId;
    public $NPCGroupId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $house = static::newWithDefault();
        $house->setValuesByArray($post);
        return $house;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        if (isset($arr['NPCGroupId'])) $this->NPCGroupId = $arr['NPCGroupId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigue_npcgroup SET IntrigueId=?, NPCGroupId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->NPCGroupId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigue_npcgroup (IntrigueId, NPCGroupId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->NPCGroupId))) {
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
    
    
    public function getAllIntrigues() {
        return Intrigue::getAllIntriguesForIntrigueNPCGroup($this);
    }
    
    public function getNPCGroup() {
        return NPCGroup::loadById($this->NPCGroupId);
    }
    
    public static function delete($id)
    {
        $intrigueNPCGroup = static::loadById($id);
        $known_npcgroups = $intrigueNPCGroup->getAllKnownNPCGroups();
        foreach ($known_npcgroups as $known_npcgroup) IntrigueActor_KnownNPCGroup::delete($known_npcgroup->Id);
        
        parent::delete($id);
     }
    
    public static function getAllNPCGroupsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigue_npcgroup WHERE IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    public function getAllKnownNPCGroups() {
        return IntrigueActor_KnownNPCGroup::getAllKnownNPCGroupsForIntrigueNPCGroup($this);
    }
    
    
    
}
