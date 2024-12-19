<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/letter_pdf.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $letter=Letter::loadById($_GET['id']);
    
    if ($letter->PersonId != $current_person->Id) {
        header('Location: index.php'); //Inte ditt brev
        exit;
    }
    
    
    $pdf = new Letter_PDF();
    $pdf->SetTitle('Brev');
    $pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
    $pdf->SetCreator('Omnes Mundi');
    $pdf->SetSubject('Brev');
    $pdf->nytt_brev($letter);
    
    $pdf->Output();
}