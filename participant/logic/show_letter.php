<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/letter_pdf.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $letter=Letter::loadById($_GET['id']);
    
    if ($letter->UserId != $current_user->Id) {
        header('Location: index.php'); //Inte ditt brev
        exit;
    }
    
    
    $pdf = new Letter_PDF();
    $pdf->SetTitle('Telegram');
    $pdf->SetAuthor(utf8_decode($current_larp->Name));
    $pdf->SetCreator('Omnes Mundos');
    $pdf->SetSubject('Brev');
    $pdf->nytt_brev($letter);
    
    $pdf->Output();
}