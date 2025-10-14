<?php

global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['RoleId'])) {
        $role = Role::loadById($_POST['RoleId']);
    }
}

if (empty($role)) {
    header('Location: ../index.php?notfount');
    exit;
}

if ($role->CampaignId != $current_larp->CampaignId) {
    header('Location: ../index.php?wrongcamp'); //Tillhör inte kampanjen
    exit;
}

$role->PersonId = null;
$role->UserMayEdit = 0;
$role->update();


if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
     
header('Location: ../view_role.php?id='.$role->Id); 

