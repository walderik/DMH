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
        
        return $larp;
    }
     
     
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing larp in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE ".strtolower(static::class)." SET Name=?, TagLine=?, StartDate=?, EndDate=?, MaxParticipants=?, LatestRegistrationDate=?, StartTimeLARPTime=?, EndTimeLARPTime=?, DisplayIntrigues=?, CampaignId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate, 
            $this->StartTimeLARPTime, $this->EndTimeLARPTime, $this->DisplayIntrigues, $this->CampaignId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    # Create a new larp in db
    public function create() {
        //print_r($this);
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO ".strtolower(static::class)." (Name, TagLine, StartDate, EndDate, MaxParticipants, 
            LatestRegistrationDate, StartTimeLARPTime, EndTimeLARPTime, DisplayIntrigues, CampaignId) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Name, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate,
            $this->StartTimeLARPTime, $this->EndTimeLARPTime, $this->DisplayIntrigues, $this->CampaignId))) {
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
}