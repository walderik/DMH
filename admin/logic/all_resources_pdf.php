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
$pdf->SetTitle(utf8_decode('Alla resurser för alla lagfarter'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->SetSubject(utf8_decode('Alla resurser för alla lagfarter'));
$pdf->all_resources($arrayOfTitledeeds, $type);

$pdf->Output();


