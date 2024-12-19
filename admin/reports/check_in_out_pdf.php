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


$name = 'In- och utcheckning';

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $name, $current_larp->Name, false);



$persons = Person::getAllRegistered($current_larp, false);
$rows = array();

if ($variant == 1) {

    $header = array("Namn", "Incheck", "Utcheck", "Rekvisita", "Kommentar");
    foreach ($persons as $person) {
        $props = Prop::getCheckinPropsForPerson($person, $current_larp);
        
        //TODO få med brev och telegram till grupperingar om personen har en roll som är första roll i grupperingen
        //De finns med i packlistan, så de borde komma med där.
        $letters = Letter::getCheckinLettersForPerson($person, $current_larp);
        $telegrams = Telegram::getCheckinTelegramsForPerson($person, $current_larp);
    
        $checkin_txt_Arr = array();
        foreach($props as $prop) $checkin_txt_Arr[] = $prop->Name;
        foreach($letters as $letter) $checkin_txt_Arr[] = "Brev från: $letter->Signature till: $letter->Recipient";
        foreach($telegrams as $telegram) $checkin_txt_Arr[] = "Telegram från: $telegram->Sender till: $telegram->Reciever";
        
        
        $propstext = "";
        if (!empty($checkin_txt_Arr)) $propstext = implode(", ", $checkin_txt_Arr);
        $rows[] = array($person->Name, "", "", $propstext, "                ");
    }
    
    // add a page
    $pdf->AddPage();
    // print table
    $pdf->Table("In- och utcheckning deltagare", $header, $rows);
    
        
    
    
    $groups = Group::getAllRegistered($current_larp);
    $rows = array();
    $header = array("Namn", "Incheck", "Utcheck", "Rekvisita", "Kommentar");
    foreach ($groups as $group) {
        $props = Prop::getCheckinPropsForGroup($group, $current_larp);
        $letters = Letter::getCheckinLettersForGroup($group, $current_larp);
        $telegrams = Telegram::getCheckinTelegramsForGroup($group, $current_larp);
        
        $checkin_txt_Arr = array();
        foreach($props as $prop) $checkin_txt_Arr[] = $prop->Name;
        foreach($letters as $letter) $checkin_txt_Arr[] = "Brev från: $letter->Signature till: $letter->Recipient";
        foreach($telegrams as $telegram) $checkin_txt_Arr[] = "Telegram från: $telegram->Sender till: $telegram->Reciever";
        $propstext="";
        if (!empty($checkin_txt_Arr)) $propstext = implode(", ", $checkin_txt_Arr);
        $rows[] = array($group->Name, "", "", $propstext, "                 ");
    }
    // add a page
    $pdf->AddPage();
    // print table
    $pdf->Table("In- och utcheckning grupper", $header, $rows);
} elseif ($variant == 2) {
    
    if ($current_larp->ChooseParticipationDates) $header = array("Offnamn", "Ålder", "Frånvarande", "Grupp", "Rollnamn", "Ansvarig vuxen");
    else $header = array("Offnamn", "Ålder", "Grupp", "Rollnamn", "Ansvarig vuxen");

    foreach ($persons as $person) {
        $role = $person->getMainRole($current_larp);
        $group = $role->getGroup();
        $groupName = "";
        if (isset($group)) $groupName = $group->Name;
        
        $guardianName = "";
        $guardian = $person->getGuardian($current_larp);
        if (isset($guardian)) $guardianName = $guardian->Name;
        
        if ($current_larp->ChooseParticipationDates) {
            $registration = $person->getRegistration($current_larp);
            $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $registration->LarpPartNotAttending, $groupName, $role->Name, $guardianName);
        }
        else {
            $rows[] = array($person->Name, $person->getAgeAtLarp($current_larp), $groupName, $role->Name, $guardianName);
        }
    }
    
    // add a page
    $pdf->AddPage();
    // print table
    $pdf->Table("In- och utcheckning deltagare", $header, $rows);
    
}
// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
