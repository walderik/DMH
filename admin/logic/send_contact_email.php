<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}

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


$campaign = $current_larp->getCampaign();

$email = $_POST['email'];
$name = (isset($_POST['name'])) ? $_POST['name'] : 'Stranger';
$text = $_POST['text'];
$subject = "Meddelande till $name från $current_user->Name";
$referer = (isset($_POST['referer'])) ? $_POST['referer'] : '../../index.php';
$referer .= "?message=contact_email_sent";

BerghemMailer::send($email, $name, $text, $subject);
BerghemMailer::send($campaign->Email, $name, $text, "Kopia av ".$subject);

header('Location: ' . $referer);

// header('Location: ../../index.php');
