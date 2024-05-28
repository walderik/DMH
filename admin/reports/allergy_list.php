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

$variant = 1;
if (isset($_GET['variant'])) $variant=$_GET['variant'];

$name = 'Lista över alla allergier';


$foodChoises = Registration::getFoodVariants($current_larp);
$hasFoodChoices = false;
foreach($foodChoises as $foodChoise) {
    if (!empty($foodChoise[0])) {
        $hasFoodChoices = true;
        break;
    }
}


if ($variant == 1) $pdf = new Report_PDF();
else $pdf = new Report_PDF('L');

$pdf->SetTitle(encode_utf_to_iso($name));
$pdf->SetAuthor(encode_utf_to_iso($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(encode_utf_to_iso($name));

if (NormalAllergyType::isInUse()){
    $allAllergies = NormalAllergyType::all();
    
    foreach($allAllergies as $allergy) {
        $persons = Person::getAllWithSingleAllergy($allergy, $current_larp);
        if (isset($persons) && count($persons) > 0) {
            $rows = array();
            
            if ($variant == 1) {
                $header = array('Namn','Ålder', 'Epost','Övrigt','Vald Mat');
                if ($hasFoodChoices) $header[] = 'Matalternativ';
            } else {
                $header = array('Namn-in','Namn-off','Grupp','Ålder','Vald Mat');
                if ($hasFoodChoices) $header[] = 'Matalternativ';
                if ($current_larp->ChooseParticipationDates) $header[] = 'Frånvarande';
            }
            $rows[] = $header;
    
            foreach($persons as $person) {
                $registration=$person->getRegistration($current_larp);
                if ($variant == 1) {
                    $personrow = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email,
                        $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
                    if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
                } else {
                    $role = $person->getMainRole($current_larp);
                    $group = $role->getGroup();
                    $groupname = "";
                    if (isset($group)) $groupname = $group->Name;
                    $personrow = array($person->Name, $role->Name, $groupname, $person->getAgeAtLarp($current_larp), $registration->getTypeOfFood()->Name);
                    if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
                    if ($current_larp->ChooseParticipationDates) $personrow[]=$registration->LarpPartNotAttending;
                    
                }
                $rows[] = $personrow;
            }
    
            $pdf->new_report($current_larp, "Enbart $allergy->Name", $rows, true);
        }
    }
    
    //Multipla allergier
    $persons = Person::getAllWithMultipleAllergies($current_larp);
    if (!empty($persons) && count($persons) > 0) {
        $rows = array();

        if ($variant == 1) {
            $header = array('Namn','Ålder', 'Epost','Allergier','Övrigt','Vald Mat');
            if ($hasFoodChoices) $header[] = 'Matalternativ';
        } else {
            $header = array('Namn-in','Namn-off','Grupp','Ålder','Vald Mat');
            if ($hasFoodChoices) $header[] = 'Matalternativ';
            $header[] = 'Allergier';
            $header[] = 'Övrigt';
            if ($current_larp->ChooseParticipationDates) $header[] = 'Frånvarande';
        }
        $rows[] = $header;
        
        if ($hasFoodChoices) $rows[] = array('Namn','Ålder', 'Epost','Allergier','Övrigt','Vald Mat', 'Matalternativ');
        else $rows[] = array('Namn','Ålder','Epost','Allergier','Övrigt','Vald Mat');
        
        foreach($persons as $person) {
            $registration=$person->getRegistration($current_larp);
            if ($variant == 1) {
                $personrow = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email, commaStringFromArrayObject($person->getNormalAllergyTypes()),
                    $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
                
                if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
            } else {
                $role = $person->getMainRole($current_larp);
                $group = $role->getGroup();
                $groupname = "";
                if (isset($group)) $groupname = $group->Name;
                $personrow = array($person->Name, $role->Name, $groupname, $person->getAgeAtLarp($current_larp), $registration->getTypeOfFood()->Name);
                if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
                $personrow[] = commaStringFromArrayObject($person->getNormalAllergyTypes());
                $personrow[] = $person->FoodAllergiesOther;
                if ($current_larp->ChooseParticipationDates) $personrow[]=$registration->LarpPartNotAttending;
                
            }
            
            $rows[] = $personrow;
        }
        $pdf->new_report($current_larp, "Multipla vanliga allergier", $rows, true);
    }
}

//Hitta alla som inte har någon vald allergi, men som har en kommentar
$persons = Person::getAllWithoutAllergiesButWithComment($current_larp);
if (!empty($persons) && count($persons) > 0) {
    $rows = array();
    
    if ($variant == 1) {
        $header = array('Namn','Ålder', 'Epost','Allergi','Vald Mat');
        if ($hasFoodChoices) $header[] = 'Matalternativ';
    } else {
        $header = array('Namn-in','Namn-off','Grupp','Ålder','Vald Mat');
        if ($hasFoodChoices) $header[] = 'Matalternativ';
        $header[] = 'Allergi';
        if ($current_larp->ChooseParticipationDates) $header[] = 'Frånvarande';
    }
    $rows[] = $header;
    
    
    foreach($persons as $person) {
        $registration=$person->getRegistration($current_larp);
        
        if ($variant == 1) {
            $personrow = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email, 
                $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
            
            if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
        } else {
            $role = $person->getMainRole($current_larp);
            $group = $role->getGroup();
            $groupname = "";
            if (isset($group)) $groupname = $group->Name;
            $personrow = array($person->Name, $role->Name, $groupname, $person->getAgeAtLarp($current_larp), $registration->getTypeOfFood()->Name);
            if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
            $personrow[] = $person->FoodAllergiesOther;
            if ($current_larp->ChooseParticipationDates) $personrow[]=$registration->LarpPartNotAttending;
            
        }
        
        $rows[] = $personrow;
    }
    $pdf->new_report($current_larp, "Special", $rows, true);
}

$pdf->Output();
