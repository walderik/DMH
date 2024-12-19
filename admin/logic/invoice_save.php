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
    } elseif ($operation == 'send_invoice') {
        $invoice=Invoice::loadById($_POST['Id']);
        BerghemMailer::sendInvoice($invoice, $current_person->Id);
    } elseif ($operation == 'mark_invoice_sent') {
        $invoice=Invoice::loadById($_POST['Id']);
        $invoice->setSent();
    } elseif ($operation == 'invoice_payment') {
        $invoice=Invoice::loadById($_POST['Id']);
        if (isset($_POST['AmountPayed'])) $invoice->AmountPayed = $_POST['AmountPayed'];
        if (isset($_POST['PayedDate'])) $invoice->PayedDate = $_POST['PayedDate'];
        $invoice->update();
        if($invoice->isPayed()) $invoice->markFeesPayed(); 
    }
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $operation = $_GET['operation'];

    if ($operation == 'delete_concerns') {
        $invoice=Invoice::loadById($_GET['Id']);
        $invoice->removeConcernedRegistration($_GET['registrationId']);
        header('Location: ../invoice_form.php?operation=update&id='.$invoice->Id);
        exit;
    }
    
}

header('Location: ../invoice_admin.php');