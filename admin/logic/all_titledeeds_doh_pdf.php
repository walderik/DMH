<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/titledeed_pdf.php';
include_once '../header.php';

$arrayOfTitledeeds = Titledeed::allByCampaign($current_larp, false);
$pdf = new TITLEDEED_PDF();
$pdf->SetTitle(utf8_decode('Verksamheter'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->SetSubject(utf8_decode('Alla verksamheter'));
$pdf->all_titledeedsDOH($arrayOfTitledeeds, $current_larp);

$pdf->Output();
