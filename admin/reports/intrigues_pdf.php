<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$one_intrigue = null;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['Id'])) $one_intrigue = Intrigue::loadById($_GET['Id']);
}

$name = 'Alla intriger';

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));




$intrigue_array = Intrigue::allByLARP($current_larp);

foreach ($intrigue_array as $intrigue) {
    if (!$intrigue->isActive()) continue;
    $rows = array();
    $rows[] = array("Ansvarig", $intrigue->getResponsibleUser()->Name);
    $rows[] = array("Anteckningar", $intrigue->Notes);

    //Brev
    $intrigue_letters = $intrigue->getAllLetters();
    if (!empty($intrigue_letters)) {
        $letter_text_array = array();
        foreach ($intrigue_letters as $intrigue_letter) {
            $letter = $intrigue_letter->getLetter();
            $letter_text_array[] = "Från: $letter->Signature, Till: $letter->Recipient, $letter->Message";
        }
        
        $rows[] = array("Brev", "* ".implode("\n\n* ", $letter_text_array));
    }
    
    //Telegram
    $intrigue_telegrams = $intrigue->getAllTelegrams();
    if (!empty($intrigue_telegrams)) {
        $telegram_text_array = array();
        foreach ($intrigue_telegrams as $intrigue_telegram) {
            $telegram = $intrigue_telegram->getTelegram();
            $telegram_text_array[] = "$telegram->Deliverytime, Från: $telegram->Sender, Till: $telegram->Reciever, $telegram->Message";
        }
        
        $rows[] = array("Telegram", "* ".implode("\n\n* ", $telegram_text_array));
    }
    
    //Rykten
    $rumours = $intrigue->getRumours();
    if (!empty($rumours)) {
        $rumour_text_array = array();
        foreach ($rumours as $rumour) {
            $knows_rumour = $rumour->getKnows();
            $knows_rumour_array = array();
            foreach($knows_rumour as $knows) $knows_rumour_array[] = $knows->getName();
            $rumour_text_array[] = $rumour->Text . "\nVet om: ". implode(", ", $knows_rumour_array);
        }
        
        $rows[] = array("Rykten", "* ".implode("\n\n* ", $rumour_text_array));
    }
    
    $groupActors = $intrigue->getAllGroupActors();
    foreach($groupActors as $groupActor) {
        if (empty($groupActor->IntrigueText)) continue;
        $text = $groupActor->IntrigueText;
        if (!empty($groupActor->WhatHappened)) $text .= "\n\nVAD HÄNDE:\n" . $groupActor->WhatHappened;
        $rows[] = array($groupActor->getGroup()->Name, $text);
    }
    $roleActors = $intrigue->getAllRoleActors();
    foreach($roleActors as $roleActor) {
        if (empty($roleActor->IntrigueText)) continue;
        $text = $roleActor->IntrigueText;
        if (!empty($roleActor->WhatHappened)) $text .= "\n\nVAD HÄNDE:\n" . $roleActor->WhatHappened;
        $rows[] = array($roleActor->getRole()->Name, $text);
    }
    
    
    $pdf->new_report($current_larp, "$intrigue->Number. $intrigue->Name", $rows);
}
    
    
    
$pdf->Output();
