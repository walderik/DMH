<?php
include_once 'header.php';
include_once '../includes/selection_data_control.php';

    $object;
    $current_permissions = array();
    
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
            $object = OfficialType::newWithDefault();

        } elseif ($operation == 'update') {
            $object = OfficialType::loadById($_GET['id']);          
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
    $current_permissions = $object->getPermissions();
    
    include 'navigation.php';
    ?>

    <div class="content"> 
    <h1><?php echo default_value('action');?> Typ av funktionärer <a href="officialtype_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="officialtype_admin.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
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
			<tr>
				<td>Behörigheter</td>
				<td>
            		<?php 

            		$permissions = AccessControl::OFFICIAL_ACCESS_TYPES;
            		foreach ($permissions as $key => $permission) {
            		    echo "<input type='checkbox' id='Permission$key' name='Permission[]' value='$key'";
            		    if (!empty($current_permissions) && in_array($key, $current_permissions)) echo " checked=checked ";
            		    echo ">";
            		    echo " <label for='Permission$key'>$permission</label><br>";
            		}
            		
            		
            		?>
        		</td>
 			</tr>
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>