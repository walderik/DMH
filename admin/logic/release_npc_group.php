<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['id'])) {
        $npc_group = NPCGroup::loadById($_POST['id']);
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($npc_group)) {
    header('Location: ../npc.php');
    exit;
}


$npc_group->release();


header('Location: ../npc.php');

