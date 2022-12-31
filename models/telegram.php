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
        return new self();
    }
    
    # Update an existing telegram in db
    public function update()
    {
        global $conn;
        
        $stmt = $conn->prepare("UPDATE ".static::$tableName." SET Deliverytime=?, Sender=?, SenderCity=?, Reciever=?, RecieverCity=?, Message=?, OrganizerNotes=? WHERE Id = ?");
        $stmt->bind_param("sssssssi",$Deliverytime, $Sender, $SenderCity, $Reciever, $RecieverCity, $Message, $OrganizerNotes, $Id);
        
        // set parameters and execute
        $Id = $this->Id;
        $Deliverytime = $this->Deliverytime;
        $Sender = $this->Sender;
        $SenderCity = $this->SenderCity;
        $Reciever = $this->Reciever;
        $RecieverCity = $this->RecieverCity;
        $Message = $this->Message;
        $OrganizerNotes = $this->OrganizerNotes;
        $stmt->execute();
    }
    
    # Create a new telegram in db
    public function create()
    {
        global $conn;
        
        $stmt = $conn->prepare("INSERT INTO ".static::$tableName." (Deliverytime, Sender, SenderCity, Reciever, RecieverCity, Message, OrganizerNotes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $Deliverytime, $Sender, $SenderCity, $Reciever, $RecieverCity, $Message, $OrganizerNotes);
        
        // set parameters and execute
        $Deliverytime = $this->Deliverytime;
        $Sender = $this->Sender;
        $SenderCity = $this->SenderCity;
        $Reciever = $this->Reciever;
        $RecieverCity = $this->RecieverCity;
        $Message = $this->Message;
        $OrganizerNotes = $this->OrganizerNotes;
        $stmt->execute();
    }
    
      
}

?>