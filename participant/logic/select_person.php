<?php

global $root, $current_user, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['PersonId'])) {
        $person = Person::loadById($_POST['PersonId']);
        if ($person->UserId == $current_user->Id) {
            $current_person = $person;
            $_SESSION['PersonId'] = $current_person->Id;
        }
    }
}

header('Location: ../index.php');
exit;


