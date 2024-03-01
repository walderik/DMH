<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RoleId = $_GET['id'];
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

$alchemist = Alchemy_Alchemist::getForRole($role);

if (isset($_GET['operation']) && $_GET['operation']=='remove_recipe') {
    $alchemist->removeRecipe($_GET['RecipeId']);
}



include 'navigation.php';
?>

	<div class="content">
		<h1>Alkemist <?php echo $role->Name?></a>
		</h1>
		

		<div>
		

    		<table>
    			<tr>
    				<td>Typ 
    				</td>
    				<td><?php echo $alchemist->getAlchemistType() ?>
                    </td>
    			</tr>
    			<tr>
    				<td>Nivå 
    				</td>
    				<td>
    					<?php echo nl2br(htmlspecialchars($alchemist->Level)); ?>
                    </td>
    			</tr>

				<tr>
    				<td>Utrustning</td>
    				<td>
    					<?php 
    					echo "<a href='upload_image.php?id=$alchemist->Id&type=alchemist'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
    					if ($alchemist->hasEquipmentImage()) {
    					    echo "<br>";
    					    $image = Image::loadById($alchemist->ImageId);
    
    					        echo "<img width='300' src='../includes/display_image.php?id=$alchemist->ImageId'/>\n";
    					        if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
    
    					}
    					?>
    					
    				</td>
    			</tr>
     			<tr>
    				<td>Workshop datum</td>
    				<td><?php echo $alchemist->Workshop; ?></td>
    			</tr>
    			<tr><td></td></tr>
    		</table>

			<h2>Recept</h2>
			Här är en lista på de recept du kan. Om något saknas kan du titta på listan med alla recept. Där kan du markera, blad de godkända recepten vilka du skulle vilja kunna. Och du kan även lägga till nya recept. 
			Dessa kommer att godkännas av arrangörerna innan du får möjlighet att önska att du kan dem.<br>
				<a href='alchemy_all_recipes.php?RoleId=<?php echo $role->Id?>'>Visa alla recept som finns / Önska recept du vill kunna / Skapa nya recept</a><br><br>

			<?php 
			$recipes = $alchemist->getRecipes();
			if (empty($recipes)) {
			    echo "Inga recept, än.";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Namn</th><th>Nivå</th><th>Typ</th><th>Effekt</th><th>Fick på/till<br>lajvet</th><th></th></tr>";
				foreach ($recipes as $recipe) {
				    echo "<tr><td><a href='view_alchemy_recipe.php?recipeId=$recipe->Id&id=$role->Id'>$recipe->Name</td><td>$recipe->Level</td><td>".$recipe->getRecipeType()."</td><td>$recipe->Effect</td>";
				    echo "<td>";
				    $approvedLarpId = $alchemist->recipeApprovedLarp($recipe);
				    if (isset($approvedLarpId)) {
				        $larp = LARP::loadById($approvedLarpId);
				        echo $larp->Name;
				    } else {
				        echo "Inte godkänt, än.";
				    }
				    echo "</td>\n";
				    echo "<td>";
				    //if (!isset($approvedLarpId)) {
				    echo "<a href='view_alchemist.php?operation=remove_recipe&RecipeId=$recipe->Id&id=$role->Id'><i class='fa-solid fa-xmark' title='Ta bort recept från alkemis'></i></a></td>";
				    //}
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			

		</div>
		


</body>
</html>
