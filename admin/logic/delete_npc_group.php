<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $npc_group = NPCGroup::loadById($_GET['id']);
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($npc_group)) {
    header('Location: ../npc.php');
    exit;
}


$npcs = $npc_group->getNPCsInGroup();

foreach($npcs as $npc) {
    $npc->NPCGroupId = null;
    $npc->update();
}

NPCGroup::delete($npc_group->Id);

header('Location: ../npc.php');

