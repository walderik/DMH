<?php
global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $advertisment = Advertisment::newFromArray($_POST);
        $advertisment->PersonId = $current_person->Id;
        $advertisment->create();
        
    } elseif ($operation == 'update') {
        $advertisment=Advertisment::loadById($_POST['Id']);
        if ($advertisment->PersonId != $current_person->Id) {
            header('Location: index.php'); //Inte din annons
            exit;
        }
        
        $advertisment->setValuesByArray($_POST);
        $advertisment->PersonId = $current_person->Id;
        $advertisment->update();
    }
}

header('Location: ../advertisments.php');