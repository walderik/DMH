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

$name = 'Lista';

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $name, $current_larp->Name, false);



$roles = Role::getAllMainRoles($current_larp, false);


$rows = array();
$header = array("Karaktär", "Deltagare", "");

foreach ($roles as $role) {
    $rows[] = array($role->Name, $role->getPerson()->Name,"                                           ");
}
// add a page
$pdf->AddPage();
// print table
$pdf->Table($name, $header, $rows);



// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
