<?php

include_once 'includes/db.inc.php';

function insert_telegram(telegram) {

    $stmt = $conn->prepare("INSERT INTO telegrams (Deliverytime, Sender, SenderCity, Reciever, RecieverCity, Message, OrganizerNotes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $deliverytime, $sender, $sendercity, $reciever, $recievercity, $message, $notes);
    
    // set parameters and execute
    $deliverytime = $_POST['delivery_time'];
    $sender = $_POST['sender'];
    $sendercity = $_POST['sender_city'];
    $reciever = $_POST['reciever'];
    $recievercity = $_POST['reciever_city'];
    $message = $_POST['message'];
    $notes = $_POST['notes'];
    $stmt->execute();

}

?>