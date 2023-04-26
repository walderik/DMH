<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id']) && isset($_GET['type'])) {
        //echo "Laddar " . $_GET['id'] . "<br>";
        $type = $_GET['type'];
        $id = $_GET['id'];
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}

switch ($type) {
    case "house":
        $object = House::loadById($id);
        break;
    case "prop":
        $object = Prop::loadById($id);
        break;
}


if (!isset($object)) {
    header('Location: index.php');
    exit;
}

$object->ImageId = null;
$object->update();
Image::delete($object->ImageId);



if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

header('Location: ../index.php?message=image_deleted');
exit;
