<?php
include_once 'header_subpage.php';
include_once '../includes/selection_data_control.php';

?>

    
    <?php

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
            print_r($object);
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
    
    ?>

    <div class="content"> 
    <h1><?php echo default_value('action');?> <?php echo getObjectName($type);?></h1>
	<form action="selection_data_admin.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="type" name="type" value="<?php echo $type; ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo $object->Name; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="Description">Beskrivning</label></td>
				<td><textarea id="Description" name=Description rows="4"
						cols="50" required><?php echo $object->Description; ?></textarea></td>
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
			<tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>