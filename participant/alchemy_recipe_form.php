<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $type = $_GET['type'];
    $RoleId = $_GET['RoleId'];
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

$alchemist = Alchemy_Alchemist::getForRole($role);


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


    <div class="content"> 
    <h1>Skapa recept <a href="alchemy_recipe_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    
   
	<form action="logic/alchemy_recipe_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="insert"> 
		<input type="hidden" id="AlchemistType" name="AlchemistType" value="<?php echo $type ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<input type="hidden" id="IsApproved" name="IsApproved" value="0">
		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id?>"> 
 
 		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="" size="100" maxlength="250" required></td>

			</tr>
			<tr>

				<td>Typ</td>
 				<td><?php echo Alchemy_Alchemist::ALCHEMY_TYPES[$type]?></td>
					 
			</tr>
			<tr>
				<td><label for="Level">Nivå</label></td>
				<td><input type="number" id="Level" name="Level" value="1" min="1" max="<?php echo $alchemist->Level?>" step="1" size="10" maxlength="250" required></td>
			</tr>
			<tr>
				<td>Tillverkas av</td>
 				<td>
  				<?php 
  				if ($type == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
 				    echo "Markera de ingredienser som ingår<br>";
 				    
                    for ($i = 1; $i <= 5; $i++) {
                        $ingredients = Alchemy_Ingredient::getIngredientsByLevel($i, $current_larp);
                        echo "Nivå $i<br>";
                        echo "<div class='ingredient-area'>";

                        foreach ($ingredients as $ingredient) {
                            $id = "ingredient_".$ingredient->Id;
                           
                            echo "<input type='checkbox' class='hidden' name='IngredientId[]' id='$id' value='$ingredient->Id'>";
                            echo "<label for='$id'>$ingredient->Name</label>";
                         }
 				    
			            echo "</div><br>";
                    }
                    $catalysts = Alchemy_Ingredient::getAllCatalysts($current_larp);
                    echo "Katalysator<br>";
                    echo "<div class='ingredient-area'>";

                    foreach ($catalysts as $ingredient) {
                        $id = "ingredient_".$ingredient->Id;
                        echo "<input type='checkbox' class='hidden' name='IngredientId[]' id='$id' value='$ingredient->Id'>";
                        echo "<label for='$id'>$ingredient->Name (Nivå $ingredient->Level)</label>";
                    }
                    
                    echo "</div><br>";
                    
    
 				} elseif ($type == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
 				    echo "Markera de essenser som ingår och på vilken nivå de ska vara.<br>";
 				    $essences = Alchemy_Essence::allByCampaign($current_larp);
 				    for ($i = 1; $i <= 5; $i++) {
 				        
 				        echo "Nivå $i<br>";
 				        echo "<div class='ingredient-area'>";
 				        
 				        foreach ($essences as $essence) {
 				            $id = "essence_L".$i."_".$essence->Id;
 				            echo "<input type='checkbox' class='hidden' name='Essences[]' id='$id' value='$i"."_"."$essence->Id'>";
 				            echo "<label for='$id'>$essence->Name</label>";
 				        }
 				        
 				        echo "</div><br>";
 				    }
 				    echo "Katalysator<br>";
 				    echo "Katalysator med samma nivå som receptet.";
 				}
 				
 				
 				?>
 				</td>
			
			</tr>
			
			
			
			<tr>

				<td><label for="Description">Beredning</label></td>
 				<td><textarea id="Preparation" name="Preparation" rows="4" cols="100" maxlength="60000" required></textarea></td>
					 
			</tr>
			<tr>

				<td><label for="Description">Effekt</label></td>
 				<td><textarea id="Effect" name="Effect" rows="4" cols="100" maxlength="60000" required></textarea></td>
					 
			</tr>
			<?php if ($type == Alchemy_Alchemist::INGREDIENT_ALCHEMY) { ?>
			<tr>

				<td><label for="Description">Bieffekt</label></td>
 				<td><textarea id="SideEffect" name="SideEffect" rows="4" cols="100" maxlength="60000" required></textarea></td>
					 
			</tr>
			<?php }?>
		</table>

		<input id="submit_button" type="submit" value="Skapa">
	</form>
	</div>
    </body>

</html>