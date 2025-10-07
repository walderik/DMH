<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = "update";
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
    }
    if ($operation == 'insert') {
            
        
        
        $group = Group::newFromArray($_POST);
        $group->create();
        
        $larp_group = LARP_Group::newFromArray($_POST);
        $larp_group->GroupId = $group->Id;
        $larp_group->LARPId = $current_larp->Id;
        $larp_group->create();
        
        if (isset($_POST['IntrigueTypeId'])) $group->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        
        if (isset($_POST['Referer']) && $_POST['Referer']!="") {
            header('Location: ' . $_POST['Referer']);
            exit;
        }
    
    } elseif ($operation == 'update') {
        $groupId = $_POST['GroupId'];
    
        $groupArr = $_POST;
        $groupArr += ["Id" => $groupId];
        
        $group = Group::loadById($groupId);
        $group->setValuesByArray($groupArr);
        $group->update();
            
        $larp_group = LARP_Group::loadByIds($groupId, $current_larp->Id);
        

        $larp_group->setValuesByArray($_POST);
        $larp_group->update();
        
        $group->deleteAllIntrigueTypes();
        if (isset($_POST['IntrigueTypeId'])) $group->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
    }
    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
           
}
header('Location: ../index.php');