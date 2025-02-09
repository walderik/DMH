<?php
include_once '../header.php';

if (!isset( $_POST['Reserve_RegistrationId'])) {
    header('Location: ../index.php');
    exit;
}

$reserve_registration = Reserve_Registration::loadById($_POST['Reserve_RegistrationId']);
$reserve_registration->removeFromReserves();

header('Location: ../reserves.php');
