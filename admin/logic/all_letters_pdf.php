<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once '../header.php';
require_once $root . '/pdf/letter_pdf.php';

$arrayOfLetters = Letter::allApprovedBySelectedLARP($current_larp);
$pdf = new Letter_PDF();
$pdf->SetTitle('Brev');
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->SetSubject('Brev');
foreach ($arrayOfLetters as $letter)  {
    $pdf->nytt_brev($letter);
}

$pdf->Output();
