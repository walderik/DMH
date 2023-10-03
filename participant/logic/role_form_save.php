<?php

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $role = Role::newFromArray($_POST);
        $role->IsApproved = 0;
        $role->create();
    } elseif ($operation == 'update') {
        $role = Role::loadById($_POST['Id']);
        
        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
        if (!empty($larp_role) && $larp_role->UserMayEdit == 0) {
            //Redan anmäld, utan tillåtelse att redigera
            header('Location: ../index.php?error=');
            exit;
        }
        if (Person::loadById($role->PersonId)->UserId != $current_user->Id) {
            header('Location: index.php'); //Inte din karaktär
            exit;
        }
 
        $role->setValuesByArray($_POST);
        $role->IsApproved = 0;
        $role->update();

        $role->deleteAllIntrigueTypes();
        if (isset($_POST['IntrigueTypeId'])) {
            $role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        }
        

        if (!empty($larp_role)) {
            $larp_role->UserMayEdit = 0;
            $larp_role->update();
        }
    }
    
    
    
    header('Location: ../index.php');
}
