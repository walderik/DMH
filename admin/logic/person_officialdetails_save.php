<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
include_once $root . '/includes/all_includes.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $officialDetails = $_POST['OfficialDetails'];
    $personId = $_POST['Id'];
    $person = Person::loadById($personId);

    if (isset($person)) {
        $person->OfficialDetails = $officialDetails;
        $person->update();
    }
}

header('Location: ../officials.php');
    exit;
?>