<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $npc_group = NPCGroup::newFromArray($_POST);
        $npc_group->create();
    } elseif ($operation == 'update') {
        $npc_group=NPCGroup::loadById($_POST['Id']);
        $npc_group->setValuesByArray($_POST);
        $npc_group->update();
    }
    
    
    
    header('Location: ../npc.php');
}
