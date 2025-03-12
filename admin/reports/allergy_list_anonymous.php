<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$variant = 1;
if (isset($_GET['variant'])) $variant=$_GET['variant'];

$name = 'Lista över alla allergier';

$rows = array();
$header = array("Allergi");

if (NormalAllergyType::isInUse()){
    $allAllergies = NormalAllergyType::allOnLarp($current_larp);
    
    foreach($allAllergies as $allergy) {
        $rows[] = array($allergy->Name);
    }
}
    
 
//Hitta alla som inte har någon vald allergi, men som har en kommentar
$persons = Person::getAllWithAllergyComment($current_larp);
if (!empty($persons) && count($persons) > 0) {
    
    foreach($persons as $person) {
        $rows[] = array($person->FoodAllergiesOther);
    }
}
// create new PDF document
$pdf = new Report_TCP_PDF();
$pdf->init($current_person->Name, $name, $current_larp->Name, false);

// add a page
$pdf->AddPage('L');

// print table
$pdf->Table($name, $header, $rows);

// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
    
