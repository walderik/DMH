<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

include_once '../header.php';
require_once $root . '/pdf/letter_pdf.php';

$arrayOfLetters = Letter::allApprovedBySelectedLARP($current_larp);
$pdf = new Letter_PDF();
$pdf->SetTitle(encode_utf_to_iso('Brev'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->SetSubject(encode_utf_to_iso('Brev'));
foreach ($arrayOfLetters as $letter)  {
    $pdf->nytt_brev($letter);
}

$pdf->Output();
