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
    public  $SwishNumber;
    public  $MinimumAge;
    public  $MinimumAgeWithoutGuardian;
    public  $Currency;
    public  $MainOrganizerPersonId;

    
    
    //     public static $tableName = 'campaign';
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $campaign = static::newWithDefault();
        $campaign->setValuesByArray($post);
        return $campaign;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Abbreviation'])) $this->Abbreviation = $arr['Abbreviation'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['Icon'])) $this->Icon = $arr['Icon'];
        if (isset($arr['Homepage'])) $this->Homepage = $arr['Homepage'];
        if (isset($arr['Email'])) $this->Email = $arr['Email'];
        if (isset($arr['Bankaccount'])) $this->Bankaccount = $arr['Bankaccount'];
        if (isset($arr['SwishNumber'])) $this->SwishNumber = $arr['SwishNumber'];
        if (isset($arr['MinimumAge'])) $this->MinimumAge = $arr['MinimumAge'];
        if (isset($arr['MinimumAgeWithoutGuardian'])) $this->MinimumAgeWithoutGuardian = $arr['MinimumAgeWithoutGuardian'];
        if (isset($arr['Currency'])) $this->Currency = $arr['Currency'];
        if (isset($arr['MainOrganizerPersonId'])) $this->MainOrganizerPersonId = $arr['MainOrganizerPersonId'];
        
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        
        
    }
    
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing campaign in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_campaign SET Name=?, Abbreviation=?, Description=?, Icon=?, 
            Homepage=?, Email=?, Bankaccount=?, SwishNumber=?, MinimumAge=?, MinimumAgeWithoutGuardian=?, 
            Currency=?, MainOrganizerPersonId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Abbreviation, $this->Description, $this->Icon,
            $this->Homepage, $this->Email, $this->Bankaccount, $this->SwishNumber, $this->MinimumAge, $this->MinimumAgeWithoutGuardian, 
            $this->Currency, $this->MainOrganizerPersonId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    # Create a new campaign in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_campaign (Name, Abbreviation, Description, Icon, Homepage, Email, Bankaccount, SwishNumber, 
            MinimumAge, MinimumAgeWithoutGuardian, Currency) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Name, $this->Abbreviation, $this->Description, $this->Icon,
            $this->Homepage, $this->Email, $this->Bankaccount, $this->SwishNumber, $this->MinimumAge, $this->MinimumAgeWithoutGuardian, $this->Currency))) {
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
    public function is_doh() {
        return ($this->Abbreviation=='DÖH');
    }
    
    # Är det här Kampen i Ringen
    public function is_kir() {
        return ($this->Abbreviation=='KIR');
    }
    
    # Är det här Mareld
    public function is_me() {
        return ($this->Abbreviation=='ME');
    }
    
    public function getMainOrganizer() {
        if (empty($this->MainOrganizerPersonId)) return NULL;
        return Person::loadById($this->MainOrganizerPersonId);
    }
    
    public function hasLarps() {

        $sql = "SELECT COUNT(*) AS Num FROM regsys_larp WHERE CampaignId=?;";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return false;
            
        }
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = null;
        
        
        if ($res[0]['Num'] == 0) return false;
        return true;
        

        
        
        
    }
    
    public static function organizerForCampaigns(User $user) {
        $sql="SELECT * FROM regsys_campaign WHERE Id IN ".
        "(SELECT CampaignId FROM regsys_access_control_campaign, regsys_person WHERE ".
        "regsys_person.UserId = ? AND ".
        "regsys_access_control_campaign.PersonId = regsys_person.Id) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($user->Id));
    }
    
    public function isMainOrganizer(User $user) {
        $mainOrganizer = $this->getMainOrganizer();
        if (isset($mainOrganizer) && ($user->Id == $mainOrganizer->UserId)) return true;
        return false;
    }
    
    
}