<?php
include_once 'header.php';

$ingredient = Alchemy_Ingredient::newWithDefault();;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "insert";
    $type = "";
    if (isset($_GET['operation'])) $operation = $_GET['operation'];
    
    if ($operation == 'insert') {
        $ingredient->IsApproved = 1; //Default godkänd om den skapas av arrangör
        if (isset($_GET['type'])) $type = $_GET['type'];
        if ($type == 'katalysator') {
            $ingredient->IsCatalyst = 1;
        }
        else $ingredient->IsCatalyst = 0;
    } elseif ($operation == 'update') {
        $ingredient = Alchemy_Ingredient::loadById($_GET['id']);
    } else {
    }
}

function default_value($field) {
    GLOBAL $ingredient;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($ingredient->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "type":
            if ($ingredient->isCatalyst()) {
                $output = "katalysator";
                break;
            }
            $output = "ingrediens";
            break;
        case "id":
            $output = $ingredient->Id;
            break;
        case "action":
            if (is_null($ingredient->Id)) {
                $output = "Lägg till";
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
</style>


    <div class="content"> 
    <h1><?php echo default_value('action');?> <?php echo default_value('type');?> <a href="alchemy_ingredient_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    
   
	<form action="logic/alchemy_ingredient_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<input type="hidden" id="IsCatalyst" name="IsCatalyst" value="<?php echo $ingredient->IsCatalyst;?>">
 		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($ingredient->Name); ?>" size="100" maxlength="250" required></td>

			</tr>
			<tr>

				<td><label for="Description">Beskrivning</label></td>
 				<td><textarea id="Description" name="Description" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($ingredient->Description); ?></textarea></td>
					 
			</tr>
			<tr>
				<td><label for="Level">Nivå</label></td>
				<td><input type="number" id="Level" name="Level" value="<?php echo $ingredient->Level; ?>" min="1" step="1" size="10" maxlength="250" required></td>
			</tr>
				<?php if ($ingredient->isIngredient()) { ?>
			<tr>

				<td><label for="StorageLocation">Essenser</label></td>
				<td>
				<?php 
				$all_essences = Alchemy_Essence::allByCampaign($current_larp);
				$chosen_essences = $ingredient->getThreeEssences();
				selectionDropDownByArray('essence1', $all_essences, false, $chosen_essences[0]); 
				selectionDropDownByArray('essence2', $all_essences, false, $chosen_essences[1]); 
				selectionDropDownByArray('essence3', $all_essences, false, $chosen_essences[2]); 
				
				
				?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td><label for="ActualIngredient">Off-ingrediens</label></td>
				<td><input type="text" id="ActualIngredient" name="ActualIngredient" value="<?php echo htmlspecialchars($ingredient->ActualIngredient); ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="IsApproved">Godkänd</label></td>
    			<td>
    				<input type="radio" id="IsApproved_yes" name="IsApproved" value="1" <?php if ($ingredient->IsApproved()) echo 'checked="checked"'?>> 
        			<label for="IsApproved_yes">Ja</label><br> 
        			<input type="radio" id="IsApproved_no" name="IsApproved" value="0" <?php if (!$ingredient->IsApproved()) echo 'checked="checked"'?>> 
        			<label for="IsApproved_no">Nej</label>
    			</td>
			</tr>
			<tr>

				<td><label for="Info">Info</label></td>
				<td><textarea id="Info" name="Info" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($ingredient->Info); ?></textarea></td>

			</tr>
			<tr>

				<td><label for="OrganizerNotes">Anteckningar<br>för arrangörer</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($ingredient->OrganizerNotes); ?></textarea></td>

			</tr>
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>