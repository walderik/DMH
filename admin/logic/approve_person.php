<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registrationId = $_POST['RegistrationId'];
    $registration = Registration::loadById($registrationId);
    if (isset($registration)) {

        $registration->ApprovedCharacters= date("Y-m-d");
        
        $registration->update();
        BerghemMailer::send_approval_mail($registration);
        header('Location: ../approval.php');
        exit;
    }
    
}
header('Location: ../index.php');
exit;


