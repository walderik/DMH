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

$one_intrigue_id = null;

if (isset($_GET['Id'])) $one_intrigue_id = $_GET['Id'];

$name = 'Alla intriger';

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));

$intrigue_array = Intrigue::allByLARP($current_larp);

foreach ($intrigue_array as $intrigue) {
    if (!$intrigue->isActive()) continue;
    if (isset($one_intrigue_id) && $one_intrigue_id != $intrigue->Id) continue;
    $rows = array();
    $rows[] = array("Ansvarig", $intrigue->getResponsibleUser()->Name);

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

    $pdf->new_report($current_larp, "$intrigue->Number. $intrigue->Name", $rows);
}
    
    
    
$pdf->Output();
