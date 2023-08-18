<?php
include_once 'header.php';

    $house = House::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
         $house = House::loadById($_GET['id']);
    }
      
    
    include "navigation.php";
    
    ?>
    
     
<style>

img {
  float: right;
}
</style>

    <div class="content"> 
    	<h1><?php echo $house->Name ?></h1>
	        <?php 
	        if ($house->hasImage()) {
	            $image = Image::loadById($house->ImageId);
                echo "<td>";
                echo '<img src="data:image/jpeg;base64,'.base64_encode($image->file_data).'"/>';
                if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
                echo "</td>";
            }
            ?>
    	
    		<table>
    			<tr>
    			
    				<td>Antal 
    				<?php 
    				if ($house->IsHouse()) echo "sovplatser"; 
    				else echo "tältplatser";
    				?>
    				</td>
    				<td><?php echo htmlspecialchars($house->NumberOfBeds); ?></td>
    			</tr>
    			<tr>
    				<td>Plats</td>
    				<td>
    				<?php                 
    				if ($house->IsHouse()) echo "Hus";
                    else echo "Lägerplats";
    				?>
    				</td>
    			</tr>
    			<tr>
    				<td>Plats</td>
    				<td><?php echo nl2br(htmlspecialchars($house->PositionInVillage)); ?></td>
    			</tr>
    			<tr>
    				<td>Beskrivning</td>
    				<td><?php echo nl2br(htmlspecialchars($house->Description)); ?></td>
    			</tr>
    		</table>
    </body>

</html>