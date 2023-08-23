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

// print_r($_POST);
// echo "<br>";
// print_r($_FILES);
// echo "<br>";
// print_r($_FILES['bilaga']);
// echo "<br>";

if ($_POST['email'] == 'ALLADELTAGARE') {
    BerghemMailer::sendContactMailToAll($current_larp, nl2br($_POST['text']));
} elseif ($_POST['email'] == 'ALLAGRUPPLEDARE') {
    BerghemMailer::sendContactMailToAllGroupLeaders($current_larp, nl2br($_POST['text']));
} elseif ($_POST['email'] == 'OFFICIALTYPE') {
    $official_type = OfficialType::loadById($_POST['official_type']);
    if (isset($official_type)) BerghemMailer::sendContactMailToAllOfficals($current_larp, $official_type, nl2br($_POST['text']));
} elseif ($_POST['email'] == 'send_intrigues') {
    if (!$current_larp->isIntriguesReleased()) {
        $current_larp->DisplayIntrigues = 1;
        $current_larp->update();
    }
    BerghemMailer::sendIntrigues($current_larp, nl2br($_POST['text']));
} elseif ($_POST['email'] == 'send_housing') {
    if (!$current_larp->isHousingReleased()) {
        $current_larp->DisplayHousing = 1;
        $current_larp->update();
    } 
    BerghemMailer::sendHousing($current_larp, nl2br($_POST['text']));
} else {
    BerghemMailer::sendContactMailToSomeone($_POST['email'], $name, "Meddelande till $name från $current_user->Name", nl2br($_POST['text']));
    header('Location: ' . $referer);
    exit;
}

header('Location: ../mail_admin.php');


