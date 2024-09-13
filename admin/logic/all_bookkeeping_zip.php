<?php
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/bookkeeping_pdf.php';
require_once $root . '/pdf/invoice_payed_pdf.php';
include_once '../header.php';



$file = tempnam($root . '/tmp', 'zip');
register_shutdown_function('unlink', $file);
$zipname = "Verifikationer $current_larp->Name.zip";
$zip = new ZipArchive;
$zip->open($file, ZipArchive::OVERWRITE);


//Skapa pdf med alla vanliga verifikationer
$bookkeepings = Bookkeeping::allFinished($current_larp);
$pdf = new Bookkeeping_PDF();
$pdf->SetTitle(encode_utf_to_iso('Alla verifikationer'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->SetSubject(encode_utf_to_iso('Alla verifikatoner'));
$pdf->printBookkeepings($bookkeepings);

$invoices = Invoice::getAllNormalInvoices($current_larp);
$pdf->printInvoices($invoices);


$zip->addFromString('Alla verifikatoner.pdf',$pdf->Output('S'));


//Lägg till alla pdf'verifikationer
$pdf_images = Image::getAllPDFVerifications($current_larp);
foreach ($pdf_images as $pdf_image) {
    $zip->addFromString($pdf_image->file_name, $pdf_image->file_data);
}

foreach ($invoices as $invoice) {
    $pdf = new Invoice_payed_PDF();
    $pdf->SetTitle('Faktura');
    $pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
    $pdf->SetCreator('Omnes Mundi');
    $pdf->AddFont('SpecialElite','');
    $pdf->SetSubject('Faktura');
    $pdf->ny_faktura($invoice);

    $zip->addFromString('Faktura '.$invoice->Number.'.pdf',$pdf->Output('S'));
}

$zip->close();

///Then download the zipped file.
header('Content-Type: application/zip');
header('Content-disposition: attachment; filename='.$zipname);
header('Content-Length: ' . filesize($file));
readfile($file);