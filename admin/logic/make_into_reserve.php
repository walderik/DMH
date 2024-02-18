<?php
include_once '../header.php';

if (!isset( $_GET['RegistrationId'])) {
    header('Location: ../index.php');
    exit;
}

$registration = Registration::loadById($_GET['RegistrationId']);
$registration->changeRegistrationToReserve();


header('Location: ../reserves.php');


