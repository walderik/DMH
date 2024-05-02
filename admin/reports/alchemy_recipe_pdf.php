<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/alchemy_recipe_pdf.php';
include_once '../header.php';


$arrayOfRecipes = Alchemy_Recipe::allApprovedByCampaign($current_larp);
$pdf = new AlchemyRecipe_PDF();
$pdf->SetTitle(encode_utf_to_iso('Recept'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject(encode_utf_to_iso('Recept'));

if (isset($_GET['RecipeId'])) {
    $recipe = Alchemy_Recipe::loadById($_GET['RecipeId']);
    if (isset($recipe)) $pdf->PrintRecipe($recipe, $current_larp->Name);
} else {
    foreach ($arrayOfRecipes as $recipe)  {
        $pdf->PrintRecipe($recipe, $current_larp->Name);
    }
}

$pdf->Output();
