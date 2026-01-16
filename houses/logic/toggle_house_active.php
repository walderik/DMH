<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset( $_POST['houseId'])) {
        header('Location: ../index.php?post');
        exit;
    }
    $houseId = $_POST['houseId'];
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset( $_GET['houseId'])) {
        header('Location: ../index.php?get');
        exit;
    }
    $houseId = $_GET['houseId'];
    
}

if (!isset($houseId)) {
    header('Location: ../index.php');
    exit;
}


$house = House::loadById($houseId);


if ($house->IsActive()) $house->Active = 0;
else $house->Active = 1;

$house->update();

if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}


header('Location: ../house_admin.php');