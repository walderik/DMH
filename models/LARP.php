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
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("UPDATE ".$tbl_prefix.strtolower(static::class)." SET Name=?, TagLine=?, StartDate=?, EndDate=?, MaxParticipants=?, LatestRegistrationDate=?, StartTimeLARPTime=?, EndTimeLARPTime=?, DisplayIntrigues=?, CampaignId=?, RegistrationOpen=? WHERE Id = ?");
        
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
        global $tbl_prefix;
        //print_r($this);
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO ".$tbl_prefix.strtolower(static::class)." (Name, TagLine, StartDate, EndDate, MaxParticipants, 
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
    public function pastLatestRegistrationDate() {
        $today = date("Y-m-d H:i:s");
        if ($today < $this->LatestRegistrationDate) return false;
        return true;
    }
    
    public function isFull() {
        $registrations = Registration::allBySelectedLARP($this);
        if (count($registrations) >= $this->MaxParticipants) return true;
        else return false;
    }
    
    public function mayRegister() {
        if ($this->pastLatestRegistrationDate()) return false;
        if ($this->isFull()) return false;
        if ($this->RegistrationOpen == 0) return false;
        return true;
    }
    
    public static function allFutureOpenLARPs() {
            global $tbl_prefix;

            $sql = "SELECT * FROM `".$tbl_prefix."LARP` WHERE StartDate >= CURDATE() AND RegistrationOpen=1 ORDER BY ".static::$orderListBy.";";
            $stmt = static::connectStatic()->prepare($sql);
            
            if (!$stmt->execute()) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
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
        
        public static function allFutureNotYetOpenLARPs() {
            global $tbl_prefix;
            
            $sql = "SELECT * FROM `".$tbl_prefix."LARP` WHERE StartDate >= CURDATE() AND RegistrationOpen=0 AND LatestRegistrationDate >= CURDATE() ORDER BY ".static::$orderListBy.";";
            $stmt = static::connectStatic()->prepare($sql);
            
            if (!$stmt->execute()) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
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
        
        public static function allPastLarpsWithRegistrations(User $user) {
            global $tbl_prefix;
            
            $sql = "SELECT * FROM `".$tbl_prefix."LARP` WHERE StartDate <= CURDATE() AND Id IN (SELECT DISTINCT ".$tbl_prefix."registration.LARPId FROM ".$tbl_prefix."person, ".$tbl_prefix."registration WHERE ".$tbl_prefix."person.id = ".$tbl_prefix."registration.PersonId AND ".$tbl_prefix."person.UserId = ?) ORDER BY ".static::$orderListBy.";";
            $stmt = static::connectStatic()->prepare($sql);
            
            if (!$stmt->execute(array($user->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
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
        
        
        
}