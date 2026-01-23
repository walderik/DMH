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
    public  $ShowGroupMemberHousingInormation;
    public  $HasGroups = 1;
    public  $ParticipantsMayCreateGroups = 1;

    
    
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
        if (isset($arr['ShowGroupMemberHousingInormation'])) $this->ShowGroupMemberHousingInormation = $arr['ShowGroupMemberHousingInormation'];
        if (isset($arr['HasGroups'])) $this->HasGroups = $arr['HasGroups'];
        if (isset($arr['ParticipantsMayCreateGroups'])) $this->ParticipantsMayCreateGroups = $arr['ParticipantsMayCreateGroups'];
        
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
            Currency=?, MainOrganizerPersonId=?, ShowGroupMemberHousingInormation=?, HasGroups=?, ParticipantsMayCreateGroups=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Abbreviation, $this->Description, $this->Icon,
            $this->Homepage, $this->Email, $this->Bankaccount, $this->SwishNumber, $this->MinimumAge, $this->MinimumAgeWithoutGuardian, 
            $this->Currency, $this->MainOrganizerPersonId, $this->ShowGroupMemberHousingInormation, $this->HasGroups, $this->ParticipantsMayCreateGroups, $this->Id))) {
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
            MinimumAge, MinimumAgeWithoutGuardian, Currency, ShowGroupMemberHousingInormation, HasGroups, ParticipantsMayCreateGroups) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)");
        
        if (!$stmt->execute(array($this->Name, $this->Abbreviation, $this->Description, $this->Icon,
            $this->Homepage, $this->Email, $this->Bankaccount, $this->SwishNumber, $this->MinimumAge, $this->MinimumAgeWithoutGuardian, $this->Currency, $this->ShowGroupMemberHousingInormation,
            $this->HasGroups, $this->ParticipantsMayCreateGroups  
        ))) {
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
    
    # Är det här Handbok för superhjältar
    public function is_hfs() {
        return (strtoupper($this->Abbreviation)=='HFS');
    }
    
    public function getMainOrganizer() {
        if (empty($this->MainOrganizerPersonId)) return NULL;
        return Person::loadById($this->MainOrganizerPersonId);
    }
    
    public function showGroupMemberHousingInormation() {
        return $this->ShowGroupMemberHousingInormation == 1;
    }

    public function hasGroups() {
        return $this->HasGroups == 1;
    }
    
    public function participantsMayCreateGroups() {
        return $this->ParticipantsMayCreateGroups == 1;
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
    
    public static function organizerForCampaigns(Person $person) {
        $sql="SELECT * FROM regsys_campaign WHERE Id IN ".
        "(SELECT CampaignId FROM regsys_access_control_campaign WHERE ".
        "regsys_access_control_campaign.PersonId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id));
    }
    
    public function isMainOrganizer(Person $person) {
        $mainOrganizer = $this->getMainOrganizer();
        if (isset($mainOrganizer) && ($person->Id == $mainOrganizer->Id)) return true;
        return false;
    }
    
    
}