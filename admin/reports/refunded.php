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

$name = 'Återbetalningar';

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $current_larp->Name, false);


$persons = Person::getAllRefunded($current_larp);
$rows = array();
$header = array("Namn", "Betalat", "Återbetalat", "Datum");

foreach ($persons as $person) {
    $registration = $person->getRegistration($current_larp);

    $rows[] = array($person->Name, $registration->AmountPayed, $registration->RefundAmount, $registration->RefundDate);

}

// add a page
$pdf->AddPage();
// print table
$pdf->Table($name, $header, $rows);

    
    
// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
