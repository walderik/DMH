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




$foodChoises = Registration::getFoodVariants($current_larp);
$hasFoodChoices = false;
foreach($foodChoises as $foodChoise) {
    if (!empty($foodChoise[0])) {
        $hasFoodChoices = true;
        break;
    }
}



$persons = Person::getAllRegistered($current_larp, false);
if (isset($persons) && count($persons) > 0) {
    
    if ($variant == 1) {
        $header = array('Namn','Ålder','Boende','Vald Mat');
        if ($hasFoodChoices) $header[] = 'Matalternativ';
        $header[] = 'Allergi';
    } else {
        $header = array('Namn-in','Namn-off','Grupp','Ålder','Vald Mat');
        if ($hasFoodChoices) $header[] = 'Matalternativ';
        $header[] = 'Allergi';
        if ($current_larp->ChooseParticipationDates) $header[] = 'Frånvarande';
    }
    
    
    $rows = array();
    
    foreach($persons as $person) {
        $registration=$person->getRegistration($current_larp);
        $housingStr = "";
        $housing = $person->getHouseAtLarp($current_larp);
        if (isset($housing)) $housingStr=$housing->Name;
        
        if ($variant == 1) {
            $name = 'Matlista';
            $orientation = 'S';
            $allergyStr = 'Nej';
            if (!empty($person->getNormalAllergyTypes()) || !empty($person->FoodAllergiesOther)) $allergyStr = 'Ja';
            $person_row = array($person->Name, $person->getAgeAtLarp($current_larp), $housingStr,
                $registration->getTypeOfFood()->Name);
            if ($hasFoodChoices) $person_row[] = $registration->FoodChoice;
            $person_row[] = $allergyStr;
        } else {
            $name = 'Mat- och allergilista';
            $orientation = 'L';
            $role = $person->getMainRole($current_larp);
            $group = $role->getGroup();
            $groupname = "";
            if (isset($group)) $groupname = $group->Name;
            $person_row = array($person->Name, $role->Name, $groupname, $person->getAgeAtLarp($current_larp), $registration->getTypeOfFood()->Name);
            if ($hasFoodChoices) $person_row[] = $registration->FoodChoice;
            $normalAllergies = commaStringFromArrayObject($person->getNormalAllergyTypes());
            $allergyArr = array();
            if (!empty($normalAllergies)) $allergyArr[] = $normalAllergies;
            if (!empty($person->FoodAllergiesOther)) $allergyArr[] = $person->FoodAllergiesOther;
            
            $person_row[] = implode("\n",$allergyArr);
            if ($current_larp->ChooseParticipationDates) $person_row[]=$registration->LarpPartNotAttending;
        }
        $rows[] = $person_row;
    }
    
    


    if ($variant == 1) $isSensitive = false;
    else $isSensitive = true;



// create new PDF document
$pdf = new Report_TCP_PDF();
$pdf->init($current_person->Name, $name, $current_larp->Name, $isSensitive);

// add a page
$pdf->AddPage($orientation);

// print table
$pdf->Table($name, $header, $rows);

// close and output PDF document
ob_end_clean(); 
$pdf->Output($name.'.pdf', 'I');



}
