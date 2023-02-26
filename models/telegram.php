<?php

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

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
    
    
    public static function allBySelectedLARP() {
        global $current_larp;
        
        $sql = "SELECT * FROM ".strtolower(static::class)." WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($current_larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
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
    
    
    
    # Update an existing telegram in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE ".strtolower(static::class)." SET Deliverytime=?, Sender=?, SenderCity=?, Reciever=?, RecieverCity=?, Message=?, OrganizerNotes=? WHERE Id = ?");
        
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
        $stmt =  $connection->prepare("INSERT INTO ".strtolower(static::class)." (Deliverytime, Sender, SenderCity, Reciever, RecieverCity, Message, OrganizerNotes, LARPid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Deliverytime, $this->Sender, $this->SenderCity, $this->Reciever, $this->RecieverCity, $this->Message, $this->OrganizerNotes, $this->LARPid))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
       }
       $this->Id = $connection->lastInsertId();
       $stmt = null;
    }
    
      
}
