<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

echo '$_POST :<br>';
print_r($_POST);

echo "<br />";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $larp_group = LARP_Group::newFromArray($_POST);
        $larp_group->create();
        $larp_group->saveAllIntrigueTypes($_POST);
    } elseif ($operation == 'delete') {
        $larp_group->deleteAllIntrigueTypes();
        LARP_Group::delete($_POST['LarpId'], $_POST['GroupId']);
    } elseif ($operation == 'update') {
        
        $larp_group = LARP_Group::newFromArray($_POST);
        $larp_group->update();
        $larp_group->deleteAllIntrigueTypes();
        $larp_group->saveAllIntrigueTypes($_POST);
        
    } else {
        echo $operation;
    }
    header('Location: ../index.php');
}
