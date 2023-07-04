<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../participant/index.php');
    exit;
}

if (!isset($_POST['email'])) {
    header('Location: ../../admin/index.php?error=no_email');
    exit;
}
if (!isset($_POST['text'])) {
    header('Location: ../../admin/index.php?error=no_text');
    exit;
}

$name = (isset($_POST['name'])) ? $_POST['name'] : 'Stranger';
$referer = (isset($_POST['referer'])) ? $_POST['referer'] : '../../index.php';
$referer .= "?message=contact_email_sent";

BerghemMailer::sendContact($_POST['email'], $name, "Meddelande till $name från $current_user->Name", nl2br($_POST['text']));

// BerghemMailer::send($email, $name, $text, $subject, array(), $campaign->Email);
//BerghemMailer::send($campaign->Email, $name, $text, "Kopia av ".$subject);

header('Location: ' . $referer);

// header('Location: ../../index.php');
