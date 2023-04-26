<?php 
include_once '../header.php';

$arrayOfTelegrams = Telegram::allApprovedBySelectedLARP($current_larp);
$pdf = new TELEGRAM_PDF();
$pdf->SetTitle('Telegram');
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject('Telegram');
foreach ($arrayOfTelegrams as $telegram)  {
    $pdf->nytt_telegram($telegram);
}

$pdf->Output();
