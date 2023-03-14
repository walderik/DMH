<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];

    if ($operation == 'insert') {
        $group = Group::newFromArray($_POST);
        $group->create();
        if (!strpos($_POST['action'], "anmälan")) {
            header('Location: ../index.php');
            exit;
        }
        else {
            header('Location: ../group_registration_form.php?new_group='.$group->Id);
            exit;
        }
        exit;
    } elseif ($operation == 'update') {
        
        $group = Group::newFromArray($_POST);
        
        //Kolla om man är gruppledare annars får man inte ändra på gruppen
        if (!$current_user->isGroupLeader($group)) {
            header('Location: ../index.php');
            exit;
        }
        $group->update();
        if (!strpos($_POST['action'], "anmälan")) {
            header('Location: ../index.php');
            exit;
        }
        else {
            header('Location: ../group_registration_form.php?new_group='.$group->Id);
            exit;
        }
        exit;
    } 
}
header('Location: ../index.php');
