<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roleId = $_POST['Id'];
    
    if (empty($roleId)) {
        $role=Role::newFromArray($_POST);
        $role->CreatorPersonId = $current_person->Id;
        $role->UserMayEdit = 0;
        $role->IsApproved = 1;
        $role->ApprovedByPersonId = $current_person->Id;
        $now = new Datetime();
        $role->ApprovedDate = date_format($now,"Y-m-d H:i:s");
        $role->create();
    } else {
        $role = Role::loadById($roleId);
        $role->setValuesByArray($_POST);
        $role->update();
    }
    
    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    if (isset($larp_role)) {
        $larp_role->deleteAllIntrigueTypes();
        if (isset($_POST['IntrigueTypeId'])) {
            $larp_role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        }
        $larp_role->IntrigueIdeas = $_POST['IntrigueIdeas'];
        $larp_role->update();
    }
    
        
    $role->deleteAllAbilities();
    if (isset($_POST['AbilityId'])) {
        $role->saveAllAbilities($_POST['AbilityId']);
    }
    $role->deleteAllRoleFunctions();
    if (isset($_POST['RoleFunctionId'])) {
        $role->saveAllRoleFunctions($_POST['RoleFunctionId']);
    }
    $role->deleteAllSuperPowerActives();
    if (isset($_POST['SuperPowerActiveId'])) {
        $role->saveAllSuperPowerActives($_POST['SuperPowerActiveId']);
    }
    $role->deleteAllSuperPowerPassives();
    if (isset($_POST['SuperPowerPassiveId'])) {
        $role->saveAllSuperPowerActives($_POST['SuperPowerPassiveId']);
    }
    
        
    if (isset($_POST['Referer']) && $_POST['Referer'] != "") {
        header('Location: ' . $_POST['Referer']);
        exit();
    }
}
header('Location: ../index.php');