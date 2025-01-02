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


if ($variant == 1) $pdf = new Report_TCP_PDF();
else $pdf = new Report_TCP_PDF('L');

$pdf->init($current_person->Name, $name, $current_larp->Name, true);

if (NormalAllergyType::isInUse()){
    $allAllergies = NormalAllergyType::all();
    
    foreach($allAllergies as $allergy) {
        $persons = Person::getAllWithSingleAllergyWithoutComment($allergy, $current_larp);
        if (isset($persons) && count($persons) > 0) {
            $rows = array();
            
            if ($variant == 1) {
                $header = array('Namn','Ålder', 'Epost','Vald Mat');
                if ($hasFoodChoices) $header[] = 'Matalternativ';
            } else {
                $header = array('Namn-in','Namn-off','Grupp','Ålder','Vald Mat');
                if ($hasFoodChoices) $header[] = 'Matalternativ';
                if ($current_larp->ChooseParticipationDates) $header[] = 'Frånvarande';
            }
            
    
            foreach($persons as $person) {
                $registration=$person->getRegistration($current_larp);
                if ($variant == 1) {
                    $personrow = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email,
                        $registration->getTypeOfFood()->Name);
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
    
            // add a page
            $pdf->AddPage('s');
            // print table
            $pdf->Table("Enbart $allergy->Name", $header, $rows);
            //$pdf->new_report($current_larp, "Enbart $allergy->Name", $rows, true);
        }
    }
    
    //Multipla allergier, utan kommentar
    $persons = Person::getAllWithMultipleAllergiesWithoutComment($current_larp);
    if (!empty($persons) && count($persons) > 0) {
        $rows = array();

        if ($variant == 1) {
            $header = array('Namn','Ålder', 'Epost','Allergier','Vald Mat');
            if ($hasFoodChoices) $header[] = 'Matalternativ';
        } else {
            $header = array('Namn-in','Namn-off','Grupp','Ålder','Vald Mat');
            if ($hasFoodChoices) $header[] = 'Matalternativ';
            $header[] = 'Allergier';
            if ($current_larp->ChooseParticipationDates) $header[] = 'Frånvarande';
        }

        
        
        foreach($persons as $person) {
            $registration=$person->getRegistration($current_larp);
            if ($variant == 1) {
                $personrow = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email, commaStringFromArrayObject($person->getNormalAllergyTypes()),
                    $registration->getTypeOfFood()->Name);
                
                if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
            } else {
                $role = $person->getMainRole($current_larp);
                $group = $role->getGroup();
                $groupname = "";
                if (isset($group)) $groupname = $group->Name;
                $personrow = array($person->Name, $role->Name, $groupname, $person->getAgeAtLarp($current_larp), $registration->getTypeOfFood()->Name);
                if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
                $personrow[] = commaStringFromArrayObject($person->getNormalAllergyTypes());
                if ($current_larp->ChooseParticipationDates) $personrow[]=$registration->LarpPartNotAttending;
                
            }
            
            $rows[] = $personrow;
        }

        // add a page
        $pdf->AddPage('s');
        // print table
        $pdf->Table("Multipla vanliga allergier", $header, $rows);
        //$pdf->new_report($current_larp, "Multipla vanliga allergier", $rows, true);
    }
}

//Hitta alla som inte har har en kommentar
$persons = Person::getAllWithAllergyComment($current_larp);
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
    
    
    
    foreach($persons as $person) {
        $registration=$person->getRegistration($current_larp);
        $allergies = commaStringFromArrayObject($person->getNormalAllergyTypes());
        if (!empty($allergies)) $allergies = implode(", ", array($allergies, $person->FoodAllergiesOther));
        else $allergies = $person->FoodAllergiesOther;
        if ($variant == 1) {
            $personrow = array($person->Name, $person->getAgeAtLarp($current_larp), $person->Email, 
                $allergies, $registration->getTypeOfFood()->Name);
            
            if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
        } else {
            $role = $person->getMainRole($current_larp);
            $group = $role->getGroup();
            $groupname = "";
            if (isset($group)) $groupname = $group->Name;
            $personrow = array($person->Name, $role->Name, $groupname, $person->getAgeAtLarp($current_larp), $registration->getTypeOfFood()->Name);
            if ($hasFoodChoices) $personrow[] = $registration->FoodChoice;
            $personrow[] = $allergies;
            if ($current_larp->ChooseParticipationDates) $personrow[]=$registration->LarpPartNotAttending;
            
        }
        
        $rows[] = $personrow;
    }
    // add a page
    $pdf->AddPage('L');
    // print table
    $pdf->Table("Special", $header, $rows);
    
    //$pdf->new_report($current_larp, "Special", $rows, true);
}

$pdf->Output();
