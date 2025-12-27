<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $assignment = NPC_assignment::newFromArray($_POST);
        $assignment->create();
    } elseif ($operation == 'update') {
               
        $role = Role::loadById($_POST['RoleId']);
        $assignment = NPC_assignment::getAssignment($role, $current_larp);
        $assignment->setValuesByArray($_POST);  
        $assignment->update();
    }
    
    
    $referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../npc_assignments.php';
    
    header('Location: ' . $referer);
    exit;
}
    
header('Location: ../npc.php');


