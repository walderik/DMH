<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/titledeed_pdf.php';
include_once '../header.php';

$type = 1;
if (isset($_GET['type'])) $type = $_GET['type'];

$pdf = new TITLEDEED_PDF();
$pdf->SetTitle(encode_utf_to_iso('Alla grupper'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->SetSubject(encode_utf_to_iso('Alla grupper'));
$pdf->groupSummaries($current_larp, $type);

$pdf->Output();
