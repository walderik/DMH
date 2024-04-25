<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/bookkeeping_pdf.php';
include_once '../header.php';

$bookkeepings = Bookkeeping::allByLARP($current_larp);
$pdf = new Bookkeeping_PDF();
$pdf->SetTitle(encode_utf_to_iso('Alla verifikationer'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->SetSubject(encode_utf_to_iso('Alla verifikatoner'));

$pdf->printBookkeepings($bookkeepings);

$invoices = Invoice::getAllNormalInvoices($current_larp);
$pdf->printInvoices($invoices);


$pdf->Output();
