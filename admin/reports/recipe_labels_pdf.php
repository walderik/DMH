<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];


require_once $root . '/pdf/alchemy_recipe_label_pdf.php';
include_once '../header.php';

$pdf = new AlchemyRecipeLabels();
$pdf->SetTitle(encode_utf_to_iso('Alkemietiketter'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject(encode_utf_to_iso('Alkemietiketter'));


if (isset($_POST['alchemistId'])) {
    //print_r($_POST);
    
    $alchemistId = $_POST['alchemistId'];
    $warning = "";
    $alchemistName = "";
    if (!empty($alchemistId)) {
        $alchemist = Alchemy_Alchemist::loadById($alchemistId);
        $alchemistName = $alchemist->getRole()->Name;
        $warning = "";
        if ($alchemist->Level == 1) $warning = "Tillverkad av lärling";
        if ($alchemist->Level == 2) $warning = "Tillverkad av gesäll";
    }

    $recipeIds = $_POST['recipeId'];
    foreach ($recipeIds as $recipeId) {
        $amount = $_POST['Recipe_'.$recipeId];
        if ($amount > 0) {
            
            $recipe = Alchemy_Recipe::loadById($recipeId);
            //echo "PrintRecipeLabelsAmount(".$recipe->Name.", $amount, $alchemistName, $warning, ".$current_larp->Name.")<br>";
            $pdf->PrintRecipeLabelsAmount($recipe, $amount, $alchemistName, $warning, $current_larp);
        }
    }
} else {

    $arrayOfRecipes = Alchemy_Recipe::getAllApproved($current_larp);
    
    foreach ($arrayOfRecipes as $recipe)  {
        $pdf->PrintRecipeLabels($recipe, $current_larp);
    }
}
$pdf->Output();
