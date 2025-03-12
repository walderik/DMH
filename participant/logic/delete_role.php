<?php

global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $role = Role::loadById($_GET['id']);
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


if (!isset($role)) {
    header('Location: ../index.php');
    exit;
}


if ($role->PersonId != $current_person->Id) {
    header('Location: ../index.php'); //Inte din karaktär
    exit;
}

Role::delete($role->Id);

header('Location: ../index.php?message=role_deleted');

