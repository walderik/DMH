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
    
    include 'navigation_subpage.php';
    
    ?>
    
     
<style>

img {
  float: right;
}
</style>

    <div class="content"> 
    	<h1><?php echo default_value('action');?> hus</h1>
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
    				<td><input type="text" id="Name" name="Name" value="<?php echo $house->Name; ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="NumberOfBeds">Antal sovplatser</label></td>
    				<td><input type="text" id="NumberOfBeds" name="NumberOfBeds" value="<?php echo $house->NumberOfBeds; ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="PositionInVillage">Plats i byn</label></td>
    				<td><textarea id="PositionInVillage" name="PositionInVillage" rows="4" cols="121" maxlength="60000" required><?php echo $house->PositionInVillage; ?></textarea></td>
    			</tr>
    			<tr>
    				<td><label for="Description">Beskrivning</label></td>
    				<td><textarea id="Description" name="Description" rows="4" cols="121" maxlength="60000" required><?php echo $house->Description; ?></textarea></td>
    			</tr>
    		</table>
     		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
    	</form>
    	</div>
    </body>

</html>