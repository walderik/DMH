<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once 'header.php';

require_once $root . '/pdf/alchemy_supplier_sheet_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}




if (isset($_GET['id'])) {
    $supplierId = $_GET['id'];
    $supplier = Alchemy_Supplier::loadById($supplierId);
    if (empty($supplier)) {
        header('Location: index.php'); // Lövjeristen finns inte
        exit;
    }
    $role = $supplier->getRole();
} 

$pdf = new AlchemySupplierSheet_PDF();
$title = (empty($role)) ? 'Alla lövjerister' : ('Lövjerist '.$role->Name) ;

$pdf->SetTitle(encode_utf_to_iso($title));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = $title;
$pdf->SetSubject(encode_utf_to_iso($subject));

if (empty($supplier)) {
    $pdf->all_supplier_sheets($current_larp);
} else {
    $pdf->single_supplier_sheet($supplier, $current_larp);
}

$pdf->Output();


