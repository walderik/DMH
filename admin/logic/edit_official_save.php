<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $registrationId = $_POST['RegistrationId'];
    
    $registration = Registration::loadById($registrationId);
    
    $registration->deleteAllOfficialTypes();
    $registration->saveAllOfficialTypes($_POST);
    
    header('Location: ../officials.php');
    exit;
}

header('Location: ../index.php');