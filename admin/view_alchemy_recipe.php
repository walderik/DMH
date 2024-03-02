<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RecipeId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
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
					if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) { 
					    $ingredients = $recipe->getSelectedIngredients();
					    foreach ($ingredients as $ingredient) {
					        echo "$ingredient->Name (Nivå $ingredient->Level)<br>";
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
					        
					        echo "$selectedEssence->Name (Nivå ".$selectedEssenceArr[1].")<br>";
					    }
					    echo "Katalysator (Nivå $recipe->Level)";
					    
					    
					}


    					?>
    					
    				</td>
    			</tr>
   			<tr>
    				<td>Summa poäng<br>ingredienser 
    				</td>
    				<td>
    					<?php 
    					
    					echo $recipe->calculatePoints(); 
    					echo " poäng<br>";
    					echo "Receptets nivå kräver ";
    					echo Alchemy_Recipe::LEVEL_REQUIREMENTS[$recipe->Level];
     					
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



			<h2>Alkemister som kan/vill kunna receptet</h2>
			<?php 
			$alchemists = $recipe->getAllAlchemists();

			if (empty($alchemists)) {
			    echo "Inga alkemister kan receptet, än.";
			} else {
			    echo "Alla alkemister som kan receptet visas, även de som inte kommer på just det här lajvet.";
				echo "<table class='small_data'>";
				echo "<tr><th>Namn</th><th>Nivå</th><th>Receptet godkänt för alkemisten</th><th>Kommer på lajvet</th><th></th></tr>";
				foreach ($alchemists as $alchemist) {
				    $role = $alchemist->getRole();
				    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
				    $isComing = !empty($larp_role);
				    echo "<tr><td><a href = view_role.php?id=$role->Id'>$role->Name</td>";
				    echo "<td>$alchemist->Level</td>";
				    echo "<td>";
				    $approvedLarpId = $alchemist->recipeApprovedLarp($recipe);
				    if (isset($approvedLarpId)) {
				        $larp = LARP::loadById($approvedLarpId);
				        echo $larp->Name;
				    } else {
				        echo showStatusIcon(false,  "logic/approve_alchemist_recipe.php?recipeId=$recipe->Id&alchemistId=$alchemist->Id");
				    }
				    echo "</td>\n";
				    echo "<td>".showStatusIcon($isComing)."</td>";
				    echo "<td><a href='logic/view_alchemist_logic.php?operation=remove_recipe&RecipeId=$recipe->Id&id=$alchemist->Id'><i class='fa-solid fa-xmark' title='Ta bort recept från alkemis'></i></a></td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			<p>
			<a href='choose_alchemist.php?id=<?php echo $recipe->Id?>&operation=add_recipe_alchemist'>Tilldela <?php echo $recipe->Name?> till alkemister</a>




		</div>
		


</body>
</html>
