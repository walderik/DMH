<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $npc = NPC::newFromArray($_POST);
        $npc->create();
    } elseif ($operation == 'update') {
               
        $npc=NPC::loadById($_POST['Id']);
        $npc->setValuesByArray($_POST);  
        $npc->update();
    }
    
    
    
    header('Location: ../npc.php');
}
