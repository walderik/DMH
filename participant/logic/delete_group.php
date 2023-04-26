<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

AccessControl::accessControlCampaign();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $group = Group::loadById($_GET['id']);
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($group)) {
    header('Location: ../index.php');
    exit;
}


if (!$current_user->isGroupLeader($group)) {
    header('Location: ../index.php'); //Inte din grupp
    exit;
}

Group::delete($group->Id);

header('Location: ../index.php?message=group_deleted');

