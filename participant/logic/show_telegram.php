<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/pdf/telegram_pdf.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $telegram=Telegram::loadById($_GET['id']);

    if ($telegram->PersonId != $current_person->Id) {
        header('Location: index.php'); //Inte ditt telegram
        exit;
    }


    $pdf = new TELEGRAM_PDF();
    $pdf->SetTitle('Telegram');
    $pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
    $pdf->SetCreator('Omnes Mundi');
    $pdf->AddFont('SpecialElite','');
    $pdf->SetSubject('Telegram');
    $pdf->nytt_telegram($telegram);
    
    $pdf->Output();
}