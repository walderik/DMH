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

$name = 'Tider för syner';

$pdf = new Report_PDF('L');

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$visions = Vision::allBySelectedLARP($current_larp);

$rows = array();
$rows[] = array("När", "Syn", "Källa", "Bieffekt", "Vem", "Anteckningar");
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
$pdf->new_report($current_larp, $name, $rows);



$pdf->Output();
