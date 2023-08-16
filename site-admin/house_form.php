<?php
include_once 'header.php';

    $house = House::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $house = House::loadById($_GET['id']);
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $house;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($house->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $house->Id;
                break;
            case "action":
                if (is_null($house->Id)) {
                    $output = "Lägg till";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    
    include "navigation.php";
    
    ?>
    
     
<style>

img {
  float: right;
}
</style>

    <div class="content"> 
    	<h1><?php echo default_value('action');?> hus eller lägerplats</h1>
    	        <?php 
    	        if ($house->hasImage()) {
    	            $image = Image::loadById($house->ImageId);
                echo "<td>";
                echo '<img src="data:image/jpeg;base64,'.base64_encode($image->file_data).'"/>';
                if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
                echo "</td>";
            }
            ?>
    	
    	
    	<form action="house_admin.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
    		<table>
    			<tr>
    				<td><label for="Name">Namn</label></td>
    				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($house->Name); ?>" required></td>
    			</tr>
				<tr><td valign="top" class="header">Typ</td>
        			<td>
        				<input type="radio" id="IsHouse_house" name="IsHouse" value="1" <?php if ($house->IsHouse()) echo 'checked="checked"'?>> 
            			<label for="IsHouse_house">Hus</label><br> 
            			<input type="radio" id="IsHouse_camp" name="IsHouse" value="0" <?php if (!$house->IsHouse()) echo 'checked="checked"'?>> 
            			<label for="IsHouse_camp">Lägerplats</label>
        			</td>
    			</tr>
    			<tr>
    				<td><label for="NumberOfBeds">Antal sovplatser/<br>tältplatser</label></td>
    				<td><input type="text" id="NumberOfBeds" name="NumberOfBeds" value="<?php echo htmlspecialchars($house->NumberOfBeds); ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="PositionInVillage">Plats</label></td>
    				<td><textarea id="PositionInVillage" name="PositionInVillage" rows="4" cols="121" maxlength="60000" required><?php echo htmlspecialchars($house->PositionInVillage); ?></textarea></td>
    			</tr>
    			<tr>
    				<td><label for="Description">Beskrivning</label></td>
    				<td><textarea id="Description" name="Description" rows="4" cols="121" maxlength="60000" required><?php echo htmlspecialchars($house->Description); ?></textarea></td>
    			</tr>
    		</table>
     		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
    	</form>
    	</div>
    </body>

</html>