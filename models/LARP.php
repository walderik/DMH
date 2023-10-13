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
    public  $DisplayHousing = 0;
    public  $CampaignId;
    public  $RegistrationOpen = 0;
    public  $PaymentReferencePrefix = "";
    public  $NetDays = 0;
    public  $HasTelegrams = 0;
    public  $HasLetters = 1;
    public  $HasRumours = 1;

    
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
        if (isset($arr['DisplayHousing'])) $this->DisplayHousing = $arr['DisplayHousing'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['RegistrationOpen'])) $this->RegistrationOpen = $arr['RegistrationOpen'];        
        if (isset($arr['PaymentReferencePrefix'])) $this->PaymentReferencePrefix = $arr['PaymentReferencePrefix'];
        if (isset($arr['NetDays'])) $this->NetDays = $arr['NetDays'];
        if (isset($arr['HasTelegrams'])) $this->HasTelegrams = $arr['HasTelegrams'];
        if (isset($arr['HasLetters'])) $this->HasLetters = $arr['HasLetters'];
        if (isset($arr['HasRumours'])) $this->HasRumours = $arr['HasRumours'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $larp = new self();
        $larp->CampaignId = $current_larp->CampaignId;
        
        return $larp;
    }
    
    # Update an existing larp in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_larp SET Name=?, TagLine=?, StartDate=?, EndDate=?, ".
                 "MaxParticipants=?, LatestRegistrationDate=?, StartTimeLARPTime=?, EndTimeLARPTime=?, ".
                 "DisplayIntrigues=?, DisplayHousing=?, CampaignId=?, RegistrationOpen=?, PaymentReferencePrefix=?, NetDays=?, ".
                 "HasTelegrams=?, HasLetters=?, HasRumours=?  WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate, 
            $this->StartTimeLARPTime, $this->EndTimeLARPTime, $this->DisplayIntrigues, $this->DisplayHousing, $this->CampaignId, 
            $this->RegistrationOpen, $this->PaymentReferencePrefix, $this->NetDays, $this->HasTelegrams, $this->HasLetters, $this->HasRumours, $this->Id))) {
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
            LatestRegistrationDate, StartTimeLARPTime, EndTimeLARPTime, DisplayIntrigues, DisplayHousing, CampaignId, 
            RegistrationOpen, PaymentReferencePrefix, NetDays, HasTelegrams, HasLetters, HasRumours) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Name, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate,
            $this->StartTimeLARPTime, $this->EndTimeLARPTime, $this->DisplayIntrigues, $this->DisplayHousing, $this->CampaignId, 
            $this->RegistrationOpen, $this->PaymentReferencePrefix, $this->NetDays, $this->HasTelegrams, $this->HasLetters, $this->HasRumours))) {
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
    
    public function getAllMainRoles($includeNotComing) {
        return Role::getAllMainRoles($this, $includeNotComing);
    }
    
    public function getAllNotMainRoles($includeNotComing) {
        return Role::getAllNotMainRoles($this, $includeNotComing);
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
    
    # Har intrigerna släppts för det här lajvet
    public function isIntriguesReleased() {
        return $this->DisplayIntrigues == 1;
    }
    
    public function isHousingReleased() {
        return $this->DisplayHousing == 1;
    }
    
    public function hasTelegrams() {
        return $this->HasTelegrams == 1;
    }
    
    
    public function hasLetters() {
        return $this->HasLetters == 1;
    }
    
    
    public function hasRumours() {
        return $this->HasRumours == 1;
    }
    
    
    public function mayRegister() {
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
    

    public static function allFutureLARPs() {
        $sql = "SELECT * FROM regsys_larp WHERE StartDate >= CURDATE() ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, null);
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

    public static function getPreviousLarpsInCampaign(LARP $larp) {
        $sql = "SELECT * FROM regsys_larp WHERE CampaignId = ? AND StartDate < ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId, $larp->StartDate));
        
    }
    
}