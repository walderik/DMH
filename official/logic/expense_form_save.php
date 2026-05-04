<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
        
        if ($operation == 'add_expense') {
            $bookkeeping = Bookkeeping::newFromArray($_POST);
            $bookkeeping->Amount = 0 - $_POST['Amount'];
            $bookkeeping->create();
            saveReceipt($bookkeeping);
        } elseif ($operation == 'update_expense') {
            $bookkeeping = Bookkeeping::loadById($_POST['id']);
            $bookkeeping->setValuesByArray($_POST);
            $bookkeeping->Amount = 0 - $_POST['Amount'];
            
            $bookkeeping->update();
            saveReceipt($bookkeeping);
        }
        header("Location: ../index.php");
        exit;
    }
 }

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Bookkeeping::delete($_GET['id']);
        header("Location: ../index.php");
        exit;
    }
}


function saveReceipt(Bookkeeping $bookkeeping) {
    global $error_code, $error_message;
    if (!empty($_FILES["upload"]["name"])) {
        $error = Image::maySave(true);
        if (!isset($error)) {
            $id = Image::saveImage("Verifikation $bookkeeping->Number", true);
            $bookkeeping->ImageId = $id;
            $bookkeeping->update();
        } else {
            $error_code = $error;
            $error_message = getErrorText($error_code);
        }
    }
    
}

