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

$name = 'Boende';




function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $current_larp->Name, false);




//Vem bor var?
$persons = Person::getAllRegistered($current_larp, false);
usort($persons, "cmp");
$rows = array();
$header = array("Namn", "Boende", "Typ");
foreach ($persons as $person) {
    $house = House::getHouseAtLarp($person, $current_larp);
    if (empty($house)) $rows[] = array($person->Name, "Inget tilldelat", "");
    else {
        $type = "hus";
        if ($house->IsCamp()) $type = "lägerplats";
        $rows[] = array($person->Name, $house->Name, $type);
    }
}

// add a page
$pdf->AddPage();

// print table
$pdf->Table("Vem bor var?", $header, $rows);


//Vilka bor i vilket hus?
$houses = House::all();
$rows = array();
$header = array("Boende", "Typ", "Personer");
foreach ($houses as $house) {
    $type = "hus";
    if ($house->IsCamp()) $type = "lägerplats";
    $persons = Person::personsAssignedToHouse($house, $current_larp);
   if (empty($persons)) continue;
   $names = array();
   foreach($persons as $person) {
       $names[] = $person->Name;
   }
   $rows[] = array($house->Name, $type, implode(", ", $names));
}

// add a page
$pdf->AddPage();

// print table
$pdf->Table("Vilka bor i husen/på lägerplatserna?", $header, $rows);

// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
