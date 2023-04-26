<?php
include_once '../header.php';

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

