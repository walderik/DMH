<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $role = Role::newFromArray($_POST);
        $role->create();
    } elseif ($operation == 'update') {
        

        $role = Role::newFromArray($_POST);
        if (Person::loadById($role->PersonId)->UserId != $current_user->Id) {
            header('Location: index.php'); //Inte din roll
            exit;
        }
        
        
        $role->update();

    } 
    header('Location: ../index.php');
}
