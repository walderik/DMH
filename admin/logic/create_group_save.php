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


    $group = Group::newFromArray($_POST);
    $group->create();
    
    $larp_group = LARP_Group::newFromArray($_POST);
    $larp_group->GroupId = $group->Id;
    $larp_group->LARPId = $current_larp->Id;
    $larp_group->create();

    $larp_group->saveAllIntrigueTypes($_POST);        
    
    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
           
}
header('Location: ../index.php');