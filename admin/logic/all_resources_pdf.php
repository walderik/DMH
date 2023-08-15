<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/resource_pdf.php';
include_once '../header.php';

$arrayOfTitledeeds = Titledeed::allByCampaign($current_larp, false);
$pdf = new RESOURCE_PDF();
$pdf->SetTitle(utf8_decode('Alla resurser för alla lagfarter'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->SetSubject(utf8_decode('Alla resurser för alla lagfarter'));
$pdf->all_resources($arrayOfTitledeeds, $current_larp);

$pdf->Output();
