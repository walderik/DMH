<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$name = 'In- och utcheckning';

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $current_larp->Name, false);


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$persons = Person::getAllRegistered($current_larp, false);
usort($persons, "cmp");
$rows = array();
$header = array("Namn", "Incheck", "Utcheck", "Kommentar                              ");
foreach ($persons as $person) {
    $props = Prop::getCheckinPropsForPerson($person, $current_larp);
    $letters = Letter::getCheckinLettersForPerson($person, $current_larp);
    $telegrams = Telegram::getCheckinTelegramsForPerson($person, $current_larp);

    $checkin_txt_Arr = array();
    foreach($props as $prop) $checkin_txt_Arr[] = $prop->Name;
    foreach($letters as $letter) $checkin_txt_Arr[] = "Brev från: $letter->Signature till: $letter->Recipient";
    foreach($telegrams as $telegram) $checkin_txt_Arr[] = "Telegram från: $telegram->Sender till: $telegram->Reciever";
    
    
    $comment = "";
    if (!empty($checkin_txt_Arr)) $comment = "Ska ha vid incheck: ". implode(", ", $checkin_txt_Arr);
    $rows[] = array($person->Name, "", "", $comment);
}

// add a page
$pdf->AddPage();
// print table
$pdf->Table("In- och utcheckning deltagare", $header, $rows);

    


$groups = Group::getAllRegistered($current_larp);
$rows = array();
$header = array("Namn", "Incheck", "Utcheck", "Kommentar                              ");
foreach ($groups as $group) {
    $props = Prop::getCheckinPropsForGroup($group, $current_larp);
    $letters = Letter::getCheckinLettersForGroup($group, $current_larp);
    $telegrams = Telegram::getCheckinTelegramsForGroup($group, $current_larp);
    
    $checkin_txt_Arr = array();
    foreach($props as $prop) $checkin_txt_Arr[] = $prop->Name;
    foreach($letters as $letter) $checkin_txt_Arr[] = "Brev från: $letter->Signature till: $letter->Recipient";
    foreach($telegrams as $telegram) $checkin_txt_Arr[] = "Telegram från: $telegram->Sender till: $telegram->Reciever";
    $rows[] = array($group->Name, "", "", $comment);
}
// add a page
$pdf->AddPage();
// print table
$pdf->Table("In- och utcheckning grupper", $header, $rows);
    
// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
