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

if (NormalAllergyType::isInUse()){
    $allAllergies = NormalAllergyType::all();
    
    foreach($allAllergies as $allergy) {
        $persons = Person::getAllWithSingleAllergy($allergy, $current_larp);
        if (isset($persons) && count($persons) > 0) {
            $rows = array();
            if ($hasFoodChoices) $rows[] = array('Namn','Ålder', 'Epost','Övrigt','Vald Mat', 'Matalternativ');
            else $rows[] = array('Namn','Ålder', 'Epost','Övrigt','Vald Mat');
    
            foreach($persons as $person) {
                $registration=$person->getRegistration($current_larp);
                if ($hasFoodChoices) $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email,
                    $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name, $registration->FoodChoice);
                else $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email, 
                                $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
            }
    
            $pdf->new_report($current_larp, "Enbart $allergy->Name", $rows);
        }
    }
    
    //Multipla allergier
    $persons = Person::getAllWithMultipleAllergies($current_larp);
    if (!empty($persons) && count($persons) > 0) {
        $rows = array();
        if ($hasFoodChoices) $rows[] = array('Namn','Ålder', 'Epost','Allergier','Övrigt','Vald Mat', 'Matalternativ');
        else $rows[] = array('Namn','Ålder','Epost','Allergier','Övrigt','Vald Mat');
        
        foreach($persons as $person) {
            $registration=$person->getRegistration($current_larp);
            if ($hasFoodChoices) $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email, commaStringFromArrayObject($person->getNormalAllergyTypes()),
                $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name, $registration->FoodChoice);
            else $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email, commaStringFromArrayObject($person->getNormalAllergyTypes()),
                            $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
        }
        $pdf->new_report($current_larp, "Multipla vanliga allergier", $rows);
    }
}

//Hitta alla som inte har någon vald allergi, men som har en kommentar
$persons = Person::getAllWithoutAllergiesButWithComment($current_larp);
if (!empty($persons) && count($persons) > 0) {
    $rows = array();
    if ($hasFoodChoices) $rows[] = array('Namn','Ålder','Epost','Övrigt','Vald Mat','Matalternativ');
    else $rows[] = array('Namn','Ålder','Epost','Övrigt','Vald Mat');
    
    foreach($persons as $person) {
        $registration=$person->getRegistration($current_larp);
        
        if ($hasFoodChoices) $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email,
            $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name, $registration->FoodChoice);
        else $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email, 
                        $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
    }
    $pdf->new_report($current_larp, "Special", $rows);
}

$pdf->Output();
