<?php

include_once 'includes/db.inc.php';

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
    public function save()
    {
        
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
    
    public function loadById($id)
    {
        # Gör en SQL där man söker baserat på ID och returnerar ett Telegram-object mha newFromArray
    }
    
}

?>