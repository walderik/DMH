<?php
global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $letter = Letter::newFromArray($_POST);
        $letter->Approved = 0;
        $letter->PersonId = $current_person->Id;
        $letter->create();
        
    } elseif ($operation == 'update') {
        $letter=Letter::loadById($_POST['Id']);
        if ($letter->PersonId != $current_person->Id) {
            header('Location: index.php'); //Inte ditt brev
            exit;
        }
        
        $letter->setValuesByArray($_POST);
        $letter->Approved = 0;
        $letter->PersonId = $current_person->Id;
        $letter->update();
    }
}

header('Location: ../index.php');