<?php

include_once 'all_includes.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $person = Person::newFromArray($_POST);
        $person->create();
    } elseif ($operation == 'delete') {
        Person::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        
        $person = Person::newFromArray($_POST);
        $person->update();
    } else {
        echo $operation;
    }
    header('Location: ../participant/index.php');
}
