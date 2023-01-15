<?php

include_once '../../includes/all_includes.php';

print_r($_POST);

echo "<br />";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $person = Person::newFromArray($_POST);
        print_r($person);
        echo "<br />";
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
