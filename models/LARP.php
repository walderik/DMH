<?php

class LARP extends BaseModel{
    
    

    public  $Id; 
    public  $Name;
    public  $Abbreviation;
    public  $TagLine; 
    public  $StartDate; 
    public  $EndDate;
    public  $MaxParticipants; 
    public  $LatestRegistrationDate;
    public  $StartTimeLARPTime;
    public  $EndTimeLARPTime;

    
    public static $tableName = 'LARPs';
    public static $orderListBy = 'StartDate';
    
    public static function newFromArray($post){
        $larp = LARP::newWithDefault();
        if (isset($post['Name'])) $larp->Name = $post['Name'];
        if (isset($post['Abbreviation'])) $larp->Abbreviation = $post['Abbreviation'];
        if (isset($post['TagLine'])) $larp->TagLine = $post['TagLine'];
        if (isset($post['StartDate'])) $larp->StartDate = $post['StartDate'];
        if (isset($post['EndDate'])) $larp->EndDate = $post['EndDate'];
        if (isset($post['MaxParticipants'])) $larp->MaxParticipants = $post['MaxParticipants'];
        if (isset($post['LatestRegistrationDate'])) $larp->LatestRegistrationDate = $post['LatestRegistrationDate'];
        if (isset($post['StartTimeLARPTime'])) $larp->StartTimeLARPTime = $post['StartTimeLARPTime'];
        if (isset($post['EndTimeLARPTime'])) $larp->EndTimeLARPTime = $post['EndTimeLARPTime'];
        if (isset($post['Id'])) $larp->Id = $post['Id'];
        
        return $larp;
    }
     
     
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing larp in db
    public function update()
    {
        $stmt = $this->connect()->prepare("UPDATE ".static::$tableName." SET Name=?, Abbreviation=?, TagLine=?, StartDate=?, EndDate=?, MaxParticipants=?, LatestRegistrationDate=?, StartTimeLARPTime=?, EndTimeLARPTime=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Abbreviation, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate, 
            $this->StartTimeLARPTime, $this->EndTimeLARPTime, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    # Create a new larp in db
    public function create()
    {
        $stmt = $this->connect()->prepare("INSERT INTO ".static::$tableName." (Name, Abbreviation, TagLine, StartDate, EndDate, MaxParticipants, LatestRegistrationDate, StartTimeLARPTime, EndTimeLARPTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Name, $this->Abbreviation, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate,
            $this->StartTimeLARPTime, $this->EndTimeLARPTime))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
        }
            
        $stmt = null;
    }
    
      
}