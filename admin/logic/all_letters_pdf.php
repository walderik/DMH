<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once '../header.php';
require_once $root . '/pdf/letter_pdf.php';

$arrayOfLetters = Letter::allApprovedBySelectedLARP($current_larp);
$pdf = new Letter_PDF();
$pdf->SetTitle(utf8_decode('Brev'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator(utf8_decode('Omnes Mundi'));
$pdf->SetSubject(utf8_decode('Brev'));
foreach ($arrayOfLetters as $letter)  {
    $pdf->nytt_brev($letter);
}

$pdf->Output();
