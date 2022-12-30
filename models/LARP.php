<?php

include_once 'includes/db.inc.php';
include 'models/base_model.php';

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

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
        global $conn;
        
        $stmt = $conn->prepare("UPDATE ".static::$tableName." SET Name=?, Abbreviation=?, TagLine=?, StartDate=?, EndDate=?, MaxParticipants=?, LatestRegistrationDate=?, StartTimeLARPTime=?, EndTimeLARPTime=? WHERE id = ?");
        $stmt->bind_param("sssssisssi", $Name, $Abbreviation, $TagLine, $StartDate, $EndDate, $MaxParticipants, $LatestRegistrationDate, $StartTimeLARPTime, $EndTimeLARPTime, $Id);
        
        // set parameters and execute
        $Id = $this->Id;
        $Name = $this->Name;
        $Abbreviation = $this->Abbreviation;
        $TagLine = $this->TagLine;
        $StartDate = $this->StartDate;
        $EndDate = $this->EndDate;
        $MaxParticipants = $this->MaxParticipants;
        $LatestRegistrationDate = $this->LatestRegistrationDate;
        $StartTimeLARPTime = $this->StartTimeLARPTime;
        $EndTimeLARPTime = $this->EndTimeLARPTime;
        $stmt->execute();        
    }
    
    # Create a new larp in db
    public function create()
    {
        global $conn;
        
        $stmt = $conn->prepare("INSERT INTO ".static::$tableName." (Name, Abbreviation, TagLine, StartDate, EndDate, MaxParticipants, LatestRegistrationDate, StartTimeLARPTime, EndTimeLARPTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssisss", $Name, $Abbreviation, $TagLine, $StartDate, $EndDate, $MaxParticipants, $LatestRegistrationDate, $StartTimeLARPTime, $EndTimeLARPTime);
        
        // set parameters and execute
        $Name = $this->Name;
        $Abbreviation = $this->Abbreviation;
        $TagLine = $this->TagLine;
        $StartDate = $this->StartDate;
        $EndDate = $this->EndDate;
        $MaxParticipants = $this->MaxParticipants;
        $LatestRegistrationDate = $this->LatestRegistrationDate;
        $StartTimeLARPTime = $this->StartTimeLARPTime;
        $EndTimeLARPTime = $this->EndTimeLARPTime;
        $stmt->execute();
    }
    
      
}

?>