<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];


require_once $root . '/pdf/props_pdf.php';
include_once '../header.php';

$props = Prop::allByCampaign($current_larp);
$pdf = new Props_PDF();
$pdf->SetTitle(encode_utf_to_iso('Rekvisita'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->SetSubject(encode_utf_to_iso('Rekvisita'));
$pdf->printProps($props);

$pdf->Output();
