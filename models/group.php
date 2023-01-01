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
        $group = group::newWithDefault();
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
        global $conn;
        
        $stmt = $conn->prepare("UPDATE ".static::$tableName." SET Name=?, ApproximateNumberOfMembers=?, NeedFireplace=?, Friends=?, Enemies=?, 
                                                                  WantIntrigue=?, Description=?, IntrigueIdeas=?, OtherInformation=?,
                                                                  WealthsId=?, OriginsId=?, PersonsId=? WHERE Id = ?");
        $stmt->bind_param("siississsiiii", $this->Name, $this->ApproximateNumberOfMembers, $this->NeedFireplace, $this->Friends, $this->Enemies, $this->WantIntrigue,
            $this->Description, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthsId, $this->OriginsId, $this->PersonsId, $this->Id);

        $stmt->execute();
    }
    
    # Create a new group in db
    public function create()
    {
        global $conn;
        
        $stmt = $conn->prepare("INSERT INTO ".static::$tableName." (Name, ApproximateNumberOfMembers, NeedFireplace, Friends, Enemies, 
                                                                    WantIntrigue, Description, IntrigueIdeas, OtherInformation, 
                                                                    WealthsId, OriginsId, PersonsId) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("siississsiii", $this->Name, $this->ApproximateNumberOfMembers, $this->NeedFireplace, $this->Friends, $this->Enemies, $this->WantIntrigue,
            $this->Description, $this->IntrigueIdeas, $this->OtherInformation, $this->WealthsId, $this->OriginsId, $this->PersonsId);

        $stmt->execute();
    }
    
    public function getWealth()
    {
        if (is_null($this->WealthsId)) return null;
        return Wealth::loadById($this->WealthsId);
    }
    
    public function getOrigin()
    {
        if (is_null($this->OriginsId)) return null;
        return Origin::loadById($this->OriginsId);
    }
    
//     public function getPerson()
//     {
//         return Person::loadById($this->PersonsId);
//     }
}

?>