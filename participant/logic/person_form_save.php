<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

echo '$_POST :<br>';
print_r($_POST);

echo "<br />";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $person = Person::newFromArray($_POST);
        $person->create();
        $person->saveAllNormalAllergyTypes($_POST);
    } elseif ($operation == 'delete') {
        Person::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        
        $person = Person::newFromArray($_POST);
        $person->update();
        $person->deleteAllNormalAllergyTypes();
        $person->saveAllNormalAllergyTypes($_POST);
    } else {
        echo $operation;
    }
    header('Location: '.$root.'/participant/index.php');
}
