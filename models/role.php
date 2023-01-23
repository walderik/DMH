<?php

class Role extends BaseModel{
    
    public $Id;
    public $Name;
    public $IsNPC;
    public $Profession;
    public $Description;
    public $PreviousLarps;
    public $ReasonForBeingInSlowRiver;
    public $Religion;
    public $DarkSecret;
    public $DarkSecretIntrigueIdeas;
    public $IntrigueSuggestions;
    public $NotAcceptableIntrigues;
    public $OtherInformation; 
    public $PersonId;
    public $GroupId;
    public $WealthId;
    public $PlaceOfResidenceId;
    public $Photo;
    public $Birthplace;
    public $CharactersWithRelations;
    

    
    public static $orderListBy = 'Name';
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    
    # Update an existing role in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE ".strtolower(static::class)." SET Name=?, IsNPC=?, Profession=?, Description=?,
                                                                  PreviousLarps=?, ReasonForBeingInSlowRiver=?, Religion=?, DarkSecret=?,
                                                                  DarkSecretIntrigueIdeas=?, IntrigueSuggestions=?, NotAcceptableIntrigues=?, OtherInformation=?,
                                                                  PersonId=?, GroupId=?, WealthId=?, PlaceOfResidenceId=?, Photo=?, Birthplace=?, 
                                                                  CharactersWithRelations=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->IsNPC, $this->Profession, $this->Description, $this->PreviousLarps,
            $this->ReasonForBeingInSlowRiver, $this->Religion, $this->DarkSecret, $this->DarkSecretIntrigueIdeas,
            $this->IntrigueSuggestions, $this->NotAcceptableIntrigues, $this->OtherInformation, $this->PersonId, 
            $this->GroupId, $this->WealthId, $this->PlaceOfResidenceId, $this->Photo,
            $this->Birthplace, $this->CharactersWithRelations, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
            
    }
    
    # Create a new role in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO ".strtolower(static::class)." (Name, IsNPC, Profession, Description, PreviousLarps,
                                                                    ReasonForBeingInSlowRiver, Religion, DarkSecret, DarkSecretIntrigueIdeas,
                                                                    GroupId, WealthId, PlaceOfResidenceId, Photo,
                                                                    Birthplace, CharactersWithRelations) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->IsNPC, $this->Profession, $this->Description, $this->PreviousLarps,
            $this->ReasonForBeingInSlowRiver, $this->Religion, $this->DarkSecret, $this->DarkSecretIntrigueIdeas,
            $this->IntrigueSuggestions, $this->NotAcceptableIntrigues, $this->OtherInformation, $this->PersonId,
            $this->GroupId, $this->WealthId, $this->PlaceOfResidenceId, $this->Photo,
            $this->Birthplace, $this->CharactersWithRelations))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    
    
    
    
    public static function getRolesForPerson($personId) {
        $sql = "SELECT * FROM ".strtolower(static::class)." WHERE PersonId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($personId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }
    

}