<?php

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $role = Role::newFromArray($_POST);
        $person = $role->getPerson();
        if (is_null($person)) $person = $current_person;
        $previous_roles = $person->getAllRoles($current_larp);
        if (!empty($previous_roles)) {
            foreach ($previous_roles as $previous_role) {
                if ($role->Name == $previous_role->Name) {
                    header('Location: ../index.php?error=role_already_exists');
                    exit;
                }
            }
        }
        
        $role->IsApproved = 0;
        $role->create();
        
        if (isset($_POST['IntrigueTypeId'])) {
            $role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        }
        
        if (isset($_POST['AbilityId'])) {
            $role->saveAllAbilities($_POST['AbilityId']);
        }
        if (isset($_POST['RoleFunctionId'])) {
            $role->saveAllRoleFunctions($_POST['RoleFunctionId']);
        }
        
    } elseif ($operation == 'update') {
        $role = Role::loadById($_POST['Id']);
        
        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
        if (!empty($larp_role) && $larp_role->UserMayEdit == 0) {
            //Redan anmäld, utan tillåtelse att redigera
            header('Location: ../index.php?error=');
            exit;
        }
        if ($role->PersonId != $current_person->Id) {
            header('Location: index.php'); //Inte din karaktär
            exit;
        }
        if ($role->isApproved()) {
            RoleApprovedCopy::makeCopyOfApprovedRole($role);
        }
        
        $role->setValuesByArray($_POST);

        $role->update();
        $role->unapprove($current_larp, false, null);

        $role->deleteAllIntrigueTypes();
        if (isset($_POST['IntrigueTypeId'])) {
            $role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        }
        
        $role->deleteAllAbilities();
        if (isset($_POST['AbilityId'])) {
            $role->saveAllAbilities($_POST['AbilityId']);
        }
        $role->deleteAllRoleFunctions();
        if (isset($_POST['RoleFunctionId'])) {
            $role->saveAllRoleFunctions($_POST['RoleFunctionId']);
        }
        
        
        if (!empty($larp_role)) {
            $larp_role->UserMayEdit = 0;
            $larp_role->update();
        }
    }
    
    
    
    header('Location: ../index.php');
}
