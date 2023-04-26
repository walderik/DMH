<?php
include_once '../header.php';

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

