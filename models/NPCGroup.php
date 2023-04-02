<?php

class NPCGroup extends BaseModel{
    
    public $Id;
    public $Name;
    public $Description;
    public $Time;
    public $LarpId;
    public $IsReleased = 0;
    
    
    
    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $npc_group = static::newWithDefault();
        $npc_group->setValuesByArray($post);
        return $npc_group;
    }
    
    public function setValuesByArray($arr) {
        
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['Time'])) $this->Time = $arr['Time'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['IsReleased'])) $this->IsReleased = $arr['IsReleased'];
        
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $newOne = new self();
        $newOne->LarpId = $current_larp->Id;
        return $newOne;
    }
    
    
    # Update an existing object in db
    public function update() {

        $stmt = $this->connect()->prepare("UPDATE regsys_npcgroup SET Name=?, Description=?,
                                                              Time=?, LarpId=?, IsReleased=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->Description,
            $this->Time, $this->LarpId, $this->IsReleased, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() {

        $connection = $this->connect();
        
        
        $stmt = $connection->prepare("INSERT INTO regsys_npcgroup(Name, Description,
                                                            Time, LarpId, IsReleased) VALUES (?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->Time,
            $this->LarpId, $this->IsReleased))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public function release() {
        $this->IsReleased = 1;
        $npcs = $this->getNPCsInGroup();
        foreach ($npcs as $npc) {
            $npc->release();
        }
        $this->update();
        
        
    }
    
    
    public function IsReleased() {
        if ($this->IsReleased==1) return true;
        return false;
    }
    
    
    public static function getAllForLARP(LARP $larp) {
        global $tbl_prefix;
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM ".$tbl_prefix."npcgroup WHERE ".
            "LarpId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public function getNPCsInGroup() {
        global $current_larp;
        return NPC::getNPCsInGroup($this, $current_larp);
    }
    
    
}