<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['person_id']) && isset($_GET['houseId'])) {
        //echo "Laddar " . $_GET['id'] . "<br>";
        $houseId = $_GET['houseId'];
        $personId = $_GET['person_id'];
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
    
    if (str_contains($url, 'housecaretakers_admin.php')) {
        header('Location: ../housecaretakers_admin.php?message=caretaker_removed');
        exit;
    }
    $url = preg_replace('/(&|\?)'.preg_quote('person_id').'=[^&]*$/', '', $url);
    $url = preg_replace('/(&|\?)'.preg_quote('person_id').'=[^&]*&/', '$1', $url);
    $url = preg_replace('/(&|\?)'.preg_quote('message').'=[^&]*$/', '', $url);
    $url = preg_replace('/(&|\?)'.preg_quote('message').'=[^&]*&/', '$1', $url);
    $url = preg_replace('/(&|\?)'.preg_quote('error').'=[^&]*$/', '', $url);
    $url = preg_replace('/(&|\?)'.preg_quote('error').'=[^&]*&/', '$1', $url);
    $url .= '&message=caretaker_removed';
    
    header('Location: ' . $url);
    exit;
}

header('Location: ../index.php?message=caretaker_removed');
exit;
