<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $RoleId = $_GET['RoleId'];
    $role = Role::loadById($RoleId);

    if (isset($_GET['recipeId'])) {
        $recipe = Alchemy_Recipe::loadById($_GET['recipeId']);
        $type = $recipe->AlchemistType;
    } else {
        $recipe = Alchemy_Recipe::newWithDefault();
        $recipe->AuthorRoleId = $RoleId;
        $recipe->AlchemistType = $_GET['type'];
        $type = $recipe->AlchemistType;
    }

}

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

if ($role->Id != $recipe->AuthorRoleId) {
    header('Location: index.php'); // inte ditt recept
    exit;
}

if ($recipe->isApproved()) {
    header('Location: index.php'); // receptet får inte redigeras
    exit;
    
}

$alchemist = Alchemy_Alchemist::getForRole($role);


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

.essences {
    font-size: 8px;
}
</style>
<script src="../javascript/recipe_calc.js"></script>

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-scroll"></i>
		<?php echo default_value('action');?> recept
	</div>
    
   
	<form action="logic/alchemy_recipe_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="AlchemistType" name="AlchemistType" value="<?php echo $recipe->AlchemistType ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer ?>">
		<input type="hidden" id="IsApproved" name="IsApproved" value="0">
		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id?>">
		<input type="hidden" id="AuthorRoleId" name="AuthorRoleId" value="<?php echo $role->Id ?>"> 
 
		<div class='itemcontainer'>
       	<div class='itemname'>Namn</div>
		<input type="text" id="Name" name="Name" value="<?php echo $recipe->Name ?>" size="100" maxlength="250" required>		
		</div>
 
		<div class='itemcontainer'>
       	<div class='itemname'>Typ</div>
		<?php echo Alchemy_Alchemist::ALCHEMY_TYPES[$type]?>		
		</div>
 
		<div class='itemcontainer'>
       	<div class='itemname'>Nivå</div>
       	1. Grundläggande alkemi – Milt sinnesförändrande effekt.<br>
        2. Lätt alkemi – Starkt sinnesförändrande.<br>
        3. Svår alkemi – Effekten är spelpåverkande.<br>
        4. Avancerad alkemi – Denna nivån påverkar spelet i stor utsträckning.<br>
        5. Fulländad alkemi – Det man skapar med fulländad alkemi blir en artefakt som kan användas flera gånger och/eller som kraftigt påverkar spelet över flera lajv. Se sektionen för artefakter.
       	<br><br>
		<select id="Level" name="Level" style='width:100%'>
		<?php 
		for ($i=1; $i<=5; $i++) {
		    echo "<option value='$i' ";
		    if ($recipe->Level==$i) echo "selected";
		    echo ">Nivå $i: ";
		    echo Alchemy_Recipe::LEVEL_REQUIREMENTS[$i];
		    echo "</option>";
		}
		?>
		</select>
		</div>

		<div class='itemcontainer'>
       	<div class='itemname'>Summa poäng ingredienser</div>
		<?php 
		echo "<span id='points'>";
		echo $recipe->calculatePoints(); 
		echo "</span>";
		echo " poäng<br>";
		?>
		</div>
 
		<div class='itemcontainer'>
       	<div class='itemname'>Tillverkas av</div>
		<?php 
		if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
		    echo "Markera de ingredienser som ingår<br>";
		    $selectedIngredientIds = $recipe->getSelectedIngredientIds();
            for ($i = 1; $i <= 5; $i++) {
                $ingredients = Alchemy_Ingredient::getIngredientsByLevel($i, $current_larp);
                echo "<details>";
                echo "<summary>Nivå $i, ".Alchemy_Ingredient::POINTS[$i]." poäng</summary>";
                echo "<div class='ingredient-area'>";

                foreach ($ingredients as $ingredient) {
                    $id = "ingredient_".$ingredient->Id;
                    $checked="";
                    if (in_array($ingredient->Id, $selectedIngredientIds)) {
                        $checked="checked='checked'";
                    }
                    echo "<input onchange='calc_points(this, ".Alchemy_Ingredient::POINTS[$i].")' type='checkbox' class='hidden' name='IngredientId[]' id='$id' value='$ingredient->Id' $checked>";
                    echo "<label for='$id' >$ingredient->Name<br><span class='essences'>".$ingredient->getEssenceNames()."</span></label>";
                }
		    
	            echo "</div>";
	            echo "</details>";
            }
            $catalysts = Alchemy_Ingredient::getAllCatalysts($current_larp);
            echo "<details>";
            
            echo "<summary>Katalysator</summary>";
            echo "<div class='ingredient-area'>";

            foreach ($catalysts as $ingredient) {
                $id = "ingredient_".$ingredient->Id;
                $checked="";
                if (in_array($ingredient->Id, $selectedIngredientIds)) {
                    $checked="checked='checked'";
                }
                echo "<input type='checkbox' class='hidden' name='IngredientId[]' id='$id' value='$ingredient->Id' $checked>";
                echo "<label for='$id'>$ingredient->Name (Nivå $ingredient->Level)</label>";
            }
            
            echo "</div>";
            echo "</details>";
            

		} elseif ($recipe->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
		    echo "Markera de essenser som ingår och på vilken nivå de ska vara.<br>";
		    $essences = Alchemy_Essence::allByCampaign($current_larp);
		    for ($i = 1; $i <= 5; $i++) {
		        $selectedEssences = $recipe->getSelectedEssencesPerLevelIds($i);
		        echo "<details>";
		        echo "<summary>Nivå $i, ".Alchemy_Ingredient::POINTS[$i]." poäng</summary>";
		        echo "<div class='ingredient-area'>";
		        
		        foreach ($essences as $essence) {
		            $checked="";
		            if (in_array($essence->Id, $selectedEssences)) {
		                $checked="checked='checked'";
		            }
		            $id = "essence_L".$i."_".$essence->Id;
		            echo "<input  onchange='calc_points(this, ".Alchemy_Ingredient::POINTS[$i].")' type='checkbox' class='hidden' name='Essences[]' id='$id' value='$i"."_"."$essence->Id' $checked>";
		            echo "<label for='$id'>$essence->Name</label>";
		            
		        }
		        
		        echo "</div>";
		        echo "</details>";
		    }
		    echo "Katalysator med samma nivå som receptet.";
		}
		
		
		?>
		</div>
 
		<div class='itemcontainer'>
       	<div class='itemname'>Beskrivning</div>
		<textarea id="Description" name="Description" rows="4" maxlength="6000"><?php echo htmlspecialchars($recipe->Description); ?></textarea>
		</div>
 
		<div class='itemcontainer'>
       	<div class='itemname'>Beredning</div>
		<textarea id="Preparation" name="Preparation" rows="4" maxlength="6000"><?php echo htmlspecialchars($recipe->Preparation); ?></textarea>
		</div>
 
		<div class='itemcontainer'>
       	<div class='itemname'>Effekt</div>
		<textarea id="Effect" name="Effect" rows="4" maxlength="6000"><?php echo htmlspecialchars($recipe->Effect); ?></textarea>
		</div>
 
		<?php if ($recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) { ?>
    		<div class='itemcontainer'>
           	<div class='itemname'>Bieffekt</div>
    		<textarea id="SideEffect" name="SideEffect" rows="4" maxlength="6000"><?php echo htmlspecialchars($recipe->SideEffect); ?></textarea>
    		</div>
		<?php }?>

		<div class='itemcontainer'>
       	<div class='itemname'>Hemligt</div>
		<input type="radio" id="IsSecret_yes" name="IsSecret" value="1" <?php if ($recipe->isSecret()) echo 'checked="checked"'?>> 
		<label for="IsSecret_yes">Ja</label><br> 
		<input type="radio" id="IsSecret_no" name="IsSecret" value="0" <?php if (!$recipe->isSecret()) echo 'checked="checked"'?>> 
		<label for="IsSecret_no">Nej</label>
		</div>

		<div class='center'><input id="submit_button" type="submit" class='button-18' value="<?php default_value('action'); ?>"></div>
	</form>
	</div>
	
	
	

    </body>

</html>