<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roleId = $_POST['Id'];
    $larp_role = LARP_Role::loadByIds($roleId, $current_larp->Id);

    $role = Role::loadById($roleId);
    $role->setValuesByArray($_POST);
    $role->update();

    $role->deleteAllIntrigueTypes();
    if (isset($_POST['IntrigueTypeId']))
        $role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);

        
    $role->deleteAllAbilities();
    if (isset($_POST['AbilityId'])) {
        $role->saveAllAbilities($_POST['AbilityId']);
    }
        
        
    if (isset($_POST['Referer']) && $_POST['Referer'] != "") {
        header('Location: ' . $_POST['Referer']);
        exit();
    }
}
header('Location: ../index.php');