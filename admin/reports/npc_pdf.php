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

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $name, $current_larp->Name, false);


$rows = array();
$header = array("Namn", "Spelas av", "Grupp", "Beskrivning", "När");


$npc_groups = NPCGroup::getAllForLARP($current_larp);
foreach ($npc_groups as $npc_group) {
    $npcs=NPC::getAllAssignedByGroup($npc_group, $current_larp);
    foreach ($npcs as $npc) {
        $rows[] = array($npc->Name, $npc->getPerson()->Name, $npc_group->Name, mb_strimwidth($npc->Description, 0, 100, '...',"UTF-8"), $npc->Time);
    }
}
$npcs = NPC::getAllAssignedWithoutGroup($current_larp);
foreach ($npcs as $npc) {
    $rows[] = array($npc->Name, $npc->getPerson()->Name, "", mb_strimwidth($npc->Description, 0, 100, '...',"UTF-8"), $npc->Time);
}

// add a page
$pdf->AddPage();
// print table
$pdf->Table($name, $header, $rows);



// close and output PDF document
$pdf->Output($name.'.pdf', 'I');