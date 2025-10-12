<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roleId = $_POST['Id'];
    
    if (empty($roleId)) {
        $role=Role::newFromArray($_POST);
        $role->CreatorPersonId = $current_person->Id;
        $role->create();
        
    } else {
        $role = Role::loadById($roleId);
        $role->setValuesByArray($_POST);
        $role->update();
    }
    
    $role->deleteAllIntrigueTypes();
    if (isset($_POST['IntrigueTypeId']))
        $role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);

        
    $role->deleteAllAbilities();
    if (isset($_POST['AbilityId'])) {
        $role->saveAllAbilities($_POST['AbilityId']);
    }
    $role->deleteAllRoleFunctions();
    if (isset($_POST['RoleFunctionId'])) {
        $role->saveAllRoleFunctions($_POST['RoleFunctionId']);
    }
    
        
    if (isset($_POST['Referer']) && $_POST['Referer'] != "") {
        header('Location: ' . $_POST['Referer']);
        exit();
    }
}
header('Location: ../index.php');