<?php
include_once '../header.php';

if (!isset( $_POST['roleId'])) {
    header('Location: ../index.php');
    exit;
}

$assignment = NPC_assignment::newWithDefault();
$assignment->RoleId = $_POST['roleId'];
$assignment->LarpId = $current_larp->Id;
$assignment->create();



if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}


header('Location: ../npc_overview.php');
