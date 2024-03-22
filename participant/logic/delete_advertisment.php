<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['id'])) {
        $id = $_GET['id'];
        
    }
}

$advertisment=Advertisment::loadById($id);
if ($advertisment->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din annons
    exit;
}

Advertisment::delete($id);

header('Location: ../index.php?message=advertisment_deleted');
exit;
