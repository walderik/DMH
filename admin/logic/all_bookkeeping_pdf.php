<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/bookkeeping_pdf.php';
include_once '../header.php';

$bookkeepings = Bookkeeping::allByLARP($current_larp);
$pdf = new Bookkeeping_PDF();
$pdf->SetTitle(utf8_decode('Alla verifikationer'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator(utf8_decode('Omnes Mundi'));
$pdf->SetSubject(utf8_decode('Alla verifikatoner'));
$pdf->printBookkeepings($bookkeepings);

$pdf->Output();
