<?php

class NPC_assignment extends BaseModel{
    
    public $Id;
    public $RoleId;
    public $PersonId;
    public $LarpId;
    public $Instructions;
    public $Time;
    public $IsReleased = 0;
    public $WhatHappened;
    public $WhatHappendToOthers;
    
    
    public static $orderListBy = 'RoleId';
    
    
    public static function newFromArray($post){
        $npc = static::newWithDefault();
        $npc->setValuesByArray($post);
        return $npc;
    }
    
    public function setValuesByArray($arr) {
        
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['RoleId']))   $this->RoleId = $arr['RoleId'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['Instructions'])) $this->Instructions = $arr['Instructions'];
        if (isset($arr['Time'])) $this->Time = $arr['Time'];
        if (isset($arr['IsReleased'])) $this->IsReleased = $arr['IsReleased'];
        if (isset($arr['WhatHappened'])) $this->WhatHappened = $arr['WhatHappened'];
        if (isset($arr['WhatHappendToOthers'])) $this->WhatHappendToOthers = $arr['WhatHappendToOthers'];
        
        if (isset($this->PersonId) && $this->PersonId=='null') $this->PersonId = null;
        
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
        $stmt = $this->connect()->prepare("UPDATE regsys_npc_assignment SET RoleId=?, PersonId=?, LarpId=?, Instructions=?,
                Time=?, IsReleased=?, WhatHappened=?, WhatHappendToOthers=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->RoleId, $this->PersonId, $this->LarpId, $this->Instructions,
            $this->Time, $this->IsReleased, $this->WhatHappened, $this->WhatHappendToOthers, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        
       $stmt = $connection->prepare("INSERT INTO regsys_npc_assignment (RoleId, PersonId, LarpId, Instructions,
                                                            Time, IsReleased, WhatHappened, WhatHappendToOthers) 
                                                            VALUES (?,?,?,?,?, ?,?,?);");
        
       if (!$stmt->execute(array($this->RoleId, $this->PersonId, $this->LarpId, $this->Instructions,
           $this->Time, $this->IsReleased, $this->WhatHappened, $this->WhatHappendToOthers))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public static function getAllForLarp(LARP $larp) {
        if (empty($larp)) return Array();
        $sql = "SELECT regsys_npc_assigmenets.* FROM regsys_npc_assignment, regsys_role WHERE ".
            "LarpId = ? AND ".
            "regsys_npc_assignment.RoleId = regsys_role.Id".
            "ORDER BY regsys_role.Name;";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getReleasedNPCsForPerson(Person $person, LARP $larp) {
        if (empty($person) or empty($larp))return Array();
        $sql = "SELECT * FROM regsys_npc_assignment WHERE PersonId=? AND IsReleased=1 AND LARPId =? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id, $larp->Id));
    }
    
    public function release() {
        global $current_person;
        $this->IsReleased = 1;
        $this->update();

        BerghemMailer::sendNPCMail($this, $current_person->Id);
  
    }
    
    public static function getAssignment(Role $role, Larp $larp) {
        $sql = "SELECT * FROM regsys_npc_assignment WHERE RoleId = ? and LarpId = ?";
        return static::getOneObjectQuery($sql, array($role->Id, $larp->Id));
    }
    
    public function getRole() {
        return Role::loadById($this->RoleId);
    }
    
    public function isReleased() {
        if ($this->IsReleased==1) return true;
        return false;
    }
    
    public function isAssigned() {
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
    
    public static function numberNotComingNPCPlayers(Larp $larp) {
        if (is_null($larp)) return 0;
        $sql = "SELECT count(regsys_npc_assignment.Id) as Num FROM regsys_npc_assignment, regsys_registration WHERE ".
            "regsys_npc_assignment.PersonId = regsys_registration.PersonId AND ".
            "regsys_npc_assignment.LarpId = ? AND ".
            "regsys_registration.LarpId = regsys_npc_assignment.LarpId AND ".
            "regsys_registration.NotComing = 1";
        return static::countQuery($sql, array($larp->Id));
    }
 
    public static function numberUnassigned(Larp $larp) {
        if (is_null($larp)) return 0;
        $sql = "SELECT count(regsys_npc_assignment.Id) as Num FROM regsys_npc_assignment WHERE ".
            "regsys_npc_assignment.LarpId = ? AND ".
            "regsys_npc_assignment.PersonId IS NULL";
        return static::countQuery($sql, array($larp->Id));
    }
    
    public static function numberUnreleased(Larp $larp) {
        if (is_null($larp)) return 0;
        $sql = "SELECT count(regsys_npc_assignment.Id) as Num FROM regsys_npc_assignment WHERE ".
            "regsys_npc_assignment.LarpId = ? AND ".
            "regsys_npc_assignment.PersonId IS NOT NULL AND ".
            "regsys_npc_assignment.IsReleased = 0";
        return static::countQuery($sql, array($larp->Id));
    }
    
    public static function numberInterestedUnassigned($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT count(regsys_person.Id) as Num from regsys_person WHERE Id IN (SELECT PersonId FROM ".
            "regsys_registration WHERE LarpId = ? AND NPCDesire <> '' AND NotComing = 0) ".
        "AND Id NOT In (SELECT PersonId FROM regsys_npc_assignment WHERE LarpId = ?)";
        return static::countQuery($sql, array($larp->Id, $larp->Id));
    }
    
    
}