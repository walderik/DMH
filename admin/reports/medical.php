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

$name = 'Hälsoinformation till sjukvårdare/trygghetsvärdar';



function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$persons = Person::getAllRegistered($current_larp, false);
usort($persons, "cmp");
$rows = array();
$header = array("Namn", "Personnummer", "Kommentar                              ", "Anhörig", "Boende");
foreach ($persons as $person) {
    if (!empty($person->HealthComment) && (trim($person->HealthComment) != "Inget")) {
        $house = $person->getHouseAtLarp($current_larp);
        $houseText = "";
        if (isset($house)) $houseText = $house->Name;
        $rows[] = array($person->Name, $person->SocialSecurityNumber, $person->HealthComment, $person->EmergencyContact, $houseText);
    }
}

// create new PDF document
$pdf = new Report_TCP_PDF();
$pdf->init($current_person->Name, $name, $current_larp->Name, true);

// add a page
$pdf->AddPage('L');

// print table
$pdf->Table($name, $header, $rows);

// close and output PDF document
$pdf->Output($name.'.pdf', 'I');


