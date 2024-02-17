<?php
include_once 'header.php';
include_once '../includes/selection_data_control.php';
if (!AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) {
    exit;
}

    $object;
    $type;
    $objectType;
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (isset($_GET['type'])) {
            $type = $_GET['type'];
        }
        else {
            header('Location: index.php');
            exit;
            
        }
        $objectType = getObjectType($type);
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
            $object = call_user_func($objectType . '::newWithDefault');

        } elseif ($operation == 'update') {
            $object = call_user_func($objectType . '::loadById', $_GET['id']);          
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $object;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($object->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $object->Id;
                break;
            case "action":
                if (is_null($object->Id)) {
                    $output = "Lägg till";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    
    include 'navigation.php';
    ?>

    <div class="content"> 
    <h1><?php echo default_value('action');?> <?php echo getObjectName($type);?> <a href="selection_data_admin.php?type=<?php echo $type; ?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="selection_data_admin.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="type" name="type" value="<?php echo $type; ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($object->Name); ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="Description">Beskrivning</label></td>
				<td><textarea id="Description" name=Description rows="4"
						cols="50" maxlength="60000" required><?php echo htmlspecialchars($object->Description); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="Active">Valbar</label></td>
				<td><input type="checkbox" id="Active" name="Active" <?php if ($object->Active == 1) {echo "checked";} ?> ></td>
			</tr>
			<tr>

				<td><label for="SortOrder">Sorteringsordning</label></td>
				<td><input type="text" id="SortOrder" name="SortOrder"
					 value="<?php echo $object->SortOrder; ?>"></td>
			</tr>

			<?php if ($objectType == 'IntrigueType') {?>
			<tr>

				<td>För vilka ska<br>alternativet finnas?</label></td>
				<td>
				<input type="checkbox" id="ForRole" name="ForRole" <?php if ($object->ForRole == 1) {echo "checked";} ?> ><label for="ForRole">Karaktärer</label>
				<input type="checkbox" id="ForGroup" name="ForGroup" <?php if ($object->ForGroup == 1) {echo "checked";} ?> ><label for="ForGroup">Grupper</label>
				</td>
			</tr>
			<?php } ?>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>