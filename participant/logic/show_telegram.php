<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/telegram_pdf.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $telegram=Telegram::loadById($_GET['id']);

    if ($telegram->UserId != $current_user->Id) {
        header('Location: index.php'); //Inte ditt telegram
        exit;
    }


    $pdf = new TELEGRAM_PDF();
    $pdf->SetTitle('Telegram');
    $pdf->SetAuthor(utf8_decode($current_larp->Name));
    $pdf->SetCreator('Omnes Mundi');
    $pdf->AddFont('SpecialElite','');
    $pdf->SetSubject('Telegram');
    $pdf->nytt_telegram($telegram);
    
    $pdf->Output();
}