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

$name = 'Matlista';


$foodChoises = Registration::getFoodVariants($current_larp);
$hasFoodChoices = false;
foreach($foodChoises as $foodChoise) {
    if (!empty($foodChoise[0])) {
        $hasFoodChoices = true;
        break;
    }
}


$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));

$persons = Person::getAllRegistered($current_larp, false);
if (isset($persons) && count($persons) > 0) {
    $rows = array();
    if ($hasFoodChoices) $rows[] = array('Namn','Ålder', 'Boende','Vald Mat', 'Matalternativ', 'Allergi');
    else $rows[] = array('Namn','Ålder', 'Boende','Vald Mat','Allergi');

    
    foreach($persons as $person) {
        $registration=$person->getRegistration($current_larp);
        $housingStr = "";
        $housing = $person->getHouseAtLarp($current_larp);
        if (isset($housing)) $housingStr=$housing->Name;
        $allergyStr = 'Nej';
        if (!empty($person->getNormalAllergyTypes()) || !empty($person->FoodAllergiesOther)) $allergyStr = 'Ja';
        if ($hasFoodChoices) $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $housingStr,
            $registration->getTypeOfFood()->Name, $registration->FoodChoice, $allergyStr);
        else $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $housingStr, 
            $registration->getTypeOfFood()->Name, $allergyStr);
    }

    $pdf->new_report($current_larp, $name, $rows);
}
$pdf->Output();
