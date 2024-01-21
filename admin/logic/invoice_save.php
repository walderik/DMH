<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $invoice = Invoice::newFromArray($_POST);
        $invoice->create();
    } elseif ($operation == 'delete') {
        Invoice::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $invoice=Invoice::loadById($_POST['Id']);
        $invoice->setValuesByArray($_POST);
        $invoice->update();
    } elseif ($operation == 'invoice_contact') {
        if (isset($_POST['PersonId'])) {
            $invoice=Invoice::loadById($_POST['Id']);
            $invoice->ContactPersonId = $_POST['PersonId'];
            $invoice->update();
        }
    } elseif ($operation == 'invoice_add_concerns') {
        if (isset($_POST['PersonId'])) {
            $invoice=Invoice::loadById($_POST['Id']);
            $invoice->addConcernedPersons($_POST['PersonId']);
        }
    }
    
}


header('Location: ../invoice_admin.php');