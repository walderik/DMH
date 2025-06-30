<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/includes/init.php';

if (!isset($_SESSION['navigation'])) {
    header('Location: ../participant/index.php');
    exit;
}


if ($_SESSION['navigation'] == Navigation::LARP) {
    include '../admin/header.php';
    $navigation = '../admin/navigation.php';
} elseif ($_SESSION['navigation'] == Navigation::CAMPAIGN) {
    include '../campaign/header.php';
    $navigation =  '../campaign/navigation.php';
} elseif ($_SESSION['navigation'] == Navigation::BOARD) {
    include '../board/header.php';
    $navigation =  '../board/navigation.php';
} elseif ($_SESSION['navigation'] == Navigation::HOUSES) {
    include '../houses/header.php';
    $navigation =  '../houses/navigation.php';
} elseif ($_SESSION['navigation'] == Navigation::OM_ADMIN) {
    include '../site-admin/header.php';
    $navigation =  '../site-admin/navigation.php';
} elseif ($_SESSION['navigation'] == Navigation::PARTICIPANT) {
    include '../participant/header.php';
    $navigation =  '../participant/navigation.php';
} else {
    header('Location: ../participant/index.php');
    exit;
}

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
if ($email->toPerson()->Id != $current_person->Id && !AccessControl::hasAccessLarp($current_person, Larp::loadById($email->LarpId)) && !AccessControl::BOARD && !AccessControl::ADMIN) {
    header('Location: index.php'); //Emailet är inte för denna person
    exit;
    
}


$filename = $attachment->Filename;

header('Content-type: application/pdf'); 
header("Content-Disposition: inline; filename='$filename'");
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

echo $attachment->Attachement;
