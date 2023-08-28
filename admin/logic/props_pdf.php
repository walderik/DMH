<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/props_pdf.php';
include_once '../header.php';

$props = Prop::allByCampaign($current_larp);
$pdf = new Props_PDF();
$pdf->SetTitle(utf8_decode('Rekvisita'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator(utf8_decode('Omnes Mundos'));
$pdf->SetSubject(utf8_decode('Rekvisita'));
$pdf->printProps($props);

$pdf->Output();
