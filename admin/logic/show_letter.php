<?php
include_once '../header.php';

global $root;
require_once $root . '/pdf/letter_pdf.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $letter=Letter::loadById($_GET['id']);
   
    $pdf = new Letter_PDF();
    $pdf->SetTitle(encode_utf_to_iso('Brev'));
    $pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
    $pdf->SetCreator('Omnes Mundi');
    $pdf->SetSubject('Brev');
    $pdf->nytt_brev($letter);
    
    $pdf->Output();
}