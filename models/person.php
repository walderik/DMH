<?php

class Person extends BaseModel{
    
    public $Id;
    public $Name;
    public $SocialSecurityNumber;
    public $PhoneNumber;
    public $EmergencyContact;
    public $Email;
    public $FoodAllergiesOther;
    public $TypeOfLarperComment;
    public $OtherInformation;
    public $ExperienceId;
    public $TypeOfFoodId;
    public $LarperTypeId;
    public $UserId; # Användare som registrerat personen
    public $NotAcceptableIntrigues;
//     public $NormalAllergyTypesIds;
    
   
    
//     public static $tableName = 'persons';
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $person = static::newWithDefault();
        if (isset($post['Id']))   $person->Id = $post['Id'];
        if (isset($post['Name'])) $person->Name = $post['Name'];
        if (isset($post['SocialSecurityNumber'])) $person->SocialSecurityNumber = $post['SocialSecurityNumber'];
        if (isset($post['PhoneNumber'])) $person->PhoneNumber = $post['PhoneNumber'];
        if (isset($post['EmergencyContact'])) $person->EmergencyContact = $post['EmergencyContact'];
        if (isset($post['Email'])) $person->Email = $post['Email'];
        if (isset($post['FoodAllergiesOther'])) $person->FoodAllergiesOther = $post['FoodAllergiesOther'];
        if (isset($post['TypeOfLarperComment'])) $person->TypeOfLarperComment = $post['TypeOfLarperComment'];
        if (isset($post['IntrigueIdeas'])) $person->IntrigueIdeas = $post['IntrigueIdeas'];
        if (isset($post['OtherInformation'])) $person->OtherInformation = $post['OtherInformation'];
        if (isset($post['ExperienceId'])) $person->ExperienceId = $post['ExperienceId'];
        if (isset($post['TypeOfFoodId'])) $person->TypeOfFoodId = $post['TypeOfFoodId'];
        if (isset($post['LarperTypeId'])) $person->LarperTypeId = $post['LarperTypeId'];
        if (isset($post['UserId'])) $person->UserId = $post['UserId'];
        if (isset($post['NotAcceptableIntrigues'])) $person->NotAcceptableIntrigues = $post['NotAcceptableIntrigues'];
        
        
//         //Normal allergy types sparas i egen tabell eftersom de kan vara flera
//         if (isset($post['NormalAllergyTypesIds'])) $person->NormalAllergyTypesIds = $post['NormalAllergyType'];
        
        return $person;
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        Global $current_user;
        $person = new self();
        $person->UserId = $current_user->Id;
        return $person;
    }
    
    public static function getPersonsForUser($userId) {
        $sql = "SELECT * FROM ".strtolower(static::class)." WHERE UserId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($userId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
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
    
    # Update an existing person in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE ".strtolower(static::class)." SET Name=?, SocialSecurityNumber=?, PhoneNumber=?, EmergencyContact=?, Email=?,
                                                                  FoodAllergiesOther=?, TypeOfLarperComment=?, OtherInformation=?, ExperienceId=?,
                                                                  TypeOfFoodId=?, LarperTypeId=?, UserId=?, NotAcceptableIntrigues=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->SocialSecurityNumber, $this->PhoneNumber, $this->EmergencyContact, $this->Email,
            $this->FoodAllergiesOther, $this->TypeOfLarperComment, $this->OtherInformation, $this->ExperienceId,
            $this->TypeOfFoodId, $this->LarperTypeId, $this->UserId, $this->NotAcceptableIntrigues, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
        }
        $stmt = null;  
        
    }
    
    # Create a new person in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO ".strtolower(static::class)." (Name, SocialSecurityNumber, PhoneNumber, EmergencyContact, Email,
                                                                    FoodAllergiesOther, TypeOfLarperComment, OtherInformation, ExperienceId,
                                                                    TypeOfFoodId, LarperTypeId, UserId, NotAcceptableIntrigues) VALUES (?,?,?,?,?,?,?,?,?,?,?,?, ?)");
        
        if (!$stmt->execute(array($this->Name, $this->SocialSecurityNumber, $this->PhoneNumber, $this->EmergencyContact, $this->Email, 
                $this->FoodAllergiesOther, $this->TypeOfLarperComment, $this->OtherInformation, $this->ExperienceId, 
                $this->TypeOfFoodId, $this->LarperTypeId, $this->UserId, $this->NotAcceptableIntrigues))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    # Spara den här relationen
    public function saveAllNormalAllergyTypes(Array $normalAllergyTypeId) {
        foreach($normalAllergyTypeId as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO NormalAllergyType_Person (NormalAllergyTypeId, PersonId) VALUES (?,?)");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;           
    }

    
    public function deleteAllNormalAllergyTypes() {
        $stmt = $this->connect()->prepare("DELETE FROM NormalAllergyType_Person WHERE PersonId = ?'");
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function getExperience()
    {
        if (is_null($this->ExperienceId)) return null;
        return Experience::loadById($this->ExperienceId);
    }
    
    
    public function getTypeOfFood()
    {
        if (is_null($this->TypeOfFoodId)) return null;
        return TypeOfFood::loadById($this->TypeOfFoodId);
    }
    
    public function getLarperType()
    {
        if (is_null($this->LarperTypeId)) return null;
        return LarperType::loadById($this->LarperTypeId);
    }
    
    public function getNormalAllergyTypes()
    {
        $stmt = $this->connect()->prepare("SELECT * FROM NormalAllergyType_Person where PersonId = ? ORDER BY id;");
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = NormalAllergyType::loadById($row['NormalAllergyTypeId']);
        }
        $stmt = null;
        return $resultArray;
    }
    
    
    public function getUser()
    {
        return User::loadById($this->UserId);
    }
}