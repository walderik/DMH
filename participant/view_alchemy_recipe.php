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

if ($person->UserId != $current_user->Id) {
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

	<div class="content">
		<h1>Recept för <?php echo $recipe->Name?></a>
		</h1>
		

		<div>
		

    		<table>
    			<tr>
    				<td>Skapat av 
    				</td>
    				<td>
    					<?php 
    					$author = $recipe->getAuthorRole();
    					if (isset($author)) echo $author->Name;
    					?>
                    </td>
    			</tr>
     			<tr>
    				<td>Hemligt 
    				</td>
    				<td>
    					<?php 
    					echo ja_nej($recipe->isSecret());
    					?>
                    </td>
    			</tr>
    			<tr>
    				<td>Typ 
    				</td>
    				<td><?php echo $recipe->getRecipeType() ?>
                    </td>
    			</tr>
    			<tr>
    				<td>Nivå 
    				</td>
    				<td>
    					<?php echo $recipe->Level; ?>
                    </td>
    			</tr>

				<tr>
    				<td>Tillverkas av</td>
    				<td>
    				
					<?php 
					echo "<table>";
					if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) { 
					    $ingredients = $recipe->getSelectedIngredients();
					    foreach ($ingredients as $ingredient) {
					        echo "<tr>";
					        echo "<td>$ingredient->Name</td><td>Nivå $ingredient->Level</td>";
					        if ($ingredient->isCatalyst()) echo "<td>Katalysator</td>";
					        else echo "<td>".$ingredient->getEssenceNames()."</td>";
					        echo "</tr>";
					    }
					} elseif ($recipe->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {

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
					        
					        echo "<tr><td>$selectedEssence->Name</td><td>Nivå ".$selectedEssenceArr[1]."</td></tr>";
					    }
					    echo "<tr><td>Katalysator</td><td>Nivå $recipe->Level</td></tr>";

					    
					}
					echo "</table>";
					if ($recipe->containsOppositeEssences()) echo "<br>".showStatusIcon(false) . " Receptet innehåller motsatta essenser.";

    					?>
    					
    				</td>
    			</tr>
     			<tr>
    				<td>Beredning</td>
    				<td><?php echo nl2br(htmlspecialchars($recipe->Preparation)); ?></td>
    			</tr>
     			<tr>
    				<td>Effekt</td>
    				<td><?php echo nl2br(htmlspecialchars($recipe->Effect)); ?></td>
    			</tr>
			<?php if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) { ?>
     			<tr>
    				<td>Bieffekt</td>
    				<td><?php echo nl2br(htmlspecialchars($recipe->SideEffect)); ?></td>
    			</tr>
			<?php } ?>
    			<tr><td></td></tr>
    		</table>

		</div>
		


</body>
</html>
