<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id']) && isset($_GET['houseId'])) {
        //echo "Laddar " . $_GET['id'] . "<br>";
        $houseId = $_GET['houseId'];
        $id = $_GET['id'];
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}

$house = House::loadById($houseId);
$person = Person::loadById($id);

if (!isset($house) || !isset($person)) {
    header('Location: ../index.php');
    exit;
}

if ($person->HouseId != $house->Id) {
    header('Location: ../index.php');
    exit;
}

$person->HouseId = null;
$person->update();


if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

header('Location: ../index.php?message=caretaker_removed');
exit;
