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


$name = "NPC'er";

$pdf = new Report_TCP_PDF('L');

$pdf->init($current_person->Name, $name, $current_larp->Name, false);


$rows = array();
$header = array("Namn", "Spelas av", "Grupp", "Instruktioner", "När");

$npcs = Role::getAllNPCToBePlayed($current_larp);

foreach ($npcs as $npc) {
    $assignment = NPC_assignment::getAssignment($npc, $current_larp);
    $group = $npc ->getGroup();
    if (!empty($group)) {
        $groupName = $group->Name;
    } else $groupName = "";
    
    $person = $assignment->getPerson();
    if (!empty($person)) $personName = $person->Name;
    else $personName = "";
    
    $rows[] = array($npc->Name, $personName, $groupName, mb_strimwidth($assignment->Instructions, 0, 100, '...',"UTF-8"), $assignment->Time);
}

// add a page
$pdf->AddPage();
// print table
$pdf->Table($name, $header, $rows);



// close and output PDF document
ob_end_clean(); 
$pdf->Output($name.'.pdf', 'I');