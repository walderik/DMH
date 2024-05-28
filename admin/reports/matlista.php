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

$name = 'Matlista';


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

$persons = Person::getAllRegistered($current_larp, false);
if (isset($persons) && count($persons) > 0) {
    $rows = array();
    
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
    $rows[] = $header;
    
    
    foreach($persons as $person) {
        $registration=$person->getRegistration($current_larp);
        $housingStr = "";
        $housing = $person->getHouseAtLarp($current_larp);
        if (isset($housing)) $housingStr=$housing->Name;
        $allergyStr = 'Nej';
        if (!empty($person->getNormalAllergyTypes()) || !empty($person->FoodAllergiesOther)) $allergyStr = 'Ja';
        
        if ($variant == 1) {
            $person_row = array($person->Name, $person->getAgeAtLarp($current_larp), $housingStr,
                $registration->getTypeOfFood()->Name);
            if ($hasFoodChoices) $person_row[] = $registration->FoodChoice;
            $person_row[] = $allergyStr;
        } else {
            $role = $person->getMainRole($current_larp);
            $group = $role->getGroup();
            $groupname = "";
            if (isset($group)) $groupname = $group->Name;
            $person_row = array($person->Name, $role->Name, $groupname, $person->getAgeAtLarp($current_larp), $registration->getTypeOfFood()->Name);
            if ($hasFoodChoices) $person_row[] = $registration->FoodChoice;
            $person_row[] = $allergyStr;
            if ($current_larp->ChooseParticipationDates) $person_row[]=$registration->LarpPartNotAttending;
        }
         $rows[] = $person_row;
    }

    $pdf->new_report($current_larp, $name, $rows);
}
$pdf->Output();
