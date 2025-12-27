<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['roleId'])) {
        $role = Role::loadById($_POST['roleId']);
        $assignment = NPC_assignment::getAssignment($role, $current_larp);
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($assignment)) {
    header('Location: ../index.php');
    exit;
}


$assignment->release();


header('Location: ../npc_assignments.php');

