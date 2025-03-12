<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

include_once 'header.php';

require_once $root . '/pdf/magic_magician_sheet_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}




if (isset($_GET['id'])) {
    $magicianId = $_GET['id'];
    $magician = Magic_Magician::loadById($magicianId);
    if (empty($magician)) {
        header('Location: index.php'); // Magikern finns inte
        exit;
    }
    $role = $magician->getRole();
} 

$pdf = new MagicMagicianSheet_PDF();
$title = (empty($role)) ? 'Alla magiker' : ('Magiker '.$role->Name) ;

$pdf->SetTitle(encode_utf_to_iso($title));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = (empty($role)) ? 'Alla magiker' : 'Magiker '.$role->Name;
$pdf->SetSubject(encode_utf_to_iso($subject));

if (empty($magician)) {
    $pdf->all_magician_sheets($current_larp);
} else {
    $pdf->single_magician_sheet($magician, $current_larp);
}

$pdf->Output();


