<?php

class Magic_Magician extends BaseModel{
    
    public $Id;
    public $RoleId;
    public $MagicSchoolId;
    public $Level;
    public $StaffImageId;
    public $StaffApproved;
    public $MasterMagicianId;
    public $Workshop;
    public $OrganizerNotes;
    
    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['RoleId'])) $this->RoleId = $arr['RoleId'];
        if (isset($arr['MagicSchoolId'])) $this->MagicSchoolId = $arr['MagicSchoolId'];
        if (isset($arr['StaffImageId'])) $this->StaffImageId = $arr['StaffImageId'];
        if (isset($arr['StaffApproved'])) $this->StaffApproved = $arr['StaffApproved'];
        if (isset($arr['MasterMagicianId'])) $this->MasterMagicianId = $arr['MasterMagicianId'];
        if (isset($arr['Workshop'])) $this->Workshop = $arr['Workshop'];
        if (isset($arr['Level'])) $this->Level = $arr['Level'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        
        if (isset($this->StaffImageId) && $this->StaffImageId=='null') $this->StaffImageId = null;
        if (isset($this->MagicSchoolId) && $this->MagicSchoolId=='null') $this->MagicSchoolId = null;
        if (isset($this->MasterMagicianId) && $this->MasterMagicianId=='null') $this->MasterMagicianId = null;
    }
    
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_magic_magician SET RoleId=?, MagicSchoolId=?, StaffImageId=?, StaffApproved=?, 
                    MasterMagicianId=?, Workshop=?, Level=?, OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->RoleId, $this->MagicSchoolId, $this->StaffImageId, $this->StaffApproved, 
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
        $stmt = $connection->prepare("INSERT INTO regsys_magic_magician (RoleId, MagicSchoolId, StaffImageId, StaffApproved, 
            MasterMagicianId, Workshop, Level, OrganizerNotes) VALUES (?,?,?,?,?, ?,?,?);");
        
        if (!$stmt->execute(array($this->RoleId, $this->MagicSchoolId, $this->StaffImageId, $this->StaffApproved,
            $this->MasterMagicianId, $this->Workshop, $this->Level, $this->OrganizerNotes))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
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
    
    
    public function getSpells() {
        Magic_Spell::getSpellsForMagician($this);
    }
    
    public function addSpell(Magic_Spell $spell, LARP $larp) {
        if (empty($spell)) return null;
        
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_magician_spell (MagicMagicianId, MagicSpellId, GrantedLarpId) VALUES (?,?,?);");
        if (!$stmt->execute(array($this->Id, $spell->Id, $larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    public function removeSpell(Magic_Spell $spell) {
        if (empty($spell)) return null;
        
        $stmt = $this->connect()->prepare("DELETE FROM regsys_magician_spell WHERE MagicMagicianId=? AND MagicSpellId=?;");
        if (!$stmt->execute(array($this->Id, $spell->Id))) {
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
    
    
}