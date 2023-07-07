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

if ($_POST['email'] == 'ALLADELTAGARE') {
    BerghemMailer::sendContactMailToAll($current_larp, nl2br($_POST['text']));
} elseif ($_POST['email'] == 'OFFICIALTYPE') {
    $official_type = OfficialType::loadById($_POST['official_type']);
    if (isset($official_type)) BerghemMailer::sendContactMailToAllOfficals($current_larp, $official_type, nl2br($_POST['text']));
} else {
    BerghemMailer::sendContactMailToSomeone($_POST['email'], $name, "Meddelande till $name från $current_user->Name", nl2br($_POST['text']));
}


header('Location: ' . $referer);

