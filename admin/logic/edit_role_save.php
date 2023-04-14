<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roleId = $_POST['Id'];
    $larp_role = LARP_Role::loadByIds($roleId, $current_larp->Id);

    $role = Role::loadById($roleId);
    $role->setValuesByArray($_POST);
    $role->update();
    
    $role->deleteAllIntrigueTypes();
    if (isset($_POST['IntrigueTypeId'])) $role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        
    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
           
}
header('Location: ../index.php');