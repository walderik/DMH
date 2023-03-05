<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registrationId = $_POST['Id'];
    $registration = Registration::loadById($registrationId);
    if (isset($registration)) {
        if ($registration->IsOfficial == 0) {
            $registration->IsOfficial = 1;
        }
        else {
            $registration->IsOfficial = 0;
        }
        $registration->update();
    }
    header('Location: ../officials.php');
    exit;
    
}
header('Location: ../index.php');