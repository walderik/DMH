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
        $registration->AmountToPay = $_POST['AmountToPay'];
        $registration->AmountPayed = $_POST['AmountPayed'];
        
        $payed = $_POST['Payed'];
        if (isset ($payed) && $payed != "") {
            $registration->Payed = $payed;
        }
 
        $registration->NotComing = $_POST['NotComing'];
        $registration->NotComingReason = $_POST['NotComingReason'];
        $registration->ToBeRefunded = $_POST['ToBeRefunded'];
        
        $refundDate = $_POST['RefundDate'];
        if (isset ($refundDate) && $refundDate != "") {
            $registration->RefundDate = $refundDate;
        }
        
        $registration->update();

        if (isset($_POST['Referer']) && $_POST['Referer']!="") {
            header('Location: ' . $_POST['Referer']);
            exit;
        }
        
        header('Location: ../registered_persons.php');
        exit;
        
    }
    
}
header('Location: ../index.php');
