<?php
global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $telegram = Telegram::newFromArray($_POST);
        $telegram->Approved = 0;
        $telegram->UserId = $current_user->Id;
        $telegram->create();

    } elseif ($operation == 'update') {
        $telegram=Telegram::loadById($_POST['Id']);
        if ($telegram->UserId != $current_user->Id) {
            header('Location: index.php'); //Inte ditt telegram
            exit;
        }
        
        $telegram->setValuesByArray($_POST);
        $telegram->Approved = 0;
        $telegram->UserId = $current_user->Id;
        $telegram->update();
    } 
}

header('Location: ../index.php');