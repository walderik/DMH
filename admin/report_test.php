<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';
require_once $root . '/pdf/report_pdf.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$name = 'Rapporten';
$persons = Person::getAllToApprove($current_larp);

$rows = array();
foreach($persons as $person) {
    $rows[] = array($person->Name, $person->Email, $person->PhoneNumber, $person->SocialSecurityNumber);
}


$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));

$pdf->new_report($current_larp, $name, $rows);
$pdf->new_report($current_larp, "Gluten", $rows);
$pdf->Output();