<?php
include_once 'header.php';

$recipe = Alchemy_Recipe::newWithDefault();;


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "insert";
    $type = "";
    if (isset($_GET['operation'])) $operation = $_GET['operation'];
    
    if ($operation == 'insert') {
        $recipe->IsApproved = 1; //Default godkänd om den skapas av arrangör
        if (isset($_GET['type'])) $recipe->AlchemistType = $_GET['type'];
    } elseif ($operation == 'update') {
        $recipe = Alchemy_Recipe::loadById($_GET['id']);
    } else {
    }
}

function default_value($field) {
    GLOBAL $recipe;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($recipe->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "id":
            $output = $recipe->Id;
            break;
        case "action":
            if (is_null($recipe->Id)) {
                $output = "Skapa";
                break;
            }
            $output = "Uppdatera";
            break;
    }
    
    echo $output;
}
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

include 'navigation.php';
?>
    
<style>

img {
  float: right;
}

.hidden {
  position: absolute;
  visibility: hidden;
  opacity: 0;
}

input[type=checkbox]+label {
  display: inline-block;
  color: white;
  padding: 8px 20px;
  font-family: Arial;
  border-radius: 25px;
  background-color: #cccccc;
  margin-top: 8px;
  margin-right: 2px;
 }

input[type=checkbox]:checked+label {
  display: inline-block;
  color: white;
  padding: 8px 20px;
  font-family: Arial;
  border-radius: 25px;
  background-color: #2100F3;
  margin-top: 8px;
  margin-right: 2px;
}

.ingredient-area {
    border: 1px solid #ccc !important;
    border-radius: 25px;
    padding: 5px;
}

</style>

<script>

function mousedown(event) {
    //this == event.target
    event.preventDefault();
    var scroll_offset= this.parentElement.scrollTop;
    this.selected= !this.selected;
    this.parentElement.scrollTop= scroll_offset;
}
function mousemove(event) {
    event.preventDefault();
}



</script>

    <div class="content"> 
    <h1><?php echo default_value('action');?> recept <a href="alchemy_recipe_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    
   
	<form action="logic/alchemy_recipe_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
 		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($recipe->Name); ?>" size="100" maxlength="250" required></td>

			</tr>
			<tr>

				<td>Typ</td>
 				<td><?php echo $recipe->getRecipeType()?></td>
					 
			</tr>
			<tr>
				<td><label for="Level">Nivå</label></td>
				<td><input type="number" id="Level" name="Level" value="<?php echo $recipe->Level; ?>" min="1" max="5" step="1" size="10" maxlength="250" required></td>
			</tr>
			<tr>
				<td>Tillverkas av</td>
 				<td>
  				<?php 
 				if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
 				    echo "Markera de ingredienser som ingår<br>";
                    for ($i = 1; $i <= 5; $i++) {
                        $ingredients = Alchemy_Ingredient::getIngredientsByLevel($i, $current_larp);
                        echo "Nivå $i<br>";
                        echo "<div class='ingredient-area'>";

                        foreach ($ingredients as $ingredient) {
                            $id = "ingredient_".$ingredient->Id;
                            echo "<input type='checkbox' class='hidden' name='$id' id='$id'>";
                            echo "<label for='$id'>$ingredient->Name</label>";
                         }
 				    
			            echo "</div><br>";
                    }
                    $catalysts = Alchemy_Ingredient::getAllCatalysts($current_larp);
                    echo "Katalysatorer<br>";
                    echo "<div class='ingredient-area'>";

                    foreach ($catalysts as $ingredient) {
                        $id = "ingredient_".$ingredient->Id;
                        echo "<input type='checkbox' class='hidden' name='$id' id='$id'>";
                        echo "<label for='$id'>$ingredient->Name (Nivå $ingredient->Level)</label>";
                    }
                    
                    echo "</div><br>";
                    
    
 				} elseif ($recipe->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
 				    echo "Markera de essenser som ingår<br>";
 				    $essences = Alchemy_Essence::allByCampaign($current_larp);
 				    for ($i = 1; $i <= 5; $i++) {
 				        echo "Nivå $i<br>";
 				        echo "<div class='ingredient-area'>";
 				        
 				        foreach ($essences as $essence) {
 				            $id = "essence_".$essence->Id;
 				            echo "<input type='checkbox' class='hidden' name='$id' id='$id'>";
 				            echo "<label for='$id'>$essence->Name</label>";
 				        }
 				        
 				        echo "</div><br>";
 				    }
 				    echo "Katalysatorer<br>";
 				    echo "<div class='ingredient-area'>";
 				    
 				    for ($i=1; $i <= 5; $i++) {
 				        $id = "catalyst_$i";
 				        echo "<input type='checkbox' class='hidden' name='$id' id='$id'>";
 				        echo "<label for='$id'>Katalysator nivå $i</label>";
 				    }
 				    
 				    echo "</div><br>";
 				}
 				
 				
 				?>
 				</td>
			
			</tr>
			
			
			
			<tr>

				<td><label for="Description">Beredning</label></td>
 				<td><textarea id="Preparation" name="Preparation" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($recipe->Preparation); ?></textarea></td>
					 
			</tr>
			<tr>

				<td><label for="Description">Effekt</label></td>
 				<td><textarea id="Effect" name="Effect" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($recipe->Effect); ?></textarea></td>
					 
			</tr>
			<tr>

				<td><label for="Description">Bieffekt</label></td>
 				<td><textarea id="SideEffect" name="SideEffect" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($recipe->SideEffect); ?></textarea></td>
					 
			</tr>
			<tr>

				<td><label for="IsApproved">Godkänd</label></td>
    			<td>
    				<input type="radio" id="IsApproved_yes" name="IsApproved" value="1" <?php if ($recipe->IsApproved()) echo 'checked="checked"'?>> 
        			<label for="IsApproved_yes">Ja</label><br> 
        			<input type="radio" id="IsApproved_no" name="IsApproved" value="0" <?php if (!$recipe->IsApproved()) echo 'checked="checked"'?>> 
        			<label for="IsApproved_no">Nej</label>
    			</td>
			</tr>
			<tr>

				<td><label for="OrganizerNotes">Anteckningar<br>för arrangörer</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($recipe->OrganizerNotes); ?></textarea></td>

			</tr>
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>