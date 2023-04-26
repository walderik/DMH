<?php
include_once '../header.php';

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
