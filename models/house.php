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
        if (isset($post['Id'])) $house->Id = $post['Id'];
        if (isset($post['Name'])) $house->Name = $post['Name'];
        if (isset($post['NumberOfBeds'])) $house->NumberOfBeds = $post['NumberOfBeds'];
        if (isset($post['PositionInVillage'])) $house->PositionInVillage = $post['PositionInVillage'];
        if (isset($post['Description'])) $house->Description = $post['Description'];
        
        return $house;
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing house in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE house SET Name=?, NumberOfBeds=?, PositionInVillage=?, Description=? WHERE Id = ?");
        
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
        $stmt = $connection->prepare("INSERT INTO house (Name, NumberOfBeds, PositionInVillage, Description) VALUES (?,?,?,?)");
        
        if (!$stmt->execute(array($this->Name, $this->NumberOfBeds, $this->PositionInVillage, $this->Description))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    
    
}
    