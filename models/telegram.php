<?php

class Telegram extends BaseModel{
    
    public  $Id;
    public  $Deliverytime;
    public  $Sender;
    public  $SenderCity = 'Junk City';
    public  $Reciever;
    public  $RecieverCity = 'Slow River';
    public  $Message;
    public  $OrganizerNotes;
    public  $LARPid;
    
//     public static $tableName = 'telegrams';
    public static $orderListBy = 'Deliverytime';
    
    public static function newFromArray($post) {
        $telegram = static::newWithDefault();
        if (isset($post['Deliverytime'])) $telegram->Deliverytime = $post['Deliverytime'];
        if (isset($post['Sender'])) $telegram->Sender = $post['Sender'];
        if (isset($post['SenderCity'])) $telegram->SenderCity = $post['SenderCity'];
        if (isset($post['Reciever'])) $telegram->Reciever = $post['Reciever'];
        if (isset($post['RecieverCity'])) $telegram->RecieverCity = $post['RecieverCity'];
        if (isset($post['Message'])) $telegram->Message = $post['Message'];
        if (isset($post['OrganizerNotes'])) $telegram->OrganizerNotes = $post['OrganizerNotes'];
        if (isset($post['Id'])) $telegram->Id = $post['Id'];
        if (isset($post['LARPid'])) $telegram->LARPid = $post['LARPid'];
        
        return $telegram;
    }
     
     
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $telegram = new self();
        $telegram->Deliverytime = $current_larp->StartTimeLARPTime;
        $telegram->LARPid = $current_larp->Id;
        return $telegram;
    }
    
    
    public static function allBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_telegram WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    
    # Update an existing telegram in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_telegram SET Deliverytime=?, Sender=?, SenderCity=?, Reciever=?, RecieverCity=?, Message=?, OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Deliverytime, $this->Sender, $this->SenderCity, $this->Reciever, $this->RecieverCity, $this->Message, $this->OrganizerNotes, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
    }
    
    # Create a new telegram in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_telegram (Deliverytime, Sender, SenderCity, Reciever, RecieverCity, Message, OrganizerNotes, LARPid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Deliverytime, $this->Sender, $this->SenderCity, $this->Reciever, $this->RecieverCity, $this->Message, $this->OrganizerNotes, $this->LARPid))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
       }
       $this->Id = $connection->lastInsertId();
       $stmt = null;
    }
    
      
}
