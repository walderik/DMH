<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../participant/index.php');
    exit;
}

if (!isset($_POST['type'])) {
    header('Location: ../admin/index.php'); //Borde aldrig hända
    exit;
}
$type = $_POST['type'];

if ($type == 'one' && !isset($_POST['email'])) {
    header('Location: ../admin/index.php?error=no_email');
    exit;
}

if (!isset($_POST['text'])) {
    header('Location: ../admin/index.php?error=no_text');
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


switch ($type) {
    case "intrigues":
        if (!$current_larp->isIntriguesReleased()) {
            $current_larp->DisplayIntrigues = 1;
            $current_larp->update();
        }
        BerghemMailer::sendIntrigues($current_larp, nl2br($_POST['text']));
        break;
    case "housing":
        if (!$current_larp->isHousingReleased()) {
            $current_larp->DisplayHousing = 1;
            $current_larp->update();
        }
        BerghemMailer::sendHousing($current_larp, nl2br($_POST['text']));
        break;
    case "one":
        BerghemMailer::sendContactMailToSomeone($_POST['email'], $name, "Meddelande till $name från $current_user->Name", nl2br($_POST['text']), $current_larp);
        header('Location: ' . $referer);
        exit;
        break;
    case "all":
        BerghemMailer::sendContactMailToAll($current_larp, nl2br($_POST['text']));
        break;
    case "several":
        BerghemMailer::sendContactMailToSeveral(nl2br($_POST['text']), $_POST['email'], $_POST['subject'], $_POST['name'], $current_larp);
        break;
}

header('Location: ../mail_admin.php');


