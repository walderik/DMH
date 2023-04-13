<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['id'])) {
        Housing::deleteHousing($current_larp->Id, $_POST['id']);

    } else {
        
        header('Location: ../index.php');
        exit;
    }
}



header('Location: ../housing.php');
exit;
