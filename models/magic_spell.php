<?php

class Magic_Spell extends BaseModel{
    
    public $Id;
    public $Name;
    public $Description;
    public $OrganizerNotes;
    public $Type;
    public $Special;
    public $Level;
    public $CampaignId;
    

    const TYPES = [
        "Magi",
        "Ritual"
    ];
  

    
    
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
        if (isset($arr['Type'])) $this->Type = $arr['Type'];
        if (isset($arr['Special'])) $this->Special = $arr['Special'];
        if (isset($arr['Level'])) $this->Level = $arr['Level'];
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
        $stmt = $this->connect()->prepare("UPDATE regsys_magic_spell SET Name=?, Description=?, OrganizerNotes=?, Type=?, Special=?, Level=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->OrganizerNotes, $this->Type, $this->Special, $this->Level, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_magic_spell (Name, Description, OrganizerNotes, Type, Special, Level, CampaignId) VALUES (?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->OrganizerNotes, $this->Type, $this->Special, $this->Level, $this->CampaignId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    
    public static function getSpellsForMagician(Magic_Magician $magician) {
        $sql = "SELECT * FROM regsys_magic_spell WHERE Id IN (".
            "SELECT MagicSpellId FROM regsys_magician_spell WHERE MagicMagicianId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($magician->Id));
    }
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_magic_spell WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
}