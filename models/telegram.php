<?php

include_once 'includes/db.inc.php';
include 'models/base_model.php';

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class Telegram extends BaseModel{
    
    public  $id;
    public  $deliverytime = "1868-09-13T17:00";
    public  $sender;
    public  $senderCity = 'Junk City';
    public  $reciever;
    public  $recieverCity = 'Slow River';
    public  $message;
    public  $organizerNotes;
    
    public static $tableName = 'telegrams';
    public static $orderListBy = 'Deliverytime';
    
    public static function newFromArray($post){
        $telegram = Telegram::newWithDefault();
        if (isset($post['Deliverytime'])) $telegram->deliverytime = $post['Deliverytime'];
        if (isset($post['Sender'])) $telegram->sender = $post['Sender'];
        if (isset($post['SenderCity'])) $telegram->senderCity = $post['SenderCity'];
        if (isset($post['Reciever'])) $telegram->reciever = $post['Reciever'];
        if (isset($post['RecieverCity'])) $telegram->recieverCity = $post['RecieverCity'];
        if (isset($post['Message'])) $telegram->message = $post['Message'];
        if (isset($post['OrganizerNotes'])) $telegram->organizerNotes = $post['OrganizerNotes'];
        if (isset($post['Id'])) $telegram->id = $post['Id'];
        
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
        
        $stmt = $conn->prepare("UPDATE ".static::$tableName." SET Deliverytime=?, Sender=?, SenderCity=?, Reciever=?, RecieverCity=?, Message=?, OrganizerNotes=? WHERE id = ?");
        $stmt->bind_param("sssssssi", $deliverytime, $sender, $sendercity, $reciever, $recievercity, $message, $notes, $id);
        
        // set parameters and execute
        $id = $this->id;
        $deliverytime = $this->deliverytime;
        $sender = $this->sender;
        $sendercity = $this->senderCity;
        $reciever = $this->reciever;
        $recievercity = $this->recieverCity;
        $message = $this->message;
        $notes = $this->organizerNotes;
        $stmt->execute();
    }
    
    # Create a new telegram in db
    public function create()
    {
        global $conn;
        
        $stmt = $conn->prepare("INSERT INTO ".static::$tableName." (Deliverytime, Sender, SenderCity, Reciever, RecieverCity, Message, OrganizerNotes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $deliverytime, $sender, $sendercity, $reciever, $recievercity, $message, $notes);
        
        // set parameters and execute
        $deliverytime = $this->deliverytime;
        $sender = $this->sender;
        $sendercity = $this->senderCity;
        $reciever = $this->reciever;
        $recievercity = $this->recieverCity;
        $message = $this->message;
        $notes = $this->organizerNotes;
        $stmt->execute();
    }
    
      
}

?>