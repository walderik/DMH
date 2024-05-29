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

$name = 'Ansvarig vuxen för barn';

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $current_larp->Name, false);


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$persons = Person::getAllWithGuardians($current_larp, false);
usort($persons, "cmp");
$rows = array();
$header = array("In-namn", "Off-namn", "Vuxens in-namn", "Off-namn", "Telefon", "Boende");
foreach ($persons as $person) {
    $guardian = $person->getGuardian($current_larp);
    $child_roles = $person->getRoles($current_larp);
    $child_main_role_name = ""; 
    foreach($child_roles as $child_role) {
        if ($child_role->isMain($current_larp)) $child_main_role_name = $child_role->Name;
    }
    $guardian_roles = $guardian->getRolesAtLarp($current_larp);
    $guardian_role_names_arr = array();
    foreach($guardian_roles as $guardian_role) $guardian_role_names_arr[] = $guardian_role->Name;
    $house = $guardian->getHouseAtLarp($current_larp);
    $houseText = "";
    if (isset($house)) $houseText = $house->Name;
    $rows[] = array($child_main_role_name, $person->Name, implode(", ", $guardian_role_names_arr), $guardian->Name, $guardian->PhoneNumber, $houseText);

}

// add a page
$pdf->AddPage('L');
// print table
$pdf->Table($name, $header, $rows);

    
    
// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
