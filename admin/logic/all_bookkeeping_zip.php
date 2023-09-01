<?php
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/bookkeeping_pdf.php';
include_once '../header.php';




$zipname = "Verifikationer $current_larp->Name.zip";
$zip = new ZipArchive;
$zip->open($zipname, ZipArchive::CREATE);


//Skapa pdf med alla vanliga verifikationer
$bookkeepings = Bookkeeping::allByLARP($current_larp);
$pdf = new Bookkeeping_PDF();
$pdf->SetTitle(utf8_decode('Alla verifikationer'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator(utf8_decode('Omnes Mundos'));
$pdf->SetSubject(utf8_decode('Alla verifikatoner'));
$pdf->printBookkeepings($bookkeepings);

$zip->addFromString('Alla verifikatoner.pdf',$pdf->Output('S'));


//Lägg till alla pdf'verifikationer
$pdf_images = Image::getAllPDFVerifications($current_larp);
foreach ($pdf_images as $pdf_image) {
    $zip->addFromString($pdf_image->file_name, $pdf_image->file_data);
}

$zip->close();

///Then download the zipped file.
header('Content-Type: application/zip');
header('Content-disposition: attachment; filename='.$zipname);
header('Content-Length: ' . filesize($zipname));
readfile($zipname);