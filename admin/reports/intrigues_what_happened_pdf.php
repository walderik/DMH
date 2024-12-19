<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$one_intrigue_id = null;

if (isset($_GET['Id'])) $one_intrigue_id = $_GET['Id'];

$name = 'Alla intriger';

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $name, $current_larp->Name, false);

$intrigue_array = Intrigue::allByLARP($current_larp);

foreach ($intrigue_array as $intrigue) {
    if (!$intrigue->isActive()) continue;
    if (isset($one_intrigue_id) && $one_intrigue_id != $intrigue->Id) continue;
    $rows = array();
    $header = array("Ansvarig", $intrigue->getResponsiblePerson()->Name);

    $groupActors = $intrigue->getAllGroupActors();
    foreach($groupActors as $groupActor) {
        if (empty($groupActor->IntrigueText)) continue;
        if (empty($groupActor->WhatHappened)) continue;
        $rows[] = array($groupActor->getGroup()->Name, $groupActor->WhatHappened);
    }
    $roleActors = $intrigue->getAllRoleActors();
    foreach($roleActors as $roleActor) {
        if (empty($roleActor->IntrigueText)) continue;
        if (empty($roleActor->WhatHappened)) continue;
        $rows[] = array($roleActor->getRole()->Name, $roleActor->WhatHappened);
    }

    // add a page
    $pdf->AddPage();
    // print table
    $pdf->Table("$intrigue->Number. $intrigue->Name", $header, $rows);

}
    
// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
