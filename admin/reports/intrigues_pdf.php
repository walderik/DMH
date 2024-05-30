<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$one_intrigue_id = null;

if (isset($_GET['Id'])) $one_intrigue_id = $_GET['Id'];


$name = 'Alla intriger';

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $current_larp->Name, false);




$intrigue_array = Intrigue::allByLARP($current_larp);

foreach ($intrigue_array as $intrigue) {
    if (!$intrigue->isActive()) continue;
    if (isset($one_intrigue_id) && $one_intrigue_id != $intrigue->Id) continue;
    $rows = array();
    $header = array("Ansvarig", $intrigue->getResponsibleUser()->Name);
    $widths = array(100, 500);
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
    
    //Syner
    $intrigue_visions = $intrigue->getAllVisions();
    if (!empty($intrigue_visions)) {
        $vision_text_array = array();
        foreach ($intrigue_visions as $intrigue_vision) {
            $vision = $intrigue_vision->getVision();
            $has_vision = $vision->getHas();
            $has_vision_array = array();
            foreach($has_vision as $has) $has_vision_array[] = "$has->Name";
            $visionTxt = $vision->getWhenStr() . ": ". $vision->VisionText;
            $has_txt = "Kommmer att ha synen: ". implode(", ", $has_vision_array);
            $vision_text_array[] = $visionTxt . "\n". $has_txt;
        }
        
        $rows[] = array("Syner", "* ".implode("\n\n* ", $vision_text_array));
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
    
    // add a page
    $pdf->AddPage();
    // print table
    $pdf->Table("$intrigue->Number. $intrigue->Name", $header, $rows, $widths, true);

}
    

// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
