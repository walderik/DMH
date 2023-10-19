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

$all_info = false;
if (isset($_GET["all_info"])) $all_info = true;

$name = 'Priser';

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));



//Vem bor var?

$resources = Resource::allNormalByCampaign($current_larp);
$currency = $current_larp->getCampaign()->Currency;

$rows = array();
$rows[] = array("Namn", "Pris", "Kommentar                                                                                                                                   ");
foreach ($resources as $resource) {
    $rows[] = array($resource->UnitSingular, $resource->Price." $currency", "");
}
$pdf->new_report($current_larp, $name, $rows);
    
    
    
$pdf->Output();
