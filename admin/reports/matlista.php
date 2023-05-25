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

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));

$allAllergies = NormalAllergyType::all();

foreach($allAllergies as $allergy) {
    $persons = Person::getAllWithSingleAllergy($allergy, $current_larp);
    if (isset($persons) && count($persons) > 0) {
        $rows = array();
        $rows[] = array('Namn','Epost','Telefon','Övrigt','Vald Mat');

        foreach($persons as $person) {
            $registration=$person->getRegistration($current_larp);
            $rows[] = array($person->Name, $person->Email, $person->PhoneNumber,
                            $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
        }

        $pdf->new_report($current_larp, "Enbart $allergy->Name", $rows);
    }
}

//Multipla allergier
$persons = Person::getAllWithMultipleAllergies($current_larp);
if (!empty($persons) && count($persons) > 0) {
    $rows = array();
    $rows[] = array('Namn','Epost','Telefon','Allergier','Övrigt','Vald Mat');
    foreach($persons as $person) {
        $registration=$person->getRegistration($current_larp);
        $rows[] = array($person->Name, $person->Email, $person->PhoneNumber, commaStringFromArrayObject($person->getNormalAllergyTypes()),
                        $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
    }
    $pdf->new_report($current_larp, "Multipla vanliga allergier", $rows);
}

//Hitta alla som inte har någon vald allergi, men som har en kommentar
$persons = Person::getAllWithoutAllergiesButWithComment($current_larp);
if (!empty($persons) && count($persons) > 0) {
    $rows = array();
    $rows[] = array('Namn','Epost','Telefon','Övrigt','Vald Mat');
    foreach($persons as $person) {
        $registration=$person->getRegistration($current_larp);
        $rows[] = array($person->Name, $person->Email, $person->PhoneNumber, 
                        $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
    }
    $pdf->new_report($current_larp, "Special", $rows);
}
$pdf->Output();
