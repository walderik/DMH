<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/alchemy_recipe_label_pdf.php';
include_once '../header.php';

$arrayOfRecipes = Alchemy_Recipe::getAllApproved($current_larp);
$pdf = new AlchemyRecipeLabels();
$pdf->SetTitle(utf8_decode('Alkemietiketter'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator(utf8_decode('Omnes Mundi'));
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject(utf8_decode('Alkemietiketter'));

//$pdf->PrintRecipeLabels($arrayOfRecipes[0]);


foreach ($arrayOfRecipes as $recipe)  {
    $pdf->PrintRecipeLabels($recipe, $current_larp);
}

$pdf->Output();
