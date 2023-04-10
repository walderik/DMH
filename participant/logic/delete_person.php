<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $person = Person::loadById($_GET['id']);
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($person)) {
    header('Location: ../index.php');
    exit;
}


if ($person->UserId != $current_user->Id) {
    header('Location: ../index.php'); //Inte din person
    exit;
}

$roles = $person->getRoles();
$groups = $person->getGroups();

//Kolla om personen har karaktärer 
if (isset($roles) && count($roles) > 0) {
    header('Location: ../index.php'); 
    exit;
    
}

//Kolla om personen har grupper
if (isset($groups) && count($groups) > 0) {
    header('Location: ../index.php'); 
    exit;   
}


Person::delete($person->Id);

header('Location: ../index.php?message=person_deleted');
exit;
