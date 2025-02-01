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

$all_info = false;
if (isset($_GET["all_info"])) $all_info = true;

$name = 'Verksamheter';

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $name, $current_larp->Name, false);



//Vem bor var?

$titledeed_array = Titledeed::allByCampaign($current_larp, false);
$currency = $current_larp->getCampaign()->Currency;

$rows = array();
if ($all_info) {
    $header = array("Namn", "Plats", "Ägare", "Tillgångar/Behov");
} else {
    $header = array("Namn", "Plats", "Ägare");
}
foreach ($titledeed_array as $titledeed) {
    $owners = array();
    $owner_groups = $titledeed->getGroupOwners();
    foreach ($owner_groups as $owner_group) {
        $owners[] = $owner_group->Name;
    }
    $owner_roles = $titledeed->getRoleOwners();
    foreach ($owner_roles as $owner_role) {
        $owners[] = $owner_role->Name;
    }
    
    if ($titledeed->isGeneric()) {
        $tradeStr = "";
        if ($titledeed->Tradeable == 0) $tradeStr = "\nKan inte säljas.\n";
    } else {
        $owner_str = implode(", ", $owners);
        if ($titledeed->Tradeable == 0) $owner_str = "Kan inte säljas.\n" . $owner_str;
    }

    
    if ($all_info) {
        $prod_needs = "";
        $prod_needs = $prod_needs. "Tillgångar: ";
        $prod_needs = $prod_needs.  $titledeed->ProducesString()."\n";
        $prod_needs = $prod_needs.  "Behöver: ";
        $prod_needs = $prod_needs.  $titledeed->RequiresString()."\n";
        $prod_needs = $prod_needs.  "För uppgradering: ";
        $prod_needs = $prod_needs.  $titledeed->RequiresForUpgradeString();
        if ($titledeed->isGeneric()) {
            foreach ($owners as $owner) $rows[] = array($titledeed->Name, $titledeed->Location, $owner.$tradeStr, $prod_needs);
        }
        else {
            $rows[] = array($titledeed->Name, $titledeed->Location, $owner_str, $prod_needs);
        }
    } else {
        if ($titledeed->isGeneric()) {
            foreach ($owners as $owner) $rows[] = array($titledeed->Name, $titledeed->Location, $owner.$tradeStr);
        }
        else {
            $rows[] = array($titledeed->Name, $titledeed->Location, $owner_str);
        }
    }
}
// add a page
$pdf->AddPage();
// print table
$pdf->Table($name, $header, $rows);



// close and output PDF document
$pdf->Output($name.'.pdf', 'I');

