<?php

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class Group extends BaseModel{
    
    public $Id;
    public $Name;
    public $ApproximateNumberOfMembers;
    public $NeedFireplace = false;
    public $Friends;
    public $Enemies;
    public $WantIntrigue = true;
    public $Description;
    public $IntrigueIdeas;
    public $OtherInformation;
    public $WealthsId;
    public $OriginsId;
    public $PersonsId; # Gruppledaen
    
    public static $tableName = 'groups';
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $group = static::newWithDefault();
        if (isset($post['Id'])) $group->Id = $post['Id'];
        if (isset($post['Name'])) $group->Name = $post['Name'];
        if (isset($post['ApproximateNumberOfMembers'])) $group->ApproximateNumberOfMembers = $post['ApproximateNumberOfMembers'];
        if (isset($post['NeedFireplace'])) $group->NeedFireplace = $post['NeedFireplace'];
        if (isset($post['Friends'])) $group->Friends = $post['Friends'];
        if (isset($post['Enemies'])) $group->Enemies = $post['Enemies'];
        if (isset($post['WantIntrigue'])) $group->WantIntrigue = $post['WantIntrigue'];
        if (isset($post['Description'])) $group->Description = $post['Description'];
        if (isset($post['IntrigueIdeas'])) $group->IntrigueIdeas = $post['IntrigueIdeas'];
        if (isset($post['OtherInformation'])) $group->OtherInformation = $post['OtherInformation'];
        if (isset($post['WealthsId'])) $group->WealthsId = $post['WealthsId'];
        if (isset($post['OriginsId'])) $group->OriginsId = $post['OriginsId'];
        if (isset($post['PersonsId'])) $group->PersonsId = $post['PersonsId'];
        
        return $group;
    }
     
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
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
    
    public function getWealth()
    {
        if (is_null($this->WealthsId)) return null;
        return Wealth::loadById($this->WealthsId);
    }
    
    public function getPlaceOfResidence()
    {
        if (is_null($this->OriginsId)) return null;
        return PlaceOfResidence::loadById($this->OriginsId);
    }
    
     public function getPerson()
     {
//         return Person::loadById($this->PersonsId);
    }
}