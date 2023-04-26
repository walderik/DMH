<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['id'])) {
        $npc = NPC::loadById($_POST['id']);
        $personId = $_POST['PersonId'];
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($npc) && !isset($personId)) {
    header('Location: ../npc.php');
    exit;
}

if ($personId == "null") {
    $personId = null;
}

$npc->PersonId = $personId;
$npc->IsReleased = 0;
$npc->update();


header('Location: ../npc.php');

