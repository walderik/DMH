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

$name = 'Avgifter';

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $current_larp->Name, false);


$persons = Person::getAllRegistered($current_larp, true);

$rows = array();
$header = array("Namn", "Datum", "Summa", "Kommentar");
$widths = array(300, 100, 100);
foreach ($persons as $person) {
    $registration = $person->getRegistration($current_larp);
    if (!empty($registration->AmountPayed) && $registration->AmountPayed > 0) $rows[] = array($person->Name, $registration->Payed, $registration->AmountPayed, $registration->PaymentComment);
}

usort($rows, function ($a, $b) {
    $a_val =  strtotime($a[1]);
    $b_val =  strtotime($b[1]);
    
    if($a_val > $b_val) return 1;
    if($a_val < $b_val) return -1;
    return 0;
});

// add a page
$pdf->AddPage();
// print table
$pdf->Table("Inbetalade avgifter", $header, $rows, $widths);


$rows = array();
$header = array("Namn", "Datum", "Summa");
$widths = array(300, 100, 100);
foreach ($persons as $person) {
    $registration = $person->getRegistration($current_larp);
    if (!empty($registration->RefundAmount) && $registration->RefundAmount > 0) $rows[] = array($person->Name, $registration->RefundDate, $registration->RefundAmount);
}

if (!empty($rows) && sizeof($rows) > 1) {

    usort($rows, function ($a, $b) {
        $a_val =  strtotime($a[1]);
        $b_val =  strtotime($b[1]);
        
        if($a_val > $b_val) return 1;
        if($a_val < $b_val) return -1;
        return 0;
    });
    
    // add a page
    $pdf->AddPage();
    // print table
    $pdf->Table("Återbetalade avgifter", $header, $rows, $widths);
}



// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
