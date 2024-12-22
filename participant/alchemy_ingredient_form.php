<?php
include_once 'header.php';


$ingredient = Alchemy_Ingredient::newWithDefault();;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "insert";
    $type = "";
    $RoleId = $_GET['RoleId'];
    if (isset($_GET['operation'])) $operation = $_GET['operation'];
    
    if ($operation == 'insert') {
        if (isset($_GET['type'])) $type = $_GET['type'];
        if ($type == 'katalysator') {
            $ingredient->IsCatalyst = 1;
        }
        else $ingredient->IsCatalyst = 0;
    }
    
    
    
    
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
if (!Alchemy_Supplier::isSupplier($role)) {
    header('Location: index.php'); // karaktären är inte lövjerist
    exit;
}
if (!$current_larp->isAlchemySupplierInputOpen()) {
    header('Location: index.php'); // sista datum för lövjerister är passerat
    exit;
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
                $output = "Skapa ny";
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

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-leaf"></i>
		<?php echo default_value('action');?> <?php echo default_value('type');?>
	</div>
   
	<form action="logic/alchemy_ingredient_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<input type="hidden" id="IsCatalyst" name="IsCatalyst" value="<?php echo $ingredient->IsCatalyst;?>">
		<input type="hidden" id="IsApproved" name="IsApproved" value="0">
		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id?>">

   		<div class='itemcontainer'>
       	<div class='itemname'>Namn</div>
		<input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($ingredient->Name); ?>" maxlength="250" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Beskrivning</div>
		<textarea id="Description" name="Description" rows="4" maxlength="60000" ><?php echo htmlspecialchars($ingredient->Description); ?></textarea>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Nivå</div>
		<input type="number" id="Level" name="Level" value="<?php echo $ingredient->Level; ?>" min="1" max="5" step="1" size="10" maxlength="250" required>
		</div>

		<?php if ($ingredient->isIngredient()) { ?>

  			<div class='itemcontainer'>
       		<div class='itemname'>Essenser</div>
			En ingrediens kan ha 1-3 essenser.<br>
			
			<?php 
			$all_essences = Alchemy_Essence::allByCampaign($current_larp);
			
			selectionDropDownByArray('essence1', $all_essences, false, null); 
			selectionDropDownByArray('essence2', $all_essences, false, null); 
			selectionDropDownByArray('essence3', $all_essences, false, null); 
			
			
			?>
			</div>
		<?php } ?>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Info</div>
		<textarea id="Info" name="Info" rows="4" maxlength="60000" ><?php echo htmlspecialchars($ingredient->Info); ?></textarea>
		</div>

		<div class='center'><input id="submit_button" type="submit" class='button-18' value="<?php default_value('action'); ?>"></div>
	</form>
	</div>
    </body>

</html>