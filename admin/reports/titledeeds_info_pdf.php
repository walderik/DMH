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

$name = 'Verksamheter';

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));



//Vem bor var?

$titledeed_array = Titledeed::allByCampaign($current_larp, true);
$currency = $current_larp->getCampaign()->Currency;

$rows = array();
if ($all_info) {
    $rows[] = array("Namn", "Plats", "Ägare", "Tillgångar/Behov");
} else {
    $rows[] = array("Namn", "Plats", "Ägare");
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
    
    $owner_str = implode(", ", $owners);
    if ($titledeed->Tradeable == 0) $owner_str = "Kan inte säljas.\n" . $owner_str;
    
    
    if ($all_info) {
        $prod_needs = "";
        $prod_needs = $prod_needs. "Tillgångar: ";
        $prod_needs = $prod_needs.  $titledeed->ProducesString()."\n";
        $prod_needs = $prod_needs.  "Behöver: ";
        $prod_needs = $prod_needs.  $titledeed->RequiresString()."\n";
        $prod_needs = $prod_needs.  "För uppgradering: ";
        $prod_needs = $prod_needs.  $titledeed->RequiresForUpgradeString();
        
        $rows[] = array($titledeed->Name, $titledeed->Location, $owner_str, $prod_needs);
    } else {
        $rows[] = array($titledeed->Name, $titledeed->Location, $owner_str);
    }
}
$pdf->new_report($current_larp, $name, $rows);
    
    
    
$pdf->Output();
