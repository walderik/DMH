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
    } elseif ($operation == 'update') {
        //TODO Kolla om man är gruppledare annars får man inte ändra på gruppen
        
        
        $larp_group = LARP_Group::newFromArray($_POST);
        
        $group = Group::loadById($larp_group->GroupId);
        //Kolla om man är gruppledare annars får man inte ändra på gruppen
        if (!$current_user->isGroupLeader($group)) {
            header('Location: ../index.php');
            exit;
        }
        
        $larp_group->update();
        $larp_group->deleteAllIntrigueTypes();
        $larp_group->saveAllIntrigueTypes($_POST);
        
    } else {
        echo $operation;
    }
    header('Location: ../index.php');
}
