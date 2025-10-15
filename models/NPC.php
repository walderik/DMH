<?php

class NPC extends BaseModel{
    
    public $Id;
    public $RoleId;
    public $Name;
    public $Description;
    public $Time;
    public $PersonId;
    public $NPCGroupId;
    public $LarpId;
    public $ImageId;
    public $IsReleased = 0;
    public $IsToBePlayed = 1;
    
    
    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $npc = static::newWithDefault();
        $npc->setValuesByArray($post);
        return $npc;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['RoleId']))   $this->RoleId = $arr['RoleId'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['Time'])) $this->Time = $arr['Time'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['NPCGroupId'])) $this->NPCGroupId = $arr['NPCGroupId'];
        if (isset($arr['LARPId'])) $this->LarpId = $arr['LARPId'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['IsReleased'])) $this->IsReleased = $arr['IsReleased'];
        if (isset($arr['IsToBePlayed'])) $this->IsToBePlayed = $arr['IsToBePlayed'];
        
        if (isset($this->NPCGroupId) && $this->NPCGroupId=='null') $this->NPCGroupId = null;
        if (isset($this->ImageId) && $this->ImageId=='null') $this->ImageId = null;
        
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
        $stmt = $this->connect()->prepare("UPDATE regsys_npc SET RoleId=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->RoleId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        
       $stmt = $connection->prepare("INSERT INTO regsys_npc (RoleId, Name, Description,
                                                            Time, PersonId,
                                                            NPCGroupId, LarpId, ImageId, IsReleased, IsToBePlayed) 
                                                            VALUES (?,?,?,?,?, ?,?,?,?);");
        
        if (!$stmt->execute(array($this->RoleId, $this->Name, $this->Description, $this->Time,
            $this->PersonId, $this->NPCGroupId, $this->LarpId, $this->ImageId, $this->IsReleased, $this->IsToBePlayed))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public function getRole() {
        return Role::loadById($this->RoleId);
    }
    
    public static function getAllForLarp(LARP $larp) {
        if (empty($larp)) return Array();
        $sql = "SELECT * FROM regsys_npc WHERE ".
            "LarpId = ? ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
   
   
    public static function getAllAssignedByGroup(Group $group, LARP $larp) {
        if (empty($larp) or empty($group)) return Array();
        $sql = "SELECT regsys_npc.* FROM regsys_npc, regsys_role WHERE ".
            "regsys_npc.PersonId IS NOT NULL AND ".
            "regsys_npc.LarpId = ? AND ".
            "regsys_npc.RoleId = regsys_role.Id AND ".
            "regsys_role.GroupId = ? ".
            "ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $group->Id));
    }
     
    public static function getAllUnassignedByGroup(Group $group, LARP $larp) {
        if (empty($larp) or empty($group)) return Array();
        $sql = "SELECT regsys_npc.* FROM regsys_npc, regsys_role WHERE ".
            "regsys_npc.PersonId IS NULL AND ".
            "regsys_npc.LarpId = ? AND ".
            "regsys_npc.RoleId = regsys_role.Id AND ".
            "regsys_role.GroupId = ? ".
            "ORDER BY Name;";
        
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $group->Id));
    }
    
    public static function getAllAssignedWithoutGroup(LARP $larp) {
        if (empty($larp)) return Array();
        $sql = "SELECT regsys_npc.* FROM regsys_npc, regsys_role WHERE ".
            "regsys_npc.PersonId IS NOT NULL AND ".
            "regsys_npc.LarpId = ? AND ".
            "regsys_npc.RoleId = regsys_role.Id AND ".
            "regsys_role.GroupId IS NULL ".
            "ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllUnassignedWithoutGroup(LARP $larp) {
        if (empty($larp)) return Array();
        $sql = "SELECT regsys_npc.* FROM regsys_npc, regsys_role WHERE ".
            "regsys_npc.PersonId IS NULL AND ".
            "regsys_npc.LarpId = ? AND ".
            "regsys_npc.RoleId = regsys_role.Id AND ".
            "regsys_role.GroupId IS NULL ".
            "ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    # Hämta NPCer i en grupp
    public static function getNPCsInGroup(Group $group, LARP $larp) {
        if (empty($group) || empty($larp)) return Array();
        $sql = "SELECT regsys_npc.* FROM regsys_npc, regsys_role WHERE ".
            "regsys_npc.LarpId = ? AND ".
            "regsys_npc.RoleId = regsys_role.Id AND ".
            "regsys_role.GroupId = ? ".
            "ORDER BY Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $group->Id));
    }

    
    
    
    public static function getReleasedNPCsForPerson(Person $person, LARP $larp) {
        if (empty($person) or empty($larp))return Array();
        $sql = "SELECT * FROM regsys_npc WHERE PersonId=? AND IsReleased=1 AND LARPId =? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id, $larp->Id));
    }
    
    public function release() {
        global $current_person;
        if ($this->IsReleased()) return;
        $this->IsReleased = 1;
        $this->update();

        BerghemMailer::sendNPCMail($this, $current_person->Id);
  
    }
    
    public function IsReleased() {
        if ($this->IsReleased==1) return true;
        return false;
    }
    
    public function IsToBePlayed() {
        if ($this->IsToBePlayed==1) return true;
        return false;
    }
    
    public function IsAssigned() {
        if (empty($this->PersonId)) {
            return false;
        }
        return true;
    }
    
    public function getPerson() {
        if (!empty($this->PersonId)) {
            return Person::loadById($this->PersonId);
        }
        return null;
    }

    public function getLARP() {
        if (!empty($this->LarpId)) {
            return LARP::loadById($this->LarpId);
        }
        return null;
    }
    
    public static function getAllGroupsForLARP(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_group WHERE ID IN (".
            "SELECT GroupId FROM regsys_role, regsys_npc WHERE ".
            "regsys_npc.LarpId = ? AND ".
            "regsys_npc.RoleId = regsys_role.Id ".
            ") ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public function getNPCGroup() {
        if (empty($this->NPCGroupId)) return null;
        return NPCGroup::loadById($this->NPCGroupId);
    }
    
    public function hasImage() {
        if (isset($this->ImageId)) return true;
        return false;
    }
    
    
    
}