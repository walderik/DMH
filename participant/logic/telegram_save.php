<?php
global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $telegram = Telegram::newFromArray($_POST);
        $telegram->Approved = 0;
        $telegram->PersonId = $current_person->Id;
        $telegram->create();

    } elseif ($operation == 'update') {
        $telegram=Telegram::loadById($_POST['Id']);
        if ($telegram->PersonId != $current_person->Id) {
            header('Location: index.php'); //Inte ditt telegram
            exit;
        }
        
        $telegram->setValuesByArray($_POST);
        $telegram->Approved = 0;
        $telegram->PersonId = $current_person->Id;
        $telegram->update();
    } 
}

header('Location: ../index.php');