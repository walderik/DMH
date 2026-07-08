<?php
include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        $paymentReference = base64_decode($code);
        
        $registration = Registration::findByPaymentReference($paymentReference);
        if (empty($registration) || ($registration->LARPId != $current_larp->Id)) {
            header('Location: index.php'); // personen är inte anmäld
            exit;
        }
        if ($current_larp->isCheckoutTime()) {
            header("Location: checkout_person.php?id=$registration->PersonId");
            exit;
        }
        
        header("Location: checkin_person.php?id=$registration->PersonId");
        exit;
        
    }
    else {
        header('Location: index.php');
        exit;
    }
}



