<?php

global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['RoleId'])) {
        $role = Role::loadById($_POST['RoleId']);
    }
    if (isset($_POST['PersonId'])) {
        $person = Person::loadById($_POST['PersonId']);
    }
}

if (empty($role) || empty($person)) {
    header('Location: ../index.php');
    exit;
}

if ($role->isPC($current_larp) || $role->CampaignId != $current_larp->CampaignId) {
    header('Location: ../index.php'); //Redan spelarkaraktär eller tillhör inte kampanjen
    exit;
}

$assignment = NPC_assignment::getAssignment($role, $current_larp);
if (!empty($assignment)) {
    header('Location: ../index.php'); //Har uppdrag under lajvet
    exit;
    
}

$role->PersonId = $person->Id;
$role->update();

$larp_role = LARP_Role::newWithDefault();
$larp_role->RoleId = $role->Id;
$larp_role->LARPId = $current_larp->Id;
$larp_role->PersonId = $person->Id;
if (empty($person->getMainRole($current_larp))) $larp_role->IsMainRole = 1;
else $larp_role->IsMainRole = 0;

$larp_role->create();


header('Location: ../view_role.php?id='.$role->Id); 

