<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/alchemy_recipe_label_pdf.php';
include_once '../header.php';

$arrayOfRecipes = Alchemy_Recipe::getAllApproved($current_larp);
$pdf = new AlchemyRecipeLabels();
$pdf->SetTitle(encode_utf_to_iso('Alkemietiketter'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject(encode_utf_to_iso('Alkemietiketter'));

//$pdf->PrintRecipeLabels($arrayOfRecipes[0]);


foreach ($arrayOfRecipes as $recipe)  {
    $pdf->PrintRecipeLabels($recipe, $current_larp);
}

$pdf->Output();
