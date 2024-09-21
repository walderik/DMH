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
    public $Lat;
    public $Lon;
    
    //SELECT ST_X(MY_POINT) as latitude,  ST_Y(MY_POINT) as longitude  FROM MY_TABLE;
        
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
        if (isset($arr['Lat'])) $this->Lat = $arr['Lat'];
        if (isset($arr['Lon'])) $this->Lon = $arr['Lon'];
        
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
                ImageId=?, IsHouse=?, NotesToUsers=?, History=?, DeletedAt=?, InspectionNotes=?, Lat=?, Lon=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->NumberOfBeds, $this->PositionInVillage, $this->Description, 
            $this->ImageId, $this->IsHouse, $this->NotesToUsers, $this->History, $this->DeletedAt, $this->InspectionNotes, $this->Lat, $this->Lon, $this->Id))) {
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
            IsHouse, NotesToUsers, History, DeletedAt, InspectionNotes, Lat, Lon) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->Name, $this->NumberOfBeds, $this->PositionInVillage, $this->Description, 
            $this->IsHouse, $this->NotesToUsers, $this->History, $this->DeletedAt, $this->InspectionNotes, $this->Lat, $this->Lon))) {
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
    
    # Vilka är caretakers för det här huset? 
    # Från dessa kan personer plockas fram
    public function getHousecaretakers() {
        $sql = "SELECT * FROM regsys_housecaretaker WHERE HouseId=? ORDER BY ".Housecaretaker::$orderListBy.";";
        return Housecaretaker::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    # Plocka fram housecaretaker-objektet för en person i ett hus. Nil om det inte finns
    public function getHousecaretakerForPerson(Person $person) {
        $sql = "SELECT * FROM regsys_housecaretaker WHERE HouseId=? and PersonId=? ORDER BY ".Housecaretaker::$orderListBy.";";
        return Housecaretaker::getOneObjectQuery($sql, array($this->Id, $person->Id));
    }

    public function getCaretakerPersons() {
        $sql = "SELECT * FROM regsys_person WHERE Id IN (".
            "SELECT PersonId FROM regsys_housecaretaker WHERE HouseId=?) ORDER BY ".Person::$orderListBy.";";
        return Person::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    public function addCaretakerPerson(Person $person) {
        $caretaker = Housecaretaker::loadByIds($this->Id, $person->Id);
        if (empty($caretaker)) {
            $caretaker = Housecaretaker::newWithDefault();
            $caretaker->HouseId = $this->Id;
            $caretaker->PersonId = $person->Id;
            $caretaker->IsApproved = true;
            $caretaker->ContractSignedDate = null;
            $caretaker->create();
        }
        return $caretaker;
    }
    
    # Det finns en objectmetod på person som heter @person->housesOf
    public static function housesOf(Person $person) {
        $sql = "SELECT * FROM regsys_house WHERE Id IN (
            SELECT HouseId FROM regsys_housecaretaker WHERE PersonId =?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id));
        
    }
    
    # Är någon på något lajv tilldelad att bo i det här huset?
    # Används för att reda ut om det går att radera eller bara ska arkiveras
    public function hasHousing() {
        $sql = "SELECT * FROM regsys_housing WHERE HouseId = ? limit 1";
        $housing = Housing::getOneObjectQuery($sql, array($this->Id));
        if (is_null($housing)) return false;
        return true;
    }
    
    # Plocka fram alla som bott i huset
    public function getAllLarpsWithHousing() {
        $sql = "SELECT * FROM regsys_larp WHERE Id in 
            (SELECT LARPId FROM regsys_housing WHERE HouseId = ?) ORDER BY ".LARP::$orderListBy.";";
        return LARP::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    public function mayDelete() {
        if ($this->hasHousing()) return false;
        return true;
    }
    
}
    