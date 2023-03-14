<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $larp_group = LARP_Group::newFromArray($_POST);
        $larp_group->create();
        $larp_group->saveAllIntrigueTypes($_POST);
    } 
    header('Location: ../index.php');
}
