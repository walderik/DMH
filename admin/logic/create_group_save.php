<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $group = Group::newFromArray($_POST);
    $group->create();
    
    $larp_group = LARP_Group::newFromArray($_POST);
    $larp_group->GroupId = $group->Id;
    $larp_group->LARPId = $current_larp->Id;
    $larp_group->create();

    $group->saveAllIntrigueTypes($_POST);        
    
    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
           
}
header('Location: ../index.php');