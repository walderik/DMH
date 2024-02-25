<?php
include_once 'header.php';

// get the parameters from URL


$registrationId = $_REQUEST["registrationId"];
$amount = $_REQUEST["amount"];
$date = $_REQUEST["date"];

if (empty($registrationId) || empty($amount) || empty($date)) {
    echo "Värde saknas";
    return;
}

$registration = Registration::loadById($registrationId);
$registration->AmountPayed = $amount;
$registration->Payed = $date;
$registration->update();
echo "Betalning registrerad";



