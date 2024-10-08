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
    case "npc":
        $object = NPC::loadById($id);
        break;
    case "resource":
        $object = Resource::loadById($id);
        break;
    case "role":
        $object = Role::loadById($id);
        break;
    case "group":
        $object = Group::loadById($id);
        break;
    case "bookkeeping":
        $object = Bookkeeping::loadById($id);
        break;
}


if (!isset($object)) {
    header('Location: ../index.php');
    exit;
}

$imageId = $object->ImageId;
$object->ImageId = null;
$object->update();
Image::delete($imageId);


if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

header('Location: ../index.php?message=image_deleted');
exit;
