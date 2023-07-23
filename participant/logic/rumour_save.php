<?php
global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $rumour = Rumour::newFromArray($_POST);
        $rumour->Approved = 0;
        $rumour->UserId = $current_user->Id;
        $rumour->create();
        
    } elseif ($operation == 'update') {
        $rumour=Rumour::loadById($_POST['Id']);
        if ($rumour->UserId != $current_user->Id) {
            header('Location: index.php'); //Inte ditt brev
            exit;
        }
        
        $rumour->setValuesByArray($_POST);
        $rumour->Approved = 0;
        $rumour->UserId = $current_user->Id;
        $rumour->update();
    }
}

header('Location: ../index.php');