<?php
include_once 'header.php';


// get the parameters from URL
$alchemistId = $_REQUEST["alchemistId"];
//$larpId = $_REQUEST["larpId"];
if (!empty($alchemistId)) {
    $alchemist = Alchemy_Alchemist::loadById($alchemistId);
    $recipes = Alchemy_Recipe::getRecipesForAlchemist($alchemist, true);
} else {
    $recipes = Alchemy_Recipe::allByCampaign($current_larp);
}
$res = array();
foreach ($recipes as $recipe) {
    $res[] = $recipe->Id.":".$recipe->Name.", nivå ".$recipe->Level;
}

echo implode(";",$res);
    


