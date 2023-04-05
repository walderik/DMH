<?php

global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if (!isset( $_POST['RegistrationId'])) {
    header('Location: ../index.php');
    exit;
}

$registration = Registration::loadByIds($_POST['RegistrationId']);
if (empty($registration)) {
    header('Location: ../index.php');
    exit;
}


$registration->SpotAtLARP = 1;
$registration->update();

BerghemMailer::send_spot_at_larp($registration);


header('Location: ../registered_persons.php');
