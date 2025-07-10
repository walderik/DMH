<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $npc = NPC::loadById($_GET['id']);
        if (isset($npc)) NPC::delete($npc->Id);
    } elseif (isset($_GET['roleID'])) {
        $role = Role::loadById($_GET['roleID']);
        if (isset($role) && $role->mayDelete()) Role::delete($role->Id);
    } else {
        header('Location: ../index.php');
        exit;
    }
}

if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}


header('Location: ../npc.php');

