<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $RoleId = $_GET['RoleId'];
}


$role = Role::loadById($RoleId);

if ($role->PersonId != $current_person->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}
if (!Alchemy_Alchemist::isAlchemist($role)) {
    header('Location: index.php'); // karaktären är inte alkemist
    exit;
}
if (!$current_larp->isAlchemyInputOpen()) {
    header('Location: index.php'); // sista datum för alkemister har passerat
    exit;
}

$alchemist = Alchemy_Alchemist::getForRole($role);


include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

	<div class='itemselector'>
		<div class="header">
			<i class="fa-solid fa-scroll"></i> Alla recept
		</div>
   		<div class='itemcontainer'>
			Bocka för de recept du skulle vilja kunna. Arrangörerna godkänner sedan att du faktiskt kan dem.<br>
			Om du vill ha ett recept som inte redan finns i listan får du skapa ett nytt recept. Efter att du har skapat ett recept måste arrangörerna godkänna det innan du kan önska att få kunna det.
		</div>
   		<div class='itemcontainer'>
		
		<?php if ($alchemist->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY)  echo "<strong>";?>
            <a href="alchemy_recipe_form.php?RoleId=<?php echo $role->Id?>&type=<?php echo Alchemy_Alchemist::INGREDIENT_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt traditionellt recept</a>&nbsp;&nbsp;  
		<?php if ($alchemist->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY)  echo "</strong>";?>
		<br>
		<?php if ($alchemist->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY)  echo "<strong>";?>
        <a href="alchemy_recipe_form.php?RoleId=<?php echo $role->Id?>&type=<?php echo Alchemy_Alchemist::ESSENCE_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt experimentellt recept</a>  
		<?php if ($alchemist->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY)  echo "</strong>";?>

		</div>
			
	
	    <form action="logic/alchemy_alchemist_recipe_save.php" method="post">
		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id;?>">

       <?php
    
       $recipes = Alchemy_Recipe::allNotSecretByCampaign($current_larp);
       if (!empty($recipes)) {
           
           foreach ($recipes as $recipe) {
                echo "<div class='itemcontainer'>";
                echo "<details><summary>";
                if ($recipe->isKnown($alchemist)) {
                    echo showStatusIcon(true)." ";
                } elseif ($recipe->isWishedFor($alchemist)) {
                    echo showQuestionmarkIcon()." ";
                } elseif ($recipe->isApproved()) {
                    echo "<input type='checkbox' id='Ingridient$recipe->Id' name='RecipeId[]' value='$recipe->Id'> ";
                }
                
                echo "<span class='itemname'><a href='view_alchemy_recipe.php?recipeId=$recipe->Id&id=$role->Id'>$recipe->Name</a> ";
                echo "</span></summary>";
                echo "Nivå: $recipe->Level<br>";
                echo "Typ: ";
                if ($alchemist->AlchemistType == $recipe->AlchemistType) echo "<strong>";
                echo $recipe->getRecipeType();
                if ($alchemist->AlchemistType == $recipe->AlchemistType) echo "</strong>";
                echo "<br>";
                echo "Effekt: $recipe->Effect<br>";
                echo "</details>";
                echo "</div>";
            }
            echo "<div class='center'><button class='button-18 type='submit'>Önska recept</button></div>";
            echo "</form>";
            
        }
        else {
            echo "<p>Inga registrerade ännu</p>";
        }
        ?>
    </div>
	
</body>

</html>