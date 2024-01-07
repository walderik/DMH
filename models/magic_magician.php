<?php

class Magic_Magician extends BaseModel{
    
    public $Id;
    public $RoleId;
    public $MagicSchoolId;
    public $Level;
    public $ImageId;
    public $StaffApproved;
    public $MasterMagicianId;
    public $Workshop;
    public $OrganizerNotes;
    
    public static $orderListBy = 'Level';
    
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['RoleId'])) $this->RoleId = $arr['RoleId'];
        if (isset($arr['MagicSchoolId'])) $this->MagicSchoolId = $arr['MagicSchoolId'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['StaffApproved'])) $this->StaffApproved = $arr['StaffApproved'];
        if (isset($arr['MasterMagicianId'])) $this->MasterMagicianId = $arr['MasterMagicianId'];
        if (isset($arr['Workshop'])) $this->Workshop = $arr['Workshop'];
        if (isset($arr['Level'])) $this->Level = $arr['Level'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        
        if (isset($this->ImageId) && $this->ImageId=='null') $this->ImageId = null;
        if (isset($this->MagicSchoolId) && $this->MagicSchoolId=='null') $this->MagicSchoolId = null;
        if (isset($this->MasterMagicianId) && $this->MasterMagicianId=='null') $this->MasterMagicianId = null;
        
        if (isset($this->StaffApproved) && $this->StaffApproved=='0000-00-00') $this->StaffApproved = null;
        if (isset($this->Workshop) && $this->Workshop=='0000-00-00') $this->Workshop = null;
    }
    
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_magic_magician SET RoleId=?, MagicSchoolId=?, ImageId=?, StaffApproved=?, 
                    MasterMagicianId=?, Workshop=?, Level=?, OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->RoleId, $this->MagicSchoolId, $this->ImageId, $this->StaffApproved, 
                    $this->MasterMagicianId, $this->Workshop, $this->Level, $this->OrganizerNotes, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_magic_magician (RoleId, MagicSchoolId, ImageId, StaffApproved, 
            MasterMagicianId, Workshop, Level, OrganizerNotes) VALUES (?,?,?,?,?, ?,?,?);");
        
        if (!$stmt->execute(array($this->RoleId, $this->MagicSchoolId, $this->ImageId, $this->StaffApproved,
            $this->MasterMagicianId, $this->Workshop, $this->Level, $this->OrganizerNotes))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    public static function getForRole(Role $role) {
        if (empty($role)) return null;
        $sql = "SELECT * FROM regsys_magic_magician WHERE RoleId=?";
        return static::getOneObjectQuery($sql, array($role->Id));
        
    }
    
    public static function isMagician(Role $role) {
        if (empty($role)) return null;
        if (is_null(static::getForRole($role))) return false;
        return true;
    }
    
    
    
    public function getRole() {
        if (empty($this->RoleId)) return null;
        return Role::loadById($this->RoleId);
    }
    
    public function getMagicSchool() {
        if (empty($this->MagicSchoolId)) return null;
        return Magic_School::loadById($this->MagicSchoolId);
    }
    
    
    public function getMaster() {
        if (empty($this->MasterMagicianId)) return null;
        return Magic_Magician::loadById($this->MasterMagicianId);
    }
    
    public function getApprenticeNames() {
        $apprentices = $this->getApprentices();
        $names = array();
        foreach($apprentices as $apprentice) {
            $names[] = $apprentice->getRole()->Name;
        }
        return $names;
    }
    
    public function getApprentices() {
        $sql = "SELECT * FROM regsys_magic_magician WHERE MasterMagicianId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($this->Id));
        
    }
    
    
    public function isStaffApproved() {
        if (empty($this->StaffApproved)) return false;
        return true;
    }

    public function hasDoneWorkshop() {
        if (empty($this->StaffApproved)) return false;
        return true;
    }
    
    
    public function getSpells() {
        return Magic_Spell::getSpellsForMagician($this);
    }
    
    public function addSpells($spellIds, Larp $larp) {
        //Ta reda på vilka som inte redan är kopplade till magikern
        $exisitingIds = array();
        $magician_spells = $this->getSpells();
        foreach ($magician_spells as $magician_spell) {
            $exisitingIds[] = $magician_spell->Id;
        }
        
        $newSpellIds = array_diff($spellIds,$exisitingIds);
        //Koppla magier till magiker
        foreach ($newSpellIds as $spellId) {
            $this->addSpell($spellId, $larp);
        }
    }
    
    
     private function addSpell($spellId, LARP $larp) {
        
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_magician_spell (MagicMagicianId, MagicSpellId, GrantedLarpId) VALUES (?,?,?);");
        if (!$stmt->execute(array($this->Id, $spellId, $larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    
    public function removeSpell($spellId) {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_magician_spell WHERE MagicMagicianId=? AND MagicSpellId=?;");
        if (!$stmt->execute(array($this->Id, $spellId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function getGrantedLarp(Magic_Spell $spell) {
        if (empty($spell)) return null;
        $sql = "SELECT * FROM regsys_larp WHERE Id IN (".
            "SELECT GrantedLarpId FROM regsys_magician_spell WHERE MagicMagicianId=? AND MagicSpellId=?)";
        return LARP::getOneObjectQuery($sql, array($this->Id, $spell->Id));
    }
    
    public static function getMagiciansForSchool(Magic_School $school) {
        $sql = "SELECT * FROM regsys_magic_magician WHERE MagicSchoolId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($school->Id));
    }
    
    public static function getMagiciansThatKnowsSpell(Magic_Spell $spell) {
        $sql = "SELECT * FROM regsys_magic_magician WHERE Id IN (".
            "SELECT MagicMagicianId FROM regsys_magician_spell WHERE MagicSpellId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($spell->Id));
    }
    
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_magic_magician WHERE RoleId In (
            SELECT Id FROM regsys_role WHERE CampaignId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function createMagicians($roleIds, LARP $larp) {
        //Ta reda på vilka som inte redan är magiker
        $exisitingRoleIds = array();
        $magicians = static::allByCampaign($larp);
        foreach ($magicians as $magician) {
            $exisitingRoleIds[] = $magician->RoleId;
        }
        
        $newRoleIds = array_diff($roleIds,$exisitingRoleIds);
        foreach ($newRoleIds as $roleId) {
            $magician = Magic_Magician::newWithDefault();
            $magician->RoleId = $roleId;
            $magician->create();
        }
    }
    
    public function hasStaffImage() {
        if (isset($this->ImageId)) return true;
        return false;
    }
    
    
    
}