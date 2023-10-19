<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/telegram_pdf.php';
include_once '../header.php';

$arrayOfTitledeeds = Telegram::allApprovedBySelectedLARP($current_larp);
$pdf = new TELEGRAM_PDF();
$pdf->SetTitle(utf8_decode('Telegram'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator(utf8_decode('Omnes Mundi'));
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject(utf8_decode('Telegram'));
foreach ($arrayOfTitledeeds as $telegram)  {
    $pdf->nytt_telegram($telegram);
}

$pdf->Output();
