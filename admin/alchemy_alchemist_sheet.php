<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

include_once 'header.php';

require_once $root . '/pdf/alchemy_alchemist_sheet_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}




if (isset($_GET['id'])) {
    $alchemistId = $_GET['id'];
    $alchemist = Alchemy_Alchemist::loadById($alchemistId);
    if (empty($alchemist)) {
        header('Location: index.php'); // Alkemisten finns inte
        exit;
    }
    $role = $alchemist->getRole();
} 

$pdf = new AlchemyAlchemistSheet_PDF();
$title = (empty($role)) ? 'Alla alkemister' : ('Alkemist '.$role->Name) ;

$pdf->SetTitle(encode_utf_to_iso($title));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = $title;
$pdf->SetSubject(encode_utf_to_iso($subject));

if (empty($alchemist)) {
    $pdf->all_alchemist_sheets($current_larp);
} else {
    $pdf->single_alchemist_sheet($alchemist, $current_larp);
}

$pdf->Output();


