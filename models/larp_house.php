<?php

class Larp_House extends BaseModel{
    
    const NOT_CLEANED = 0;
    const READY_FOR_INSPECTION = 10;
    const CLEANING_APPROVED = 100;
    const STATUS_TYPES = [
        Larp_House::NOT_CLEANED => "Inte städat",
        Larp_House:: READY_FOR_INSPECTION => "Klart för kontroll",
        //20 => "Städning underkänd",
        Larp_House::CLEANING_APPROVED => "Städning godkänd"
    ];
    
    public $Id;
    public $LARPId;
    public $HouseId;
    public $OrganizerNotes;
    public $PublicNotes;
    public $CleaningStatus = Larp_House::NOT_CLEANED;
    public $StatusPerson;
    public $StatusTime;
    public $CleaningNotes;
    
    
    public static $orderListBy = 'HouseId';
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        if (isset($post['Id'])) $object->Id = $post['Id'];
        if (isset($post['LARPId'])) $object->LARPId = $post['LARPId'];
        if (isset($post['HouseId'])) $object->HouseId = $post['HouseId'];
        if (isset($post['OrganizerNotes'])) $object->OrganizerNotes = $post['OrganizerNotes'];
        if (isset($post['PublicNotes'])) $object->PublicNotes = $post['PublicNotes'];
        if (isset($post['CleaningStatus'])) $object->CleaningStatus = $post['CleaningStatus'];
        if (isset($post['StatusPerson'])) $object->StatusPerson = $post['StatusPerson'];
        if (isset($post['StatusTime'])) $object->StatusTime = $post['StatusTime'];
        if (isset($post['CleaningNotes'])) $object->CleaningNotes = $post['CleaningNotes'];
        return $object;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_larp_house (LARPId, HouseId, OrganizerNotes, PublicNotes, CleaningStatus, StatusPerson, StatusTime, CleaningNotes) VALUES (?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->HouseId, $this->OrganizerNotes, $this->PublicNotes, $this->CleaningStatus, $this->StatusPerson, $this->StatusTime, $this->CleaningNotes))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_larp_house SET OrganizerNotes=?, PublicNotes=?, CleaningStatus=?, StatusPerson=?, StatusTime=?, CleaningNotes=? WHERE Id=?;");
        
        if (!$stmt->execute(array($this->OrganizerNotes, $this->PublicNotes, $this->CleaningStatus, $this->StatusPerson, $this->StatusTime, $this->CleaningNotes, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public static function loadByIds($houseId, $larpId) {
        $sql = "SELECT * FROM regsys_larp_house WHERE HouseId = ? AND LARPId = ?";
        return static::getOneObjectQuery($sql, array($houseId, $larpId));
    }
    
    public function getLarp() {
        return LARP::loadById($this->LARPId);
    }
    
    public function getHouse() {
        return House::loadById($this->HouseId);
    }
    
    public function getStatusText() {
        return Larp_House::STATUS_TYPES[$this->CleaningStatus];
    }
    
}