<?php

class LARP extends BaseModel{

    public  $Id; 
    public  $Name;
    public  $TagLine; 
    public  $StartDate; 
    public  $EndDate;
    public  $MaxParticipants; 
    public  $LatestRegistrationDate;
    public  $StartTimeLARPTime;
    public  $EndTimeLARPTime;
    public  $DisplayIntrigues = 0;
    public  $CampaignId;
    public  $RegistrationOpen = 0;

    
//     public static $tableName = 'larp';
    public static $orderListBy = 'StartDate';
    
    public static function newFromArray($post){
        $larp = static::newWithDefault();
        if (isset($post['Name'])) $larp->Name = $post['Name'];
        if (isset($post['TagLine'])) $larp->TagLine = $post['TagLine'];
        if (isset($post['StartDate'])) $larp->StartDate = $post['StartDate'];
        if (isset($post['EndDate'])) $larp->EndDate = $post['EndDate'];
        if (isset($post['MaxParticipants'])) $larp->MaxParticipants = $post['MaxParticipants'];
        if (isset($post['LatestRegistrationDate'])) $larp->LatestRegistrationDate = $post['LatestRegistrationDate'];
        if (isset($post['StartTimeLARPTime'])) $larp->StartTimeLARPTime = $post['StartTimeLARPTime'];
        if (isset($post['EndTimeLARPTime'])) $larp->EndTimeLARPTime = $post['EndTimeLARPTime'];
        if (isset($post['DisplayIntrigues'])) $larp->DisplayIntrigues = $post['DisplayIntrigues'];
        if (isset($post['Id'])) $larp->Id = $post['Id'];
        if (isset($post['CampaignId'])) $larp->CampaignId = $post['CampaignId'];
        if (isset($post['RegistrationOpen'])) $larp->RegistrationOpen = $post['RegistrationOpen'];
        
        return $larp;
    }
     
     
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing larp in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_larp SET Name=?, TagLine=?, StartDate=?, EndDate=?, MaxParticipants=?, LatestRegistrationDate=?, StartTimeLARPTime=?, EndTimeLARPTime=?, DisplayIntrigues=?, CampaignId=?, RegistrationOpen=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate, 
            $this->StartTimeLARPTime, $this->EndTimeLARPTime, $this->DisplayIntrigues, $this->CampaignId, $this->RegistrationOpen, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    # Create a new larp in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_larp (Name, TagLine, StartDate, EndDate, MaxParticipants, 
            LatestRegistrationDate, StartTimeLARPTime, EndTimeLARPTime, DisplayIntrigues, CampaignId, RegistrationOpen) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Name, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate,
            $this->StartTimeLARPTime, $this->EndTimeLARPTime, $this->DisplayIntrigues, $this->CampaignId, $this->RegistrationOpen))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getCampaign() {
        return Campaign::loadById($this->CampaignId);
    }
    public function isPastLatestRegistrationDate() {
        $today = date("Y-m-d");
        if ($today <= $this->LatestRegistrationDate) return false;
        return true;
    }
    
    public function isEnded() {
        $now = date("Y-m-d H:i:s");
        if ($now < $this->EndDate) return false;
        return true;
        
    }
    
    public function isFull() {
        $number = Registration::countAllNonOfficials($this);
        if ($number >= $this->MaxParticipants) return true;
        else return false;
    }
    
    public function mayRegister() {
        if ($this->isPastLatestRegistrationDate()) return false;
        if ($this->isFull()) return false;
        if ($this->RegistrationOpen == 0) return false;
        return true;
    }
    
    public static function allFutureOpenLARPs() {
        $sql = "SELECT * FROM regsys_larp WHERE StartDate >= CURDATE() AND RegistrationOpen=1 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, null);
    }
    
    public static function allFutureNotYetOpenLARPs() {
        $sql = "SELECT * FROM regsys_larp WHERE StartDate >= CURDATE() AND RegistrationOpen=0 ".
            "AND LatestRegistrationDate >= CURDATE() ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, null);
    }
    
    public static function allPastLarpsWithRegistrations(User $user) {
        $sql = "SELECT * FROM regsys_larp WHERE StartDate <= CURDATE() AND Id IN ".
            "(SELECT DISTINCT regsys_registration.LARPId FROM regsys_person, regsys_registration WHERE ".
            "regsys_person.id = regsys_registration.PersonId AND regsys_person.UserId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($user->Id));
    }
        
    public static function lastLarp(Role $role) {
        $sql="SELECT * FROM regsys_larp WHERE Id IN ".
            "(SELECT LarpId from regsys_larp_role WHERE RoleId=?) ".
            "ORDER BY StartDate DESC";
        return static::getOneObjectQuery($sql, array($role->Id));
    }
        
}