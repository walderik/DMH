<?php

class Vision extends BaseModel{
    
    public $Id;
    public $LARPid;
    public $WhenDate;
    public $WhenSpec;
    
    public $VisionText;
    public $Source; 
    public $SideEffect;
    public $OrganizerNotes;
    
    
    const TIME_OF_DAY = [
        0 => "Förmiddag",
        1 => "Lunch-tid",
        2 => "Eftermiddag",
        3 => "Kväll"
    ];
    
    public static $orderListBy = 'WhenDate, WhenSpec';
    
    public static function newFromArray($post) {
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
     
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['LARPid'])) $this->LARPid = $arr['LARPid'];
        if (isset($arr['WhenDate'])) $this->WhenDate = $arr['WhenDate'];
        if (isset($arr['WhenSpec'])) $this->WhenSpec = $arr['WhenSpec'];
        if (isset($arr['VisionText'])) $this->VisionText = $arr['VisionText'];
        if (isset($arr['Source'])) $this->Source = $arr['Source'];
        if (isset($arr['SideEffect'])) $this->SideEffect = $arr['SideEffect'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp, $current_user;
        
        $object = new self();
        $object->LARPid = $current_larp->Id;
        return $object;
    }
    
    
    public static function allBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_vision WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function getAllForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_vision WHERE Id IN (SELECT VisionId FROM regsys_intrigue_vision WHERE IntrigueId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    public function getAllIntriguesForVision() {
        $sql = "SELECT * FROM regsys_intrigue WHERE Id IN (SELECT IntrigueId FROM regsys_intrigue_vision WHERE VisionId=?) ORDER BY ".Intrigue::$orderListBy.";";
        return Intrigue::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    
    
    public static function allKnownByRole(Larp $larp, Role $role) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_vision WHERE LARPid = ? AND Id IN (".
            "SELECT VisionId FROM regsys_vision_has WHERE RoleId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $role->Id));
    }
    
    public function getTimeOfDayStr() {
        if (!isset($this->WhenSpec)) return null;
        return Vision::TIME_OF_DAY[$this->WhenSpec];
    }
    
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_vision SET WhenDate=?, WhenSpec=?, VisionText=?, SideEffect=?, Source=?, OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->WhenDate, $this->WhenSpec, $this->VisionText, $this->SideEffect, $this->Source, $this->OrganizerNotes, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_vision (WhenDate, WhenSpec, VisionText, SideEffect, Source, OrganizerNotes, LARPid) VALUES (?,?,?, ?, ?, ?,?)");
        
        if (!$stmt->execute(array($this->WhenDate, $this->WhenSpec, $this->VisionText, $this->SideEffect, $this->Source, $this->OrganizerNotes, $this->LARPid))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
       }
       $this->Id = $connection->lastInsertId();
       $stmt = null;
    }
    
     public function getHas() {
         $sql = "SELECT * FROM regsys_role WHERE Id IN (SELECT RoleId FROM regsys_vision_has WHERE VisionId = ?) ORDER BY ".Role::$orderListBy.";";
         return Role::getSeveralObjectsqQuery($sql, array($this->Id));
    }

    public function getHasCount() {
        global $current_larp;
        $vision_has =  $this->getHas();
        $i = 0;
        foreach ($vision_has as $role) {
            $person = $role->getPerson();
            $registration=$person->getRegistration($current_larp);
            if ($registration->isNotComing()) continue;
            $i++;
        }
        return $i;
    }
    
    
    
    public function addRolesHas($roleIds) {
        //Ta reda på vilka som inte redan är kopplade till synen
        $exisitingRoleIds = array();
        $hasArr = $this->getHas();
        foreach ($hasArr as $has) {
            $exisitingRoleIds[] = $has->Id;
        }
        
        $newRoleIds = array_diff($roleIds,$exisitingRoleIds);
        foreach ($newRoleIds as $roleId) {
            $this->addRoleHas($roleId);
        }
    }
    
    private function addRoleHas($roleId) {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_vision_has (VisionId, RoleId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->Id, $roleId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
        
    }
    
    
    public function removeRoleHas($roleId) {
        $connection = $this->connect();
        $stmt =  $connection->prepare("DELETE FROM regsys_vision_has WHERE VisionId=? AND RoleId=?");
        
        if (!$stmt->execute(array($this->Id, $roleId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
        
    }
    
    
    public static function delete($id)
    {
        $vision = static::loadById($id);
        
        if (empty($vision)) return; 
        if (!$vision->mayDelete()) return;
 
        $vision_has = $vision->getHas();
        foreach ($vision_has as $has) $vision->removeRoleHas($has);
 
        parent::delete($id);
    }
    
    public function mayDelete() {
        $intrigues = $this->getAllIntriguesForVision();
        if (!empty($intrigues)) return false;
        return true;
    }
    
}
