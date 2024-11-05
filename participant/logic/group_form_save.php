<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];

    if ($operation == 'insert') {
        $group = Group::newFromArray($_POST);
        $group->IsApproved = 0;
        $group->create();
        $group->deleteAllIntrigueTypes();
        if (isset($_POST['IntrigueTypeId'])) $group->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        
        if (!strpos($_POST['action'], "anmälan")) {
            header('Location: ../index.php');
            exit;
        }
        else {
            header('Location: ../group_registration_form.php?new_group='.$group->Id);
            exit;
        }
        exit;
    } elseif ($operation == 'update') {
        
        
        $group=Group::loadById($_POST['Id']);

        $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
        if (!empty($larp_group) && $larp_group->UserMayEdit == 0) {
            //Redan anmäld, utan tillåtelse att redigera
            header('Location: ../index.php?error=');
            exit;
        }
        
        
        //Kolla om man är gruppledare annars får man inte ändra på gruppen
        if (!$current_user->isGroupLeader($group)) {
            header('Location: ../index.php');
            exit;
        }
        
        if ($group->isApproved()) {
            GroupApprovedCopy::makeCopyOfApprovedGroup($group);
        }
        
        
        $group->setValuesByArray($_POST);
        $group->update();
        $group->unapprove($current_larp, false);
        $group->deleteAllIntrigueTypes();
        $group->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        
        if (!empty($larp_group)) {
            $larp_group->UserMayEdit = 0;
            $larp_group->update();
        }
        
        if (!strpos($_POST['action'], "anmälan")) {
            header('Location: ../index.php');
            exit;
        }
        else {
            header('Location: ../group_registration_form.php?new_group='.$group->Id);
            exit;
        }
        exit;
    } 
}
header('Location: ../index.php');
