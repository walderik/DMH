<?php
include_once '../../participant/header.php';



if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../participant/index.php');
    exit;
}

if (!isset($_POST['type'])) {
    header('Location: ../index.php'); //Borde aldrig hända
    exit;
}
$type = $_POST['type'];

if ($type == 'one' && !isset($_POST['personId'])) {
    header('Location: ../index.php?error=no_email');
    exit;
}

if (!isset($_POST['text'])) {
    header('Location: ../index.php?error=no_text');
    exit;
}

$greeting = $_POST['greeting'];
$subject = $_POST['subject'];
$senderText = $_POST['senderText'];
$sender = $_POST['sender'];
$referer = (isset($_POST['referer'])) ? $_POST['referer'] : '../../participant/index.php';
$referer .= "?message=contact_email_sent";

// print_r($_POST);
// echo "<br>";
// print_r($_FILES);
// echo "<br>";
// print_r($_FILES['bilaga']);
// echo "<br>";


switch ($type) {
    case "intrigues":
        if (!$current_larp->isIntriguesReleased()) {
            $current_larp->DisplayIntrigues = 1;
            $current_larp->update();
        }
        BerghemMailer::sendIntrigues($greeting, $subject, nl2br($_POST['text']), $senderText, $current_larp, $current_person->Id);
        break;
    case "housing":
        if (!$current_larp->isHousingReleased()) {
            $current_larp->DisplayHousing = 1;
            $current_larp->update();
        }
        BerghemMailer::sendHousing($greeting, $subject, nl2br($_POST['text']), $senderText, $current_larp, $current_person->Id);
        break;
    case "one":
        if ($sender==BerghemMailer::LARP) BerghemMailer::sendContactMailToSomeone($_POST['personId'], $greeting, $subject, nl2br($_POST['text']), $senderText, $current_larp, $current_person->Id);
        else BerghemMailer::sendContactMailToSomeone($_POST['personId'], $greeting, $subject, nl2br($_POST['text']), $senderText, null, $current_person->Id);
        header('Location: ' . $referer);
        exit;
        break;
    case "all":
        BerghemMailer::sendContactMailToAll($greeting, $subject, nl2br($_POST['text']), $senderText, $current_larp, $current_person->Id);
        break;
    case "several":
        if ($sender==BerghemMailer::LARP) BerghemMailer::sendContactMailToSeveral($_POST['personId'], $greeting, $subject, nl2br($_POST['text']), $senderText, $current_larp, $current_person->Id);
        else BerghemMailer::sendContactMailToSeveral($_POST['personId'], $greeting, $subject, nl2br($_POST['text']), $senderText, null, $current_person->Id);
        break;
}

header('Location: ../mail_admin.php');


