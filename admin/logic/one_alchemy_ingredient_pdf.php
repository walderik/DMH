<?php 
global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];


require_once $root . '/pdf/alchemy_ingredient_pdf.php';
include_once '../header.php';

$type = ALCHEMY_INGREDIENT_PDF::Handwriting;
$id = 1;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $type = $_GET['type'];
    $id = $_GET['id'];
}

$ingredient = Alchemy_Ingredient::loadById($id);
if (empty($ingredient)) {
    header('Location: admin/alchemy_ingredient_admin.php'); // Ingrediensen finns inte
    exit;
}

$pdf = new ALCHEMY_INGREDIENT_PDF();
$pdf->SetTitle(encode_utf_to_iso($ingredient->Name));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->SetSubject(encode_utf_to_iso($ingredient->Name));
$pdf->one_resource_on_one_page($ingredient, $type, $current_larp);

$pdf->Output();


