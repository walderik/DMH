<?php
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/bookkeeping_pdf.php';
require_once $root . '/pdf/invoice_payed_pdf.php';
include_once '../header.php';

$campaignId = $current_larp->CampaignId;
$year = $_GET['year'];
if (isset($_GET['capaignId'])) $campaignId = $_GET['capaignId'];

$campaign = Campaign::loadById($campaignId);



$file = tempnam($root . '/tmp', 'zip');
register_shutdown_function('unlink', $file);
$zipname = encode_utf_to_iso("Verifikationer_".$campaign->Name."_".$year.".zip");
$zip = new ZipArchive;
$zip->open($file, ZipArchive::OVERWRITE);


//Skapa pdf med alla vanliga verifikationer
$bookkeepings = Bookkeeping::allFinishedCampaign($campaign, $year);
$pdf = new Bookkeeping_PDF();
$pdf->SetTitle(encode_utf_to_iso('Alla verifikationer för '.$campaign->Name));
$pdf->SetAuthor(encode_utf_to_iso($campaign->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->SetSubject(encode_utf_to_iso('Alla verifikatoner'));
$pdf->printBookkeepings($bookkeepings);

$zip->addFromString('Alla verifikationer för '.$campaign->Name.'.pdf',$pdf->Output('S'));

//Lägg till alla pdf'verifikationer
$pdf_images = Image::getAllPDFVerificationsCampaign($campaign, $year);
foreach ($pdf_images as $pdf_image) {
    $zip->addFromString($campaign->Name.'_'.$pdf_image->file_name, $pdf_image->file_data);
}


$larps = LARP::getAllForYear($campaign->Id, $year);
foreach ($larps as $larp) {
    //Skapa pdf med alla vanliga verifikationer
        $bookkeepings = Bookkeeping::allFinished($larp);
    $pdf = new Bookkeeping_PDF();
    $pdf->SetTitle(encode_utf_to_iso('Alla verifikationer för '.$larp->Name));
    $pdf->SetAuthor(encode_utf_to_iso($larp->Name));
    $pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
    $pdf->SetSubject(encode_utf_to_iso('Alla verifikatoner'));
    $pdf->printBookkeepings($bookkeepings);
    
    $invoices = Invoice::getAllNormalInvoices($larp);
    $pdf->printInvoices($invoices);
    
    
    $zip->addFromString('Alla verifikationer för '.$larp->Name.'.pdf',$pdf->Output('S'));
    
    
    //Lägg till alla pdf'verifikationer
    $pdf_images = Image::getAllPDFVerifications($larp);
    foreach ($pdf_images as $pdf_image) {
        $zip->addFromString($larp->Name.'_'.$pdf_image->file_name, $pdf_image->file_data);
    }
    
    foreach ($invoices as $invoice) {
        $pdf = new Invoice_payed_PDF();
        $pdf->SetTitle('Faktura');
        $pdf->SetAuthor(encode_utf_to_iso($larp->Name));
        $pdf->SetCreator('Omnes Mundi');
        $pdf->AddFont('SpecialElite','');
        $pdf->SetSubject('Faktura');
        $pdf->ny_faktura($invoice);
    
        $zip->addFromString('Faktura '.$invoice->Number.'_'.$larp->Name.'.pdf',$pdf->Output('S'));
    }
}
$zip->close();

///Then download the zipped file.
header('Content-Type: application/zip');
header('Content-disposition: attachment; filename='.$zipname);
header('Content-Length: ' . filesize($file));
readfile($file);