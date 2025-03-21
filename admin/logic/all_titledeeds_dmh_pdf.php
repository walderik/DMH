<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];


require_once $root . '/pdf/titledeed_pdf.php';
include_once '../header.php';

$arrayOfTitledeeds = Titledeed::allByCampaign($current_larp, false);
$pdf = new TITLEDEED_PDF();
$pdf->SetTitle(encode_utf_to_iso('Ägarbevisen'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->SetSubject(encode_utf_to_iso('Alla ägarbevis'));
$pdf->all_titledeedsDMH($arrayOfTitledeeds, $current_larp);

$pdf->Output();
