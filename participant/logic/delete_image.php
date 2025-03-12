<?php

global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id']) && isset($_GET['type'])) {
        //echo "Laddar " . $_GET['id'] . "<br>";
        $type = $_GET['type'];
        $id = $_GET['id'];
        
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['id']) && isset($_POST['type'])) {
        $type = $_GET['type'];
        $id = $_GET['id'];
        
    }
}


switch ($type) {
    case "role":
        $object = Role::loadById($id);
        if ($object->PersonId != $current_person->Id) {
            header('Location: ../index.php'); //Inte din karaktär
            exit;
        }
        break;
    case "group":
        $object = Group::loadById($id);
        if (!$current_person->isGroupLeader($object)) {
            header('Location: ../index.php');
            exit;
        }
        break;
    case "npc":
        $object = NPC::loadById($id);
        if ($object->PersonId != $current_person->Id) {
            header('Location: ../index.php');
            exit;
        }
        break;
}


if (!isset($object)) {
    header('Location: ../index.php');
    exit;
}


$imageId = $object->ImageId;
$object->ImageId = null;
$object->update();
if (!empty($imageId)) Image::delete($imageId);


header('Location: ../index.php?message=image_deleted');
exit;
