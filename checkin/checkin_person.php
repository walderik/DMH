<?php
include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $PersonId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$person = Person::loadById($PersonId);

if (!$person->isRegistered($current_larp) && !$person->isReserve($current_larp)) {
    header('Location: index.php'); // personen är inte anmäld
    exit;
}

$registration = Registration::loadByIds($person->Id, $current_larp->Id);


include 'navigation.php';
?>


