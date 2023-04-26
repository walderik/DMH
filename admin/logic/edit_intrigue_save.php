<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (isset($_POST['Intrigue'])) {
        $roleId = $_POST['Id'];
        $larp_role = LARP_Role::loadByIds($roleId, $current_larp->Id);
        $larp_role->Intrigue = $_POST['Intrigue'];
        $larp_role->update();
    }

    if (isset($_POST['OrganizerNotes'])) {
        $roleId = $_POST['Id'];
        $role = Role::loadById($roleId);
        $role->OrganizerNotes = $_POST['OrganizerNotes'];
        $role->update();
    }
    
       
    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
           
}
header('Location: ../index.php');