<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/telegram_pdf.php';
include_once '../header.php';

$arrayOfTitledeeds = Telegram::allApprovedBySelectedLARP($current_larp);
$pdf = new TELEGRAM_PDF();
$pdf->SetTitle(encode_utf_to_iso('Telegram'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject(encode_utf_to_iso('Telegram'));
foreach ($arrayOfTitledeeds as $telegram)  {
    $pdf->nytt_telegram($telegram);
}

$pdf->Output();
