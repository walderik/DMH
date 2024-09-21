<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../participant/index.php');
    exit;
}

if (!isset($_POST['type'])) {
    header('Location: ../houses/index.php'); //Borde aldrig hända
    exit;
}
$type = $_POST['type'];

if ($type == 'one' && !(isset($_POST['email']) || isset($_POST['personId']))) {
    header('Location: ../houses/index.php?error=no_email');
    exit;
}

if (!isset($_POST['text'])) {
    header('Location: ../houses/index.php?error=no_text');
    exit;
}

$greeting = $_POST['greeting'];
$subject = $_POST['subject'];
$senderText = $_POST['senderText'];
$name = (isset($_POST['name'])) ? $_POST['name'] : 'kära berghemmare';
$referer = (isset($_POST['referer'])) ? $_POST['referer'] : '../../index.php';
$referer .= "?message=contact_email_sent";



switch ($type) {
    case "one":
        if (isset($_POST['personId'])) BerghemMailer::sendContactMailToSomeone($_POST['personId'], $greeting, $subject, nl2br($_POST['text']), $senderText, null);
        
        else BerghemMailer::sendContactMailToSomeoneUnknown($_POST['email'], $greeting, $subject, "Meddelande till $name från $current_user->Name", nl2br($_POST['text']), $senderText, null);
        header('Location: ' . $referer);
        exit;
        break;
    case "several":
        if (isset($_POST['personId'])) BerghemMailer::sendContactMailToSeveral($_POST['personId'], $greeting, $subject, nl2br($_POST['text']), $senderText, null);
        else BerghemMailer::sendContactMailToSeveralUnknown(nl2br($_POST['email']), $greeting, $subject, nl2br($_POST['text']), $senderText, null);
        break;
}

header('Location: ../mail_admin.php');


