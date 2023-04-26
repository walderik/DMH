<?php
include_once '../header.php';

if (!isset( $_POST['RegistrationId'])) {
    header('Location: ../index.php');
    exit;
}

$registration = Registration::loadById($_POST['RegistrationId']);
if (empty($registration)) {
    header('Location: ../index.php');
    exit;
}


$registration->SpotAtLARP = 1;
$registration->update();

BerghemMailer::send_spot_at_larp($registration);


header('Location: ../registered_persons.php');
