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

$name = 'Alla intriger';

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));




$intrigue_array = Intrigue::allByLARP($current_larp);

foreach ($intrigue_array as $intrigue) {
    if (!$intrigue->isActive()) continue;
    $rows = array();
    $rows[] = array("Ansvarig", $intrigue->getResponsibleUser()->Name);
    $rows[] = array("Anteckningar", $intrigue->Notes);
    $groupActors = $intrigue->getAllGroupActors();
    foreach($groupActors as $groupActor) {
        if (empty($groupActor->IntrigueText)) continue;
        $rows[] = array($groupActor->getGroup()->Name, $groupActor->IntrigueText);
    }
    $roleActors = $intrigue->getAllRoleActors();
    foreach($roleActors as $roleActor) {
        if (empty($roleActor->IntrigueText)) continue;
        $rows[] = array($roleActor->getRole()->Name, $roleActor->IntrigueText);
    }
    
    
    $pdf->new_report($current_larp, "$intrigue->Number. $intrigue->Name", $rows);
}
    
    
    
$pdf->Output();
