<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $registrationId = $_POST['RegistrationId'];
    
    $registration = Registration::loadById($registrationId);
    
    $registration->deleteAllOfficialTypes();
    $registration->saveAllOfficialTypes($_POST);
    
    header('Location: ../officials.php');
    exit;
}

header('Location: ../index.php');