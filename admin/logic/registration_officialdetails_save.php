<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
include_once $root . '/includes/all_includes.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $officialDetails = $_POST['OfficialDetails'];
    $registrationId = $_POST['Id'];
    $registration = Registration::loadById($registrationId);

    if (isset($registration)) {
        $registration->OfficialDetails = $officialDetails;
        $registration->update();
    }
}

header('Location: ../officials.php');
    exit;
?>