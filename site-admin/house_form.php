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
    	
    	<form action="house_admin.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
    		<table>
        		<tr>
            		<td>
            		<table>
            			<tr>
            				<td><label for="Name" class="header" style="width:10%">Namn</label></td>
            				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($house->Name); ?>" required style="width:20%"></td>
            			</tr>
        				<tr>
        					<td valign="top" class="header">Typ</td>
                			<td>
                				<input type="radio" id="IsHouse_house" name="IsHouse" value="1" <?php if ($house->IsHouse()) echo 'checked="checked"'?>> 
                    			<label for="IsHouse_house">Hus</label><br> 
                    			<input type="radio" id="IsHouse_camp" name="IsHouse" value="0" <?php if (!$house->IsHouse()) echo 'checked="checked"'?>> 
                    			<label for="IsHouse_camp">Lägerplats</label>
                			</td>
            			</tr>
            			<tr>
            				<td><label for="NumberOfBeds" class="header">Antal sovplatser/<br>tältplatser</label></td>
            				<td><input type="text" id="NumberOfBeds" name="NumberOfBeds" value="<?php echo htmlspecialchars($house->NumberOfBeds); ?>" required></td>
            			</tr>
            			<tr>
            				<td><label for="PositionInVillage" class="header">Plats</label></td>
            				<td>
            					<textarea id="PositionInVillage" name="PositionInVillage" rows="6" cols="50" maxlength="60000" required><?php echo htmlspecialchars($house->PositionInVillage); ?></textarea>
            				</td>
            			</tr>
            			<tr>
            				<td><label for="Description" class="header">Beskrivning</label></td>
            				<td>
            					<textarea id="Description" name="Description" rows="6" cols="50" maxlength="60000" required><?php echo htmlspecialchars($house->Description); ?></textarea>
            				</td>
            			</tr>
            			<tr>
            				<td></td><td><input id="submit_button" type="submit" value="<?php default_value('action'); ?>"></td>
            			</tr>
            			<tr>
            				<td>&nbsp;</td>
            			</tr>
            		</table>
            		</td>
            		<td>
        				<?php 
                	        if ($house->hasImage()) {
                	            $image = $house->getImage();
                                echo "<img src='../includes/display_image.php?id=$house->ImageId'/><br>\n
                                      <a href='logic/delete_image.php?id=$house->Id&type=house'>Ta bort bild</a>\n";
                                if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
                                
                	        } else {
                	            echo "<a href='upload_image.php?id=$house->Id&type=house'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a>\n";
                	        }
                            
                        ?>
        			</td>
    			</tr>
			</table>
    	</form>
    	</div>
    </body>

</html>