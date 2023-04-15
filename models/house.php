<?php

class House extends BaseModel{
    
    public $Id;
    public $Name;
    public $NumberOfBeds;
    public $PositionInVillage;
    public $Description;
    
    public static $orderListBy = 'Name';
    
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
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing house in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_house SET Name=?, NumberOfBeds=?, PositionInVillage=?, Description=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->NumberOfBeds, $this->PositionInVillage, $this->Description, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new house in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_house (Name, NumberOfBeds, PositionInVillage, Description) VALUES (?,?,?,?)");
        
        if (!$stmt->execute(array($this->Name, $this->NumberOfBeds, $this->PositionInVillage, $this->Description))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    
    
}
    