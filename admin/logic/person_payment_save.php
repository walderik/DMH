<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}



echo '$_POST :<br>';
print_r($_POST);

echo "<br />";

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
        //header('Location: ../registered_persons.php');
        
    }
    
}
//header('Location: ../index.php');
