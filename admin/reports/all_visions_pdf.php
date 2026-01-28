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

$name = 'Tider för syner';

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $name, $current_larp->Name, false);


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$visions = Vision::allBySelectedLARP($current_larp);

$rows = array();
$header = array("När", "Syn", "Källa", "Bieffekt", "Vem", "Anteckningar");
foreach ($visions as $vision) {
    $has_roles = $vision->getHas();
    $has_roles_arr = array();
    foreach ($has_roles as $has_role) {
        $has_roles_arr[] = $has_role->Name;
        
    }
    
    $rows[] = array($vision->getWhenStr(), 
        $vision->VisionText, $vision->Source, 
        $vision->SideEffect, 
        implode(", ", $has_roles_arr),
        $vision->OrganizerNotes);
}
// add a page
$pdf->AddPage('L');
// print table
$pdf->Table($name, $header, $rows);



// close and output PDF document
ob_end_clean(); 
$pdf->Output($name.'.pdf', 'I');