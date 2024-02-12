<?php
include_once 'header.php';

$essence = Alchemy_Essence::newWithDefault();;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $essence = Alchemy_Essence::loadById($_GET['id']);
    } else {
    }
}

function default_value($field) {
    GLOBAL $essence;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($essence->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "id":
            $output = $essence->Id;
            break;
        case "action":
            if (is_null($essence->Id)) {
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
    <h1><?php echo default_value('action');?> essens <a href="alchemy_essence_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    
   
	<form action="logic/alchemy_essence_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
    			<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
 		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($essence->Name); ?>" size="100" maxlength="250" required></td>

			</tr>
			<tr>

				<td><label for="Description">Beskrivning</label></td>
 				<td><textarea id="Description" name="Description" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($essence->Description); ?></textarea></td>
					 
			</tr>
			<tr>

				<td><label for="StorageLocation">Element</label></td>
				<td><input type="text" id="Element" name="Element"
					 value="<?php echo htmlspecialchars($essence->Element); ?>" size="100" maxlength="250" ></td>
			</tr>
			<tr>
				<td><label for="OppositeEssenceId">Motsatt essens</label></td>
			<td><?php 
			     
			     $essences = Alchemy_Essence::allByCampaign($current_larp);
			     selectionByArray('OppositeEssence', $essences, false, false, $essence->OppositeEssenceId) ?></td>
			</tr>

			</tr>
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>