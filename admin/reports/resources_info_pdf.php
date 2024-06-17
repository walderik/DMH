<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$all_info = false;
if (isset($_GET["all_info"])) $all_info = true;

$name = 'Priser';

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $current_larp->Name, false);



//Vem bor var?

$resources = Resource::allNormalByCampaign($current_larp);
$currency = $current_larp->getCampaign()->Currency;

$rows = array();
$header = array("Namn", "Pris", "Kommentar                                                                                                                                   ");
foreach ($resources as $resource) {
    $rows[] = array($resource->Name, $resource->Price." $currency", "");
}

// add a page
$pdf->AddPage();
// print table
$pdf->Table($name, $header, $rows);



// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
