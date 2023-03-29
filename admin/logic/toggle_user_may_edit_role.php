<?php

global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if (!isset( $_POST['roleId'])) {
    header('Location: ../index.php');
    exit;
}

$larp_role = LARP_Role::loadByIds($_POST['roleId'], $current_larp->Id);
if (empty($larp_role)) {
    header('Location: ../index.php');
    exit;
}


if ($larp_role->UserMayEdit == 0) {
    $larp_role->UserMayEdit = 1;

}
else {
    $larp_role->UserMayEdit = 0;
}

$larp_role->update();

//Sätt deltagaren till icke-godkänd
$role = Role::loadById($larp_role->RoleId);
$registration = Registration::loadByIds($role->PersonId, $current_larp->Id);
$registration->Approved=null;
$registration->update();

if (isset($_POST['Referer']) && $_POST['Referer']!="") {
    header('Location: ' . $_POST['Referer']);
    exit;
}


header('Location: ../roles.php');
