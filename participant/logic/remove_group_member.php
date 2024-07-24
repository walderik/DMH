<?php

require '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['groupID']) or !isset($_GET['roleID'])) {
        header('Location: ../index.php');
        exit;
    }
    $GroupId = $_GET['groupID'];
    $RoleId = $_GET['roleID'];

}

$current_group = Group::loadById($GroupId);


if (!$current_group->isRegistered($current_larp)) {
    header('Location: ../index.php'); //Gruppen är inte anmäld
    exit;
}

if (!$current_user->isGroupLeader($current_group)) {
    header('Location: ../index.php'); //Inte gruppledare i gruppen
    exit;
}

$role = Role::loadById($RoleId);
$role->GroupId = null;
$role->update();

header('Location: ' . $current_group->getViewLink());



