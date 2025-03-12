<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$name = 'Närmaste anhörig';

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $name, $current_larp->Name, false);


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$persons = Person::getAllRegistered($current_larp, false);
usort($persons, "cmp");
$rows = array();
$header = array("Namn", "Kontaktperson");
$widths = array(200, 400);
foreach ($persons as $person) {
    $rows[] = array($person->Name, $person->EmergencyContact);
}
// add a page
$pdf->AddPage();
// print table
$pdf->Table($name, $header, $rows, $widths);



// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
