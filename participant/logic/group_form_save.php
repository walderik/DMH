<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];

    if ($operation == 'insert') {
        $group = Group::newFromArray($_POST);
        $group->create();
        if (strpos($_POST['action'], "anmälan") == false) {
            header('Location: ../index.php');
            exit;
        }
        else {
            echo "Till anmälan";
            header('Location: ../group_registration_form.php?new_group='.$group->Id);
            exit;
        }
        exit;
    } elseif ($operation == 'delete') {
        Group::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        
        $group = Group::newFromArray($_POST);
        $group->update();
        if (strpos($_POST['action'], "anmälan") == false) {
            header('Location: ../index.php');
            exit;
        }
        else {
            echo "Till anmälan";
            header('Location: ../group_registration_form.php?new_group='.$group->Id);
            exit;
        }
        exit;
    } else {
        echo $operation;
    }
}
header('Location: ../index.php');
