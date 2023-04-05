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
    $registrationId = $_POST['RegistrationId'];
    $registration = Registration::loadById($registrationId);
    if (isset($registration)) {

        $registration->ApprovedCharacters= date("Y-m-d");
        
        $registration->update();
        BerghemMailer::send_approval_mail($registration);
        header('Location: ../persons_to_approve.php');
        exit;
    }
    
}
header('Location: ../index.php');
exit;


