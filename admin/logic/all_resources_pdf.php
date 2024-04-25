<?php 
global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/resource_pdf.php';
include_once '../header.php';

$type = RESOURCE_PDF::Handwriting;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $type = $_GET['type'];
}

$arrayOfTitledeeds = Titledeed::allByCampaign($current_larp, false);
$pdf = new RESOURCE_PDF();
$pdf->SetTitle(encode_utf_to_iso('Alla resurser för alla verksamheter'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->SetSubject(encode_utf_to_iso('Alla resurser för alla verksamheter'));
$pdf->all_resources($arrayOfTitledeeds, $type, $current_larp);

$pdf->Output();


