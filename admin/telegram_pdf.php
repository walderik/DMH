<?php 
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/telegram_pdf.php';



//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


$arrayOfTelegrams = Telegram::allBySelectedLARP($current_larp);
$pdf = new TELEGRAM_PDF();
$pdf->SetTitle('Telegram');
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject('Telegram');
foreach ($arrayOfTelegrams as $telegram)  {
    $pdf->nytt_telegram($telegram);
}
// $doc = $pdf->Output('S');

// $attachments = ['Telegrammen' => $doc];
//BerghemMailer::send('Mats.rappe@yahoo.se', 'Admin', "Det här är alla telegrammen", "Alla Telegrammen som PDF", $attachments);

$pdf->Output();
