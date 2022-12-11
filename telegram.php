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
    public  $notes;
    
    public static function newFromArray($post)
    {
        return new Telegram($post['delivery_time'], $post['sender'], $post['sender_city'], $post['reciever'], $post['reciever_city'], $post['message'], $post['notes'] , $post['Id']);
    }
    
    public static function all()
    {
        global $conn;
        
        $sql = "SELECT * FROM telegrams ORDER BY Deliverytime;";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        
        if ($resultCheck > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<td>" . $row['Id'] . "</td>";
                echo "<td>" . $row['Deliverytime'] . "</td>";
                echo "<td>" . $row['Sender'] . "</td>";
                echo "<td>" . $row['SenderCity'] . "</td>";
                echo "<td>" . $row['Reciever'] . "</td>";
                echo "<td>" . $row['RecieverCity'] . "</td>";
                echo "<td>" . str_replace("\n", "<br>", $row['Message']) . "</td>";
                echo "<td>" . str_replace("\n", "<br>", $row['OrganizerNotes']) . "</td>";
            }
        }
    }
    
    
    public function __construct(string $deliverytime, string $sender, string $senderCity, string $reciever, string $recieverCity, string $message, ?string $notes)
    {
        $this->deliverytime = $deliverytime;
        $this->sender       = $sender;
        $this->senderCity   = empty($senderCity) ? 'Junk City' : $senderCity;
        $this->reciever     = $reciever;
        $this->recieverCity = empty($notes) ? 'Slow River' :  $recieverCity;
        $this->message      = $message;
        $this->notes        = is_null($notes) ? '' : $notes;

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
        $notes = $this->notes;
        $stmt->execute();
    }
    
    public function loadById($id)
    {
        # Gör en SQL där man söker baserat på ID och returnerar ett Telegram-object mha newFromArray
    }
    
    public function delete($id)
    {
        
    }
}

?>