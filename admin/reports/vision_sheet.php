<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once '../header.php';

require_once $root . '/pdf/vision_sheet_pdf.php';


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

$pdf = new VisionSheet_PDF();
$title = (empty($role)) ? 'Alla som får syner' : $role->Name ;

$pdf->SetTitle(utf8_decode($title));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = $title;
$pdf->SetSubject(utf8_decode($subject));

if (empty($role)) {
    $pdf->all_vision_sheets($current_larp);
} else {
    $pdf->single_vision_reciever_sheet($role, $current_larp);
}

$pdf->Output();


