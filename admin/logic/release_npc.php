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
        $npc = NPC::loadById($_POST['id']);
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($npc)) {
    header('Location: ../npc.php');
    exit;
}


$npc->release();


header('Location: ../npc.php');

