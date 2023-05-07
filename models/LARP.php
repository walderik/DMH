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
        $larp->setValuesByArray($post);
        return $larp;
    }
     
     
    public function setValuesByArray($arr) {
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['TagLine'])) $this->TagLine = $arr['TagLine'];
        if (isset($arr['StartDate'])) $this->StartDate = $arr['StartDate'];
        if (isset($arr['EndDate'])) $this->EndDate = $arr['EndDate'];
        if (isset($arr['MaxParticipants'])) $this->MaxParticipants = $arr['MaxParticipants'];
        if (isset($arr['LatestRegistrationDate'])) $this->LatestRegistrationDate = $arr['LatestRegistrationDate'];
        if (isset($arr['StartTimeLARPTime'])) $this->StartTimeLARPTime = $arr['StartTimeLARPTime'];
        if (isset($arr['EndTimeLARPTime'])) $this->EndTimeLARPTime = $arr['EndTimeLARPTime'];
        if (isset($arr['DisplayIntrigues'])) $this->DisplayIntrigues = $arr['DisplayIntrigues'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['RegistrationOpen'])) $this->RegistrationOpen = $arr['RegistrationOpen'];        
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
    
    public function getAllRoles() {
        return Role::getAllRoles($this);
    }
    
    public function getAllMainRoles() {
        return Role::getAllMainRoles($this);
    }
    
    public function getAllNotMainRoles() {
        return Role::getAllNotMainRoles($this);
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
        //if ($this->isFull()) return false;
        if ($this->RegistrationOpen == 0) return false;
        return true;
    }
    
    public function hasRegistrations() {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LarpId=?;";
        
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
    
    public static function getPreviousLarps($roleId) {
        global $current_larp;
        if (is_null($roleId)) return Array();

        $sql = "SELECT * FROM regsys_larp WHERE Id IN (SELECT LarpId FROM regsys_larp_role WHERE RoleId = ? AND LarpId != ?) ORDER BY StartDate DESC";
        return static::getSeveralObjectsqQuery($sql, array($roleId, $current_larp->Id));
    }
    
    public static function allByCampaign($campaignId) {
        $sql = "SELECT * FROM regsys_larp WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($campaignId));
        
    }
        
}