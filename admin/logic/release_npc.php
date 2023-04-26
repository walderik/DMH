<?php
include_once '../header.php';

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

