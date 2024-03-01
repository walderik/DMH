<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $alchemistId = $_GET['alchemistId'];
    $recipeId = $_GET['recipeId'];
    
    $alchemist = Alchemy_Alchemist::loadById($alchemistId);
    $alchemist->grantRecipe($recipeId, $current_larp);
    header('Location: ../view_alchemist.php?id='.$alchemistId);
    exit;
}
header('Location: ../index.php?');
exit;


