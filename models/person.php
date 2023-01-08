<?php

class Group extends BaseModel{
    
    public $Id;
    public $Name;
    public $SocialSecurityNumber;
    public $PhoneNumber;
    public $EmergencyContact;
    public $Email;
    public $FoodAllergiesOther;
    public $TypeOfLarperComment;
    public $OtherInformation;
    public $ExperiencesId;
    public $TypesOfFoodId;
    public $LarperTypesId;
    public $UsersId; # Användare som registrerat personen
    public $NotAcceptableIntrigues;
    public $NormalAllergyTypesIds;
    
   
    
    public static $tableName = 'persons';
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $person = static::newWithDefault();
        if (isset($post['Id'])) $person->Id = $post['Id'];
        if (isset($post['Name'])) $person->Name = $post['Name'];
        if (isset($post['SocialSecurityNumber'])) $person->SocialSecurityNumber = $post['SocialSecurityNumber'];
        if (isset($post['PhoneNumber'])) $person->PhoneNumber = $post['PhoneNumber'];
        if (isset($post['EmergencyContact'])) $person->EmergencyContact = $post['EmergencyContact'];
        if (isset($post['Email'])) $person->Email = $post['Email'];
        if (isset($post['FoodAllergiesOther'])) $person->FoodAllergiesOther = $post['FoodAllergiesOther'];
        if (isset($post['TypeOfLarperComment'])) $person->TypeOfLarperComment = $post['TypeOfLarperComment'];
        if (isset($post['IntrigueIdeas'])) $person->IntrigueIdeas = $post['IntrigueIdeas'];
        if (isset($post['OtherInformation'])) $person->OtherInformation = $post['OtherInformation'];
        if (isset($post['ExperiencesId'])) $person->ExperiencesId = $post['ExperiencesId'];
        if (isset($post['TypesOfFoodId'])) $person->TypesOfFoodId = $post['TypesOfFoodId'];
        if (isset($post['LarperTypesId'])) $person->LarperTypesId = $post['LarperTypesId'];
        if (isset($post['UsersId'])) $person->UsersId = $post['UsersId'];
        if (isset($post['NotAcceptableIntrigues'])) $person->NotAcceptableIntrigues = $post['NotAcceptableIntrigues'];
        
        
        //Normal allergy types sparas i egen tabell eftersom de kan vara flera
        if (isset($post['NormalAllergyTypesIds'])) $person->NormalAllergyTypesIds = $post['NormalAllergyType'];
        
        return $person;
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    //TODO
    # Update an existing group in db
    public function update()
    {
        
        $stmt = $this->connect()->prepare("UPDATE ".static::$tableName." SET Name=?, ApproximateNumberOfMembers=?, NeedFireplace=?, Friends=?, Enemies=?,
                                                                  WantIntrigue=?, Description=?, IntrigueIdeas=?, OtherInformation=?,
                                                                  WealthsId=?, OriginsId=?, PersonsId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->ApproximateNumberOfMembers, $this->NeedFireplace, $this->Friends, $this->Enemies, $this->WantIntrigue,
            $this->Description, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthsId, $this->OriginsId, $this->PersonsId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    //TODO
    # Create a new group in db
    public function create()
    {
        $stmt = $this->connect()->prepare("INSERT INTO ".static::$tableName." (Name, ApproximateNumberOfMembers, NeedFireplace, Friends, Enemies,
                                                                    WantIntrigue, Description, IntrigueIdeas, OtherInformation,
                                                                    WealthsId, OriginsId, PersonsId) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->Name, $this->ApproximateNumberOfMembers, $this->NeedFireplace, $this->Friends, $this->Enemies, $this->WantIntrigue,
            $this->Description, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthsId, $this->OriginsId, $this->PersonsId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    public function getExperience()
    {
        if (is_null($this->ExperiencesId)) return null;
        return Experience::loadById($this->ExperiencesId);
    }
    
    
    public function getTypeOfFood()
    {
        if (is_null($this->TypesOfFoodId)) return null;
        return TypeOfFood::loadById($this->TypesOfFoodId);
    }
    
    public function getLarperType()
    {
        if (is_null($this->LarperTypesId)) return null;
        return LarperType::loadById($this->LarperTypesId);
    }
    
    public function getNormalAllergyType()
    {
        if (is_null($this->NormalAllergyTypesIds) or empty($this->NormalAllergyTypesIds)) return null;
        $AllergyTypes = array();
        foreach($this->NormalAllergyTypesIds as $Id) {
            $AllergyTypes[] = NormalAllergyType::loadById($Id);
        }
        return $AllergyTypes;
    }
    
    public function getUser()
    {
        //         return User::loadById($this->UsersId);
    }
}