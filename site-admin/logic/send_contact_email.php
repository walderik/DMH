<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../participant/index.php');
    exit;
}

if (!isset($_POST['type'])) {
    header('Location: ../site-admin/index.php'); //Borde aldrig hända
    exit;
}
$type = $_POST['type'];

if ($type == 'one' && !isset($_POST['email'])) {
    header('Location: ../site-admin/index.php?error=no_email');
    exit;
}

if (!isset($_POST['text'])) {
    header('Location: ../site-admin/index.php?error=no_text');
    exit;
}

$name = (isset($_POST['name'])) ? $_POST['name'] : 'kära berghemmare';
$referer = (isset($_POST['referer'])) ? $_POST['referer'] : '../../index.php';
$referer .= "?message=contact_email_sent";



switch ($type) {
    case "one":
        BerghemMailer::sendContactMailToSomeone($_POST['email'], $name, "Meddelande till $name från $current_user->Name", nl2br($_POST['text']), null);
        header('Location: ' . $referer);
        exit;
        break;
    case "several":
        BerghemMailer::sendContactMailToSeveral(nl2br($_POST['text']), $_POST['email'], $_POST['subject'], $_POST['name'], null);
        break;
}

header('Location: ../mail_admin.php');


