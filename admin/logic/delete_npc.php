<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $npc = NPC::loadById($_GET['id']);
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($npc)) {
    header('Location: ../npc.php');
    exit;
}


NPC::delete($npc->Id);

header('Location: ../npc.php');

