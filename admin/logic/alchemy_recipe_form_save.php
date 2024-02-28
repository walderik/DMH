<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $recipe = Alchemy_Recipe::newFromArray($_POST);
        $recipe->create();
        
        //TODO Spara ingredienser/essenser
    } elseif ($operation == 'update') {
        $recipe=Alchemy_Recipe::loadById($_POST['Id']);
        $recipe->setValuesByArray($_POST);
        $recipe->update();
        //TODO Spara ingredienser/essenser
        
    }
    
}
$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../alchemy_recipe_admin.php';
header('Location: ' . $referer);
