<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $registrationId = $_POST['RegistrationId'];
    
    $registration = Registration::loadById($registrationId);
    
    $registration->deleteAllOfficialTypes();
    if (isset($_POST['OfficialTypeId'])) {
        $registration->saveAllOfficialTypes($_POST['OfficialTypeId']);
    }
    
    header('Location: ../officials.php');
    exit;
}

header('Location: ../index.php');