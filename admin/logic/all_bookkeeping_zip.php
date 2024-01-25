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
$bookkeepings = Bookkeeping::allByLARP($current_larp);
$pdf = new Bookkeeping_PDF();
$pdf->SetTitle(utf8_decode('Alla verifikationer'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator(utf8_decode('Omnes Mundi'));
$pdf->SetSubject(utf8_decode('Alla verifikatoner'));
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
    $pdf->SetAuthor(utf8_decode($current_larp->Name));
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