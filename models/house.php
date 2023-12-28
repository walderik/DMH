<?php

class House extends BaseModel{
    
    public $Id;
    public $Name;
    public $NumberOfBeds;
    public $PositionInVillage;
    public $Description;
    public $ImageId;
    public $IsHouse = 0; //1= hus, 0=lägerplats
    public $NotesToUsers;
    public $History;
    public $DeletedAt;
    public $InspectionNotes;
    public $Position;
        
    public static $orderListBy = 'IsHouse, Name';
    
    public static function newFromArray($post){
        $house = static::newWithDefault();
        $house->setValuesByArray($post);
        return $house;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['NumberOfBeds'])) $this->NumberOfBeds = $arr['NumberOfBeds'];
        if (isset($arr['PositionInVillage'])) $this->PositionInVillage = $arr['PositionInVillage'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['IsHouse'])) $this->IsHouse = $arr['IsHouse'];
        if (isset($arr['NotesToUsers'])) $this->NotesToUsers = $arr['NotesToUsers'];
        if (isset($arr['History'])) $this->History = $arr['History'];
        if (isset($arr['DeletedAt'])) $this->DeletedAt = $arr['DeletedAt'];
        if (isset($arr['InspectionNotes'])) $this->InspectionNotes = $arr['InspectionNotes'];
        if (isset($arr['Position'])) $this->Position = $arr['Position'];
   
        if (isset($this->ImageId) && $this->ImageId=='null') $this->ImageId = null;
        if (isset($this->DeletedAt) && $this->DeletedAt=='null') $this->DeletedAt = null;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing house in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_house SET Name=?, NumberOfBeds=?, PositionInVillage=?, Description=?, 
                ImageId=?, IsHouse=?, NotesToUsers=?, History=?, DeletedAt=?, InspectionNotes=?, Position=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->NumberOfBeds, $this->PositionInVillage, $this->Description, 
            $this->ImageId, $this->IsHouse, $this->NotesToUsers, $this->History, $this->DeletedAt, $this->InspectionNotes, $this->Position, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new house in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_house (Name, NumberOfBeds, PositionInVillage, Description, 
            IsHouse, NotesToUsers, History, DeletedAt, InspectionNotes, Position) VALUES (?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->Name, $this->NumberOfBeds, $this->PositionInVillage, $this->Description, 
            $this->IsHouse, $this->NotesToUsers, $this->History, $this->DeletedAt, $this->InspectionNotes, $this->Position))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function hasImage() {
        if (isset($this->ImageId)) return true;
        return false;
    }
 
    public function getImage() {
        if (empty($this->ImageId)) return null;
        return Image::loadById($this->ImageId);
    }
    
    public function IsHouse() {
        if ($this->IsHouse==1) return true;
        return false;
    }

    public function IsCamp() {
        return !$this->IsHouse();
    }
  
    
    public static function getAllHouses() {
        $sql = "SELECT * FROM regsys_house WHERE IsHouse=1 AND DeletedAt IS NULL ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array());
    }
    
    public static function getAllCamps() {
        $sql = "SELECT * FROM regsys_house WHERE IsHouse=0 AND DeletedAt IS NULL ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array());
        
    }
    
    public static function getHouseAtLarp(Person $person, Larp $larp) {
        $sql = "SELECT regsys_house.* FROM regsys_house, regsys_housing WHERE ".
            "regsys_house.Id = regsys_housing.HouseId AND ".
            "regsys_housing.LARPId=? AND ".
            "regsys_housing.PersonId=?";
        return static::getOneObjectQuery($sql, array($larp->Id, $person->Id));
    }

    public function getCaretakers() {
        $sql = "SELECT * FROM regsys_person WHERE Id IN (".
            "SELECT PersonId FROM regsys_housecaretaker WHERE HouseId=?) ORDER BY ".Person::$orderListBy.";";
        return Person::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    public static function housesOf(Person $person) {
        $sql = "SELECT * FROM regsys_house WHERE Id IN (
            SELECT HouseId FROM regsys_housecaretaker WHERE PersonId =?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id));
        
    }
    
    
}
    