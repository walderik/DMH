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
$header = array("Grupp","Karaktär", "Deltagare", "");

foreach ($roles as $role) {
    $person = $role->getPerson();
    if (empty($person)) $name = "NPC";
    else $name = $person->Name;
    $group = $role->getGroup();
    if (!empty($group)) $groupName = $group->Name;
    else $groupName = "";
    $rows[] = array($groupName, $role->Name, $name,"                                           ");
}

setlocale(LC_COLLATE, 'sv_SE');
usort($rows, function ($a, $b) {
    if($a[0] > $b[0]) return 1;
    if($a[0] < $b[0]) return -1;
    
    if($a[1] > $b[1]) return 1;
    if($a[1] < $b[1]) return -1;

    if($a[2] > $b[2]) return 1;
    if($a[2] < $b[2]) return -1;
    
    return 0;
});
// add a page
$pdf->AddPage('L');
// print table
$pdf->Table($listname, $header, $rows);



// close and output PDF document
ob_end_clean(); 
$pdf->Output($listname.'.pdf', 'I');
