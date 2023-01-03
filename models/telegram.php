<?php

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class Telegram extends BaseModel{
    
    public  $Id;
    public  $Deliverytime = "1868-09-13T17:00";
    public  $Sender;
    public  $SenderCity = 'Junk City';
    public  $Reciever;
    public  $RecieverCity = 'Slow River';
    public  $Message;
    public  $OrganizerNotes;
    public  $LarpsId;
    
    public static $tableName = 'telegrams';
    public static $orderListBy = 'Deliverytime';
    
    public static function newFromArray($post){
        $telegram = Telegram::newWithDefault();
        if (isset($post['Deliverytime'])) $telegram->Deliverytime = $post['Deliverytime'];
        if (isset($post['Sender'])) $telegram->Sender = $post['Sender'];
        if (isset($post['SenderCity'])) $telegram->SenderCity = $post['SenderCity'];
        if (isset($post['Reciever'])) $telegram->Reciever = $post['Reciever'];
        if (isset($post['RecieverCity'])) $telegram->RecieverCity = $post['RecieverCity'];
        if (isset($post['Message'])) $telegram->Message = $post['Message'];
        if (isset($post['OrganizerNotes'])) $telegram->OrganizerNotes = $post['OrganizerNotes'];
        if (isset($post['Id'])) $telegram->Id = $post['Id'];
        
        return $telegram;
    }
     
     
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $telegram = new self();
        $telegram->LarpsId = $current_larp->Id;
        return $telegram;
    }
    
    # Update an existing telegram in db
    public function update()
    {
        $stmt = $this->connect()->prepare("UPDATE ".static::$tableName." SET Deliverytime=?, Sender=?, SenderCity=?, Reciever=?, RecieverCity=?, Message=?, OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Deliverytime, $this->Sender, $this->SenderCity, 
            $this->Reciever, $this->RecieverCity, $this->Message, $this->OrganizerNotes, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
    }
    
    # Create a new telegram in db
    public function create()
    {
        $stmt = $this->connect()->prepare("INSERT INTO ".static::$tableName." (Deliverytime, Sender, SenderCity, Reciever, RecieverCity, Message, OrganizerNotes, LARPsid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->Deliverytime, $this->Sender, $this->SenderCity,
            $this->Reciever, $this->RecieverCity, $this->Message, $this->OrganizerNotes, $this->LarpsId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
      
}
