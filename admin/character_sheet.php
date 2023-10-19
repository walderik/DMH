<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once 'header.php';

require_once $root . '/pdf/character_sheet_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$bara_intrig = false;
$role = null;

if (isset($_GET['id'])) {
    $roleId = $_GET['id'];
    $role = Role::loadById($roleId);
    if (empty($role)) {
        header('Location: index.php'); // Karaktären finns inte
        exit;
    } elseif (!$role->isRegistered($current_larp)) {
        header('Location: index.php'); // Karaktären är inte anmäld
        exit;
    }
} elseif (isset($_GET['bara_intrig'])) {
    $bara_intrig = true;
}


$pdf = new CharacterSheet_PDF();
$title = (empty($role)) ? 'Alla Karaktärer' : ('Karaktärsblad '.$role->Name) ;

$pdf->SetTitle(utf8_decode($title));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = (empty($role)) ? 'ALLA' : $role->Name;
$pdf->SetSubject(utf8_decode($subject));
$all_info = (isset($_GET['all_info'])) ? true : false;

if (empty($role)) {
    $pdf->all_character_sheets($current_larp, $bara_intrig, $all_info);
} else {
    $all_info = (isset($_GET['all_info'])) ? true : false;
    $pdf->new_character_sheet($role, $current_larp, $all_info, false);
}

$pdf->Output();


