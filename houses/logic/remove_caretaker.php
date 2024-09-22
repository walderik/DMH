<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id']) && isset($_GET['houseId'])) {
        //echo "Laddar " . $_GET['id'] . "<br>";
        $houseId = $_GET['houseId'];
        $personId = $_GET['id'];
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}

$housecaretaker = Housecaretaker::loadByIds($houseId, $personId);

if (!isset($housecaretaker)) {
    header('Location: ../index.php?message=caretaker_removed');
    exit;
}

$housecaretaker->destroy();


if (isset($_SERVER['HTTP_REFERER'])) {
    $url = $_SERVER['HTTP_REFERER'];
    $url = preg_replace('/(&|\?)'.preg_quote('person_id').'=[^&]*$/', '', $url);
    $url = preg_replace('/(&|\?)'.preg_quote('person_id').'=[^&]*&/', '$1', $url);
    $url .= '&message=caretaker_removed';
    
    header('Location: ' . $url);
    exit;
}

header('Location: ../index.php?message=caretaker_removed');
exit;
