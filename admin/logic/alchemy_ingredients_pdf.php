<?php 
global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];


require_once $root . '/pdf/alchemy_ingredient_pdf.php';
include_once '../header.php';

$type = ALCHEMY_INGREDIENT_PDF::Handwriting;


if ($_SERVER["REQUEST_METHOD"] == "GET") {   
    $type = $_GET['type'];
    if (isset($_GET['id'])) $ingredient = Alchemy_Ingredient::loadById($_GET['id']);
}


$pdf = new ALCHEMY_INGREDIENT_PDF();

if (isset($_GET['extra_kort'])) {    
    $pdf->SetTitle(encode_utf_to_iso('Extra ingredienskort'));
    $pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
    $pdf->SetCreator('Omnes Mundi');
    $pdf->SetSubject(encode_utf_to_iso('Extra ingredienskort'));
    $pdf->extra_resources($type, $current_larp);    
} elseif (empty($ingredient)) {
    $arrayOfAlcheySuppliers = Alchemy_Supplier::allByCampaign($current_larp, false);
    $pdf->SetTitle(encode_utf_to_iso('Alla ingredienser för alla lövjerister'));
    $pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
    $pdf->SetCreator('Omnes Mundi');
    $pdf->SetSubject(encode_utf_to_iso('Alla ingredienser för alla lövjerister'));
    $pdf->all_ingredients_for_all_suppliers($arrayOfAlcheySuppliers, $type, $current_larp);
} else {
    $pdf->SetTitle(encode_utf_to_iso($ingredient->Name));
    $pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
    $pdf->SetCreator('Omnes Mundi');
    $pdf->SetSubject(encode_utf_to_iso($ingredient->Name));
    $pdf->one_resource_on_one_page($ingredient, $type, $current_larp);
}



$pdf->Output();


