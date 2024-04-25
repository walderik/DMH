<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once '../header.php';

require_once $root . '/pdf/vision_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}




if (isset($_GET['id'])) {
    $roleId = $_GET['id'];
    $role = Role::loadById($roleId);
    if (empty($role)) {
        header('Location: index.php'); // Karaktären finns inte
        exit;
    }
} 

$pdf = new Vision_PDF();
$title = (empty($role)) ? 'Alla visioner' : ('Syner för '.$role->Name) ;

$pdf->SetTitle(encode_utf_to_iso($title));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = $title;
$pdf->SetSubject(encode_utf_to_iso($subject));

if (empty($role)) {
    $pdf->all_vision_sheets($current_larp);
} else {
    $pdf->vision_sheets_for_role($role, $current_larp);
}

$pdf->Output();


