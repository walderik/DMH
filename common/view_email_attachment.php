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

if (empty($attachment)) {
    header('Location: index.php');
    exit;
}


$email = $attachment->getEmail();
if ($email->toPerson()->Id != $current_person->Id) {
    header('Location: index.php'); //Emailet är inte för denna person
    exit;
    
}


$filename = $attachment->Filename;

header('Content-type: application/pdf'); 
header("Content-Disposition: inline; filename='$filename'");
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

echo $attachment->Attachement;