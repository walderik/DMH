<?php
include_once '../header.php';

if (!isset( $_POST['roleId'])) {
    header('Location: ../index.php');
    exit;
}

$role = Role::loadById($_POST['roleId']);
$assignment = NPC_assignment::getAssignment($role, $current_larp);

NPC_assignment::delete($assignment->Id);

header('Location: ../npc_assignments.php');