<?php

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $roleId = $_GET['id'];
    $role = Role::loadById($roleId);
          
    $larp_role = LARP_Role::newWithDefault();
    $larp_role->RoleId = $roleId;
    $larp_role->LARPId = $current_larp->Id;
    $larp_role->IsMainRole = 0;
    $larp_role->create();   
    
    //Karaktären måste godkännas.
    $registration = Registration::loadByIds($role->PersonId, $current_larp->Id);
    $registration->ApprovedCharacters = null;
    
    BerghemMailer::send_added_role_mail($role, $current_larp);
        
    header('Location: ../index.php?message=registration_done');
    exit;
}
header('Location: ../index.php');



