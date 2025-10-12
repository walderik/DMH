<?php
include_once '../header.php';

if (!isset( $_POST['roleId']) || !isset($_POST['personId'])) {
    header('Location: ../index.php');
    exit;
}

$role = Role::loadById($_POST['roleId']);
$person = Person::loadById($_POST['personId']);

$assignment = NPC_assignment::getAssignment($role, $current_larp);
$assignment->PersonId = $person->Id;
$assignment->update();



if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}


header('Location: ../npc_assignments.php');
