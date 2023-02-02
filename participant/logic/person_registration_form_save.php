<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

echo '$_POST :<br>';
print_r($_POST);

echo "<br />";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    $mainRole = $_POST['IsMainRole'];
    if ($operation == 'insert') {
        $registration = Registration::newFromArray($_POST);
        $registration->create();
        
        $roleIdArr = $_POST['roleId'];
        
        foreach ($roleIdArr as $roleId) {
            $larp_role = LARP_Role::newWithDefault();
            $larp_role->RoleId = $roleId;
            $larp_role->LARPId = $current_larp->Id;
            if ($mainRole == $roleId) {
                echo "Main role: " . $larp_role->Id;
                $larp_role->IsMainRole = 1;
            }
            $larp_role->create();            
        }
        $intrigueTypeRoleArr = $_POST['IntrigueTypeId'];

        foreach ($intrigueTypeRoleArr as  $key => $intrigueTypeRole) {
            $larp_role = LARP_Role::loadByIds($key, $current_larp->Id);
            $larp_role->saveAllIntrigueTypes($intrigueTypeRole);
        }
        
    } else {
        echo $operation;
    }
    
    //header('Location: ../index.php');
}
