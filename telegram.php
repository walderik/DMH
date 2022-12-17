<?php

include_once 'includes/db.inc.php';

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class Telegram {
    
    public  $id;
    public  $deliverytime;
    public  $sender;
    public  $senderCity;
    public  $reciever;
    public  $recieverCity;
    public  $message;
    public  $organizerNotes;
    
    public static function newFromArray($post){
        return new Telegram($post['Deliverytime'], $post['Sender'], $post['SenderCity'], $post['Reciever'], $post['RecieverCity'], $post['Message'], $post['OrganizerNotes'] , $post['Id']);
    }
    
     public static function all() {
         global $conn;
        
        $sql = "SELECT * FROM telegrams ORDER BY Deliverytime;";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        $telegram_array = array();
        if ($resultCheck > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $telegram_array[] = Telegram::newFromArray($row);
            }
        }
         return $telegram_array;
     }
     
     public static function delete($id)
     {
         global $conn;
         
         $stmt = $conn->prepare("DELETE FROM telegrams WHERE Id = ?");
         $stmt->bind_param("i", $id);
         
         // set parameters and execute
         $stmt->execute();
     }
     
     public static function loadById($id)
     {
         # Gör en SQL där man söker baserat på ID och returnerar ett Telegram-object mha newFromArray
         global $conn;
                  
         $stmt = $conn->prepare("SELECT * FROM telegrams WHERE Id = ?");
         $stmt->bind_param("i", $id);
         $stmt->execute();
         $result = $stmt->get_result(); // get the mysqli result
         $row = $result->fetch_assoc(); // fetch data
         $telegram = Telegram::newFromArray($row);
         return $telegram;         
     }
     
    
    public function __construct(?string $deliverytime, string $sender, string $senderCity, string $reciever, string $recieverCity, string $message, ?string $organizerNotes, ?int $id=NULL) {
        $this->deliverytime   = is_null($deliverytime) ? NULL : $deliverytime;
        $this->sender         = $sender;
        $this->senderCity     = empty($senderCity) ? 'Junk City' : $senderCity;
        $this->reciever       = $reciever;
        $this->recieverCity   = empty($recieverCity) ? 'Slow River' :  $recieverCity;
        $this->message        = $message;
        $this->organizerNotes = is_null($organizerNotes) ? '' : $organizerNotes;
        $this->id             = $id;
    }
    
    # Update an existing telegram in db
    public function update()
    {
        global $conn;
        
        $stmt = $conn->prepare("UPDATE telegrams SET Deliverytime=?, Sender=?, SenderCity=?, Reciever=?, RecieverCity=?, Message=?, OrganizerNotes=? WHERE id = ?");
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
        
        $stmt = $conn->prepare("INSERT INTO telegrams (Deliverytime, Sender, SenderCity, Reciever, RecieverCity, Message, OrganizerNotes) VALUES (?, ?, ?, ?, ?, ?, ?)");
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
    
    public function destroy()
    {
        self::delete($this->id);
    }
      
}

?>