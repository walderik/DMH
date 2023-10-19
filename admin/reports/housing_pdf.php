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

$name = 'Boende';

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

//Vem bor var?
$persons = Person::getAllRegistered($current_larp, false);
usort($persons, "cmp");
$rows = array();
$rows[] = array("Namn", "Boende", "Typ");
foreach ($persons as $person) {
    $house = House::getHouseAtLarp($person, $current_larp);
    if (empty($house)) $rows[] = array($person->Name, "Inget tilldelat", "");
    else {
        $type = "hus";
        if ($house->IsCamp()) $type = "lägerplats";
        $rows[] = array($person->Name, $house->Name, $type);
    }
}
$pdf->new_report($current_larp, "Vem bor var?", $rows);


//Vilka bor i vilket hus?
$houses = House::all();
$rows = array();
$rows[] = array("Boende", "Typ", "Personer");
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
$pdf->new_report($current_larp, "Vilka bor i husen/på lägerplatserna?", $rows);


$pdf->Output();
