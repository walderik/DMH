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


$name = "NPC'er";

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));


$rows = array();
$rows[] = array("Namn", "Spelas av", "Grupp", "Beskrivning", "När");


$npc_groups = NPCGroup::getAllForLARP($current_larp);
foreach ($npc_groups as $npc_group) {
    $npcs=NPC::getAllAssignedByGroup($npc_group, $current_larp);
    foreach ($npcs as $npc) {
        $rows[] = array($npc->Name, $npc->getPerson()->Name, $npc_group->Name, mb_strimwidth($npc->Description, 0, 100, '...'), $npc->Time);
    }
}
$npcs = NPC::getAllAssignedWithoutGroup($current_larp);
foreach ($npcs as $npc) {
    $rows[] = array($npc->Name, $npc->getPerson()->Name, "", mb_strimwidth($npc->Description, 0, 100, '...'), $npc->Time);
}

$pdf->new_report($current_larp, $name, $rows);
    
    
    
$pdf->Output();
