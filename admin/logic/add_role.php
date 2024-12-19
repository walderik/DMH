<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $roleId = $_GET['id'];
    $role = Role::loadById($roleId);
          
    $larp_role = LARP_Role::newWithDefault();
    $larp_role->RoleId = $roleId;
    $larp_role->LARPId = $current_larp->Id;
    $larp_role->IsMainRole = 0;
    $larp_role->create();   

    
    BerghemMailer::send_added_role_mail($role, $current_larp, $current_person->Id);
        
    header('Location: ../not_registered_roles.php');
    exit;
}
header('Location: ../index.php');



