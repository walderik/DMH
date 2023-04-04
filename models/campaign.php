<?php

class Campaign extends BaseModel{
    
    
    
    public  $Id;
    public  $Name;
    public  $Abbreviation;
    public  $Description;
    public  $Icon;
    public  $Homepage;
    public  $Email;
    public  $Bankaccount;
    public  $MinimumAge;
    public  $MinimumAgeWithoutGuardian;

    
    
    //     public static $tableName = 'campaign';
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $campaign = static::newWithDefault();
        if (isset($post['Name'])) $campaign->Name = $post['Name'];
        if (isset($post['Abbreviation'])) $campaign->Abbreviation = $post['Abbreviation'];
        if (isset($post['Description'])) $campaign->Description = $post['Description'];
        if (isset($post['Icon'])) $campaign->Icon = $post['Icon'];
        if (isset($post['Homepage'])) $campaign->Homepage = $post['Homepage'];
        if (isset($post['Email'])) $campaign->Email = $post['Email'];
        if (isset($post['Bankaccount'])) $campaign->Bankaccount = $post['Bankaccount'];
        if (isset($post['MinimumAge'])) $campaign->MinimumAge = $post['MinimumAge'];
        if (isset($post['MinimumAgeWithoutGuardian'])) $campaign->MinimumAgeWithoutGuardian = $post['MinimumAgeWithoutGuardian'];

        if (isset($post['Id'])) $campaign->Id = $post['Id'];

        
        return $campaign;
    }
    
    
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing campaign in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_campaign SET Name=?, Abbreviation=?, Description=?, Icon=?, Homepage=?, Email=?, Bankaccount=?, MinimumAge=?, MinimumAgeWithoutGuardian=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Abbreviation, $this->Description, $this->Icon,
            $this->Homepage, $this->Email, $this->Bankaccount, $this->MinimumAge, $this->MinimumAgeWithoutGuardian, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    # Create a new campaign in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_campaign (Name, Abbreviation, Description, Icon, Homepage, Email, Bankaccount, MinimumAge, MinimumAgeWithoutGuardian) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Name, $this->Abbreviation, $this->Description, $this->Icon,
            $this->Homepage, $this->Email, $this->Bankaccount, $this->MinimumAge, $this->MinimumAgeWithoutGuardian))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    
    public static function loadByAbbreviation($abbreviation) {
    
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $sql = "SELECT * FROM regsys_campaign WHERE Abbreviation = ?";
        return static::getOneObjectQuery($sql, array($abbreviation));
     }
    
    public function hej() {
        if ($this->is_dmh()) return "Howdy";
        return "Hej";
    }
    
    # Är det här Död Mans Hand
    public function is_dmh() {
        return ($this->Abbreviation=='DMH');
    }

    # Är det här Domen Över Hjorvard
    public function is_domen() {
        return ($this->Abbreviation=='DÖH');
    }
    
    # Är det här Kampen i Ringen
    public function is_kir() {
        return ($this->Abbreviation=='KIR');
    }
    
}