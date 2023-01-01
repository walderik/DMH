<?php

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class Group extends BaseModel{
    
    public $Id;
    public $Name;
    public $ApproximateNumberOfMembers;
    public $NeedFireplace;
    public $Friends;
    public $Enemies;
    public $WantIntrigue = true;
    public $
    public $
    public $
    public $
    public $
    public $
    public $
    public $
    
    public static $tableName = 'groups';
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $group = group::newWithDefault();
        if (isset($post['Deliverytime'])) $group->Deliverytime = $post['Deliverytime'];
        if (isset($post['Sender'])) $group->Sender = $post['Sender'];
        if (isset($post['SenderCity'])) $group->SenderCity = $post['SenderCity'];
        if (isset($post['Reciever'])) $group->Reciever = $post['Reciever'];
        if (isset($post['RecieverCity'])) $group->RecieverCity = $post['RecieverCity'];
        if (isset($post['Message'])) $group->Message = $post['Message'];
        if (isset($post['OrganizerNotes'])) $group->OrganizerNotes = $post['OrganizerNotes'];
        if (isset($post['Id'])) $group->Id = $post['Id'];
        
        return $group;
    }
     
     
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing group in db
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
    
    # Create a new group in db
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