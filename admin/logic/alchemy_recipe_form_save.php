<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $recipe = Alchemy_Recipe::newFromArray($_POST);
        $recipe->create();
        
        

    } elseif ($operation == 'update') {
        $recipe=Alchemy_Recipe::loadById($_POST['Id']);
        $recipe->setValuesByArray($_POST);
        $recipe->update();
    } else {
        header('Location: ../index.php');
        exit;
    }
    
    //TODO Spara ingredienser/essenser
    //print_r($_POST);
    
    if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
        $recipe->deleteAllIngredients();
        $recipe->saveAllIngredients($_POST['IngredientId']);
    } elseif ($recipe->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
        
    }
    
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../alchemy_recipe_admin.php';
header('Location: ' . $referer);
