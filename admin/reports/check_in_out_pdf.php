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

$name = 'In- och utcheckning';

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

//Vem bor var?
$persons = Person::getAllRegistered($current_larp, false);
usort($persons, "cmp");
$rows = array();
$rows[] = array("Namn", "Incheck", "Utcheck", "Kommentar");
foreach ($persons as $person) {
    $rows[] = array($person->Name, "", "", "                              ");
}
$pdf->new_report($current_larp, "In- och utcheckning", $rows);
    
    
    
$pdf->Output();
