<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $role = Role::loadById($_GET['id']);
    } else {
        
        header('Location: index.php');
        exit;
    }
}


if (!isset($role)) {
    header('Location: index.php');
    exit;
}


if (Person::loadById($role->PersonId)->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din roll
    exit;
}

$ih = ImageHandler::newWithDefault();
$id = $ih->deleteImage($role->ImageId);
$role->ImageId = null;
$role->update();
header('Location: ../index.php?message=image_deleted');
exit;
?>