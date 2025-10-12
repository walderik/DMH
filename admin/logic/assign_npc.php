<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['roleId'])) {
        $role = Role::loadById($_POST['roleId']);
        $assignment = NPC_assignment::getAssignment($role, $current_larp);
        $personId = $_POST['PersonId'];
    } else {
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($npc) && !isset($personId)) {
    header('Location: ../npc_assignments.php');
    exit;
}

if ($personId == "null") {
    $personId = null;
}

$assignment->PersonId = $personId;
$assignment->IsReleased = 0;
$assignment->update();


header('Location: ../npc_assignments.php');

