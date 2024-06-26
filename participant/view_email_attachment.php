<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $attachmentId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$attachment = Attachment::loadById($attachmentId); 
$email = $attachment->getEmail();

if (empty($attachment)) {
    header('Location: index.php');
    exit;
}

if (!$email->isForUser($current_user)) {
    header('Location: index.php'); //Emailet är inte för någon person hos denna användare
    exit;
    
}



$filename = $attachment->Filename;

header('Content-type: application/pdf'); 
header("Content-Disposition: inline; filename='$filename'");
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

echo $attachment->Attachement;
