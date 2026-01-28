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

$listname = 'Alla huvudkaraktärer, med spelare';

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $listname, $current_larp->Name, false);



$roles = Role::getAllMainRoles($current_larp, false);


$rows = array();
$header = array("Karaktär", "Deltagare", "");

foreach ($roles as $role) {
    $person = $role->getPerson();
    if (is_null($person)) $name = "NPC";
    else $name = $person->Name;
    $rows[] = array($role->Name, $name,"                                           ");
}
// add a page
$pdf->AddPage();
// print table
$pdf->Table($listname, $header, $rows);



// close and output PDF document
ob_end_clean(); 
$pdf->Output($listname.'.pdf', 'I');
