<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $person = Person::newFromArray($_POST);
        if (Person::SSNAlreadyExists($person->SocialSecurityNumber)) {
            header('Location: ../index.php?error=SSN_already_in_use');
            exit;
        }
        $person->create();
        $person->saveAllNormalAllergyTypes($_POST);
    } elseif ($operation == 'update') {
        $person=Person::loadById($_POST['Id']);

        if ($person->UserId != $current_user->Id) {
            header('Location: index.php'); //Inte din person
            exit;
        }
        
        $person->setValuesByArray($_POST);
        
        $person->update();
        $person->deleteAllNormalAllergyTypes();
        $person->saveAllNormalAllergyTypes($_POST);
        
    } 
    header('Location: ../index.php');
}
