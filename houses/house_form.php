<?php
include_once 'header.php';

    $house = House::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
            $type = "house";
            if (isset($_GET['type'])) $type = $_GET['type'];
            if ($type == "house") $house->IsHouse = 1;
            else $house->IsHouse = 0;
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
            				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($house->Name); ?>" required ></td>
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
            				<td><label for="NumberOfBeds" class="header">Antal tältplatser<br>Används för lägerplatser</label></td>
            				<td><input type="text" id="NumberOfBeds" name="NumberOfBeds" value="<?php echo htmlspecialchars($house->NumberOfBeds); ?>" required></td>
            			</tr>
            			<tr>
            				<td><label for="ComfortNumber" class="header">Komfortantal<br>Används för hus</label></td>
            				<td><input type="number" id="ComfortNumber" name="ComfortNumber" value="<?php echo htmlspecialchars($house->ComfortNumber); ?>" maxlength="5" min="0" required></td>
            			</tr>
            			<tr>
            				<td><label for="MaxNumber" class="header">Maxantal<br>Används för hus</label></td>
            				<td><input type="number" id="MaxNumber" name="MaxNumber" value="<?php echo htmlspecialchars($house->MaxNumber); ?>" maxlength="5" min="0" required></td>
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
            			<?php if ($house->IsHouse()) {?>
            			<tr>
            				<td><label for="NotesToUsers" class="header">Husbrev<br>(Information till deltagare som ska bo i huset)</label></td>
            				<td>
            					<textarea id="NotesToUsers" name="NotesToUsers" rows="6" cols="50" maxlength="600000" required><?php echo htmlspecialchars($house->NotesToUsers); ?></textarea>
            				</td>
            			</tr>

            			<tr>
            				<td><label for="InspectionNotes" class="header">Noteringar från besiktning</label></td>
            				<td>
            					<textarea id="InspectionNotes" name="InspectionNotes" rows="6" cols="50" maxlength="60000" required><?php echo htmlspecialchars($house->InspectionNotes); ?></textarea>
            				</td>
            			</tr>
            			<?php }?>
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