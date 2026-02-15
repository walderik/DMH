<?php
include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        $paymentReference = base64_decode($code);
        
        $registration = Registration::findByPaymentReference($paymentReference);
        if (empty($registration)) {
            header('Location: index.php'); // personen är inte anmäld
            exit;
        }
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$now = time();
$start = strtotime($current_larp->StartDate);
$end = strtotime($current_larp->EndDate);
$midpoint = $start + (($end-$start)/2);

$checkout = false;


if ($now > $midpoint) {
    $checkout = true;    
}

if ($checkout) {
   header("Location: checkout_person.php?id=$registration->PersonId");
   exit;
}

header("Location: checkin_person.php?id=$registration->PersonId");
exit;

