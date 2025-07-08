<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';
require_once $root . '/pdf/magic_magician_sheet_pdf.php';



if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $roleId = $_GET['id'];
    $role = Role::loadById($roleId);
}

if (empty($role)) {
    header('Location: index.php'); // Karaktären finns inte
    exit;
}

# Kolla behörigheten
$person = $role->getPerson();
if (is_null($person) || $person->Id != $current_person->Id) {
    header("Location: index.php"); # Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // Karaktären är inte anmäld
    exit;
}


$pdf = new MagicMagicianSheet_PDF();
$title = 'Magiker '.$role->Name;

$pdf->SetTitle(encode_utf_to_iso($title));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = $title;
$pdf->SetSubject(encode_utf_to_iso($subject));

$magician=Magic_Magician::getForRole($role);
$pdf->single_magician_sheet($magician, $current_larp);


$pdf->Output();

