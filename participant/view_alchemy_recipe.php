<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RoleId = $_GET['id'];
        $RecipeId = $_GET['recipeId'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$role = Role::loadById($RoleId);
$person = $role->getPerson();

if ($person->Id != $current_person->Id) {
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


$recipe = Alchemy_Recipe::loadById($RecipeId);

if ($recipe->CampaignId != $current_larp->CampaignId) {
    header('Location: index.php'); // fel kampanj
    exit;
    
}


include 'navigation.php';
?>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-scroll"></i>
			Recept för <?php echo $recipe->Name?>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Skapat av</div>
		<?php 
			$author = $recipe->getAuthorRole();
			if (isset($author)) echo $author->Name;
		?>
		</div>

  		<div class='itemcontainer'>
       	<div class='itemname'>Hemligt</div>
		<?php echo ja_nej($recipe->isSecret()); ?>
		</div>
 
  		<div class='itemcontainer'>
       	<div class='itemname'>Typ</div>
		<?php echo $recipe->getRecipeType() ?>
		</div>

 		<div class='itemcontainer'>
       	<div class='itemname'>Nivå</div>
		<?php echo $recipe->Level; ?>
		</div>

 		<div class='itemcontainer'>
       	<div class='itemname'>Tillverkas av</div>
		<?php 
    		echo "<table class='small_data'>";
    		if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) { 
    		    echo "<tr><th>Ingrediens</th><th>Nivå</th><th>Essens</th></tr>";
    		    $ingredients = $recipe->getSelectedIngredients();
    		    foreach ($ingredients as $ingredient) {
    		        echo "<tr>";
    		        echo "<td>$ingredient->Name</td><td>$ingredient->Level</td>";
    		        if ($ingredient->isCatalyst()) echo "<td>Katalysator</td>";
    		        else echo "<td>".$ingredient->getEssenceNames()."</td>";
    		        echo "</tr>";
    		    }
    		} elseif ($recipe->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
    		    echo "<tr><th>Essens</th><th>Nivå</th></tr>";
    		    
    		    $essences = Alchemy_Essence::all();
    		    
    		    $selectedEssences = $recipe->getSelectedEssenceIds();
    		    foreach($selectedEssences as $selectedEssenceArr) {
    		        $selectedEssence = null;
    		        foreach ($essences as $essence) {
    		            if ($essence->Id == $selectedEssenceArr[0]) {
    		                $selectedEssence = $essence;
    		                break;
    		            }
    		        }
    		        
    		        echo "<tr><td>$selectedEssence->Name</td><td>".$selectedEssenceArr[1]."</td></tr>";
    		    }
    		    echo "<tr><td>Katalysator</td><td>Nivå $recipe->Level</td></tr>";
    
    		    
    		}
    		echo "</table>";
    		if ($recipe->containsOppositeEssences()) echo "<br>".showStatusIcon(false) . " Receptet innehåller motsatta essenser.";
    
		?>
		</div>

		<div class='itemcontainer'>
       	<div class='itemname'>Summa poäng ingredienser</div>
		<?php 
		
		echo $recipe->calculatePoints(); 
		echo " poäng<br>";
		echo "Receptets nivå kräver ";
		echo Alchemy_Recipe::LEVEL_REQUIREMENTS[$recipe->Level];
		
		?>
 		</div>

  		<div class='itemcontainer'>
       	<div class='itemname'>Beskrivning</div>
		<?php echo nl2br(htmlspecialchars($recipe->Description)); ?>
		</div>

  		<div class='itemcontainer'>
       	<div class='itemname'>Beredning</div>
		<?php echo nl2br(htmlspecialchars($recipe->Preparation)); ?>
		</div>

  		<div class='itemcontainer'>
       	<div class='itemname'>Effekt</div>
		<?php echo nl2br(htmlspecialchars($recipe->Effect)); ?>
		</div>

		<?php if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) { ?>
      		<div class='itemcontainer'>
           	<div class='itemname'>Bieffekt</div>
    		<?php echo nl2br(htmlspecialchars($recipe->SideEffect)); ?>
    		</div>
		<?php } ?>




	</div>
		


</body>
</html>
