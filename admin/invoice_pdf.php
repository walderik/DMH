<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

include_once 'header.php';

require_once $root . '/pdf/invoice_pdf.php';
require_once $root . '/pdf/invoice_payed_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}

if (isset($_GET['invoiceId'])) {
    $invoiceId = $_GET['invoiceId'];
    $invoice = Invoice::loadById($invoiceId);
    if (empty($invoice)) {
        header('Location: index.php'); // Posten finns inte
        exit;
    } elseif ($invoice->LARPId != $current_larp->Id) {
        header('Location: index.php'); // Tillhör inte aktuellt lajv
        exit;
    }
    if ($invoice->isPayed() && isset($_GET['showPayed'])) {
        $pdf = new Invoice_payed_PDF();
    } else {
        $pdf = new Invoice_PDF();
    }

    $pdf->SetTitle('Faktura');
    $pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
    $pdf->SetCreator('Omnes Mundi');
    $pdf->AddFont('SpecialElite','');
    $pdf->SetSubject('Faktura');
    $pdf->ny_faktura($invoice);
    
} 


$pdf->Output();


