<?php

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $role = Role::newFromArray($_POST);
        $role->create();
    } elseif ($operation == 'update') {
        

        $role = Role::newFromArray($_POST);
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
        

        $role->deleteAllIntrigueTypes();
        if (isset($_POST['IntrigueTypeId'])) {
            $role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        }
        
        
        $role->update();
        
        if (!empty($larp_role)) {
            $larp_role->UserMayEdit = 0;
            $larp_role->update();
            
            
            //Sätt deltagaren till icke-godkänd
            $registration = Registration::loadByIds($role->PersonId, $current_larp->Id);
            if (!empty($registration)) {
                $registration->ApprovedCharacters=null;
                $registration->update();
            }
            
        }
    }
    
    
    
    header('Location: ../index.php');
}
