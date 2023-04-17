<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/letter_pdf.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $letter=Letter::loadById($_GET['id']);
   
    $pdf = new Letter_PDF();
    $pdf->SetTitle('Telegram');
    $pdf->SetAuthor(utf8_decode($current_larp->Name));
    $pdf->SetCreator('Omnes Mundos');
    $pdf->SetSubject('Brev');
    $pdf->nytt_brev($letter);
    
    $pdf->Output();
}