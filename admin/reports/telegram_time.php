<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$name = 'Körschema för telegram';



$telegrams = Telegram::allApprovedBySelectedLARP($current_larp);

$rows = array();
$header = array("Tid", "Mottagare", "Meddelande", "Anteckning");
foreach ($telegrams as $telegram) {
    $rows[] = array($telegram->Deliverytime, $telegram->Reciever, mb_strimwidth($telegram->Message, 0, 100, "..."), $telegram->OrganizerNotes);
}

// create new PDF document
$pdf = new Report_TCP_PDF();
$pdf->init($current_person->Name, $name, $current_larp->Name, false);

// add a page
$pdf->AddPage('L');

// print table
$pdf->Table($name, $header, $rows);

// close and output PDF document
$pdf->Output($name.'.pdf', 'I');


