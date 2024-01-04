<?php

class Magic_School extends BaseModel{
    
    public $Id;
    public $Name;
    public $Description;
    public $OrganizerNotes;
    public $CampaignId;
    
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
    }
    
        
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $object = new self();
        $object->CampaignId = $current_larp->CampaignId;
        return $object;
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_magic_school SET Name=?, Description=?, OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->OrganizerNotes, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_magic_school (Name, Description, OrganizerNotes, CampaignId) VALUES (?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->OrganizerNotes, $this->CampaignId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    
    
    public function addSpells($spellIds) {
        //Ta reda på vilka som inte redan är kopplade till skolan
        $exisitingIds = array();
        $school_spells = $this->getAllSpells();
        foreach ($school_spells as $school_spell) {
            $exisitingIds[] = $school_spells->Id;
        }
        
        $newSpellIds = array_diff($spellIds,$exisitingIds);
        //Koppla magier till skolan
        foreach ($newSpellIds as $spellId) {
            $this->addSpell($spellId);
        }
    }
    
    private function addSpell($spellId) {
        
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_magicschool_spell (MagicSchoolId, MagicSpellId) VALUES (?,?);");
        if (!$stmt->execute(array($this->Id, $spellId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    public function removeSpell($spellId) {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_magicschool_spell WHERE MagicSchoolId=? AND MagicSpellId = ?;");
        if (!$stmt->execute(array($this->Id, $spellId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public function getAllSpells() {
        return Magic_Spell::getSpellsForSchool($this);
    }
    
    public function getAllMagicians() {
        return Magic_Magician::getMagiciansForSchool($this);
    }
    
    public static function getSchoolsForSpell(Magic_Spell $spell) {
        $sql = "SELECT * FROM regsys_magic_school WHERE Id IN (".
            "SELECT MagicSchoolId FROM regsys_magicschool_spell WHERE MagicSpellId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($spell->Id));
    }
    
    
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_magic_school WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
}