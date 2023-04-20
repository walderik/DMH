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
    
    if (isset($_POST['Intrigue'])) {
        $groupId = $_POST['Id'];
        $larp_group = LARP_Group::loadByIds($groupId, $current_larp->Id);
        
        $larp_group->Intrigue = $_POST['Intrigue'];
        $larp_group->update();
    }
    
    if (isset($_POST['OrganizerNotes'])) {
        $groupId = $_POST['Id'];
        $group = Group::loadById($groupId);
        $group->OrganizerNotes = $_POST['OrganizerNotes'];
        $group->update();
    }
    
    
       
    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
           
}
header('Location: ../index.php');