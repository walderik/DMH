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

ul.list {
    list-style-type: disc;
}
</style>

    <div class="content"> 
    	<h1><?php echo $house->Name ?></h1>
    	<table>
    		<tr>
    		<td>
    		<table>
    			<tr>
    				<td>Antal 
        				<?php 
        				if ($house->IsHouse()) echo "sovplatser"; 
        				else echo "tältplatser";
        				?>
    				</td>
    				<td>
    					<?php echo htmlspecialchars($house->NumberOfBeds); 
    					if ($house->IsHouse()) echo " (Hus)";
                        else echo " (Lägerplats)";?>
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
    			<?php if ($house->IsHouse() && isset($house->NotesToUsers)) {?>
    			<tr>
    				<td>Information om att bo i huset</td>
    				<td><?php echo nl2br(htmlspecialchars($house->NotesToUsers)); ?></td>
    			</tr>
    			<?php }?>
    			<tr>
    				<td colspan = '2'><b>De som bor i huset under <?php echo $current_larp->Name ?> är:</b><br>
    				<p>
    				<ul class="list">
    				<?php 
    				$personsInHouse = Person::personsAssignedToHouse($house, $current_larp);
    				foreach ($personsInHouse as $personInHouse) {
    				    if ($personInHouse->isNotComing($current_larp)) continue;
    				    if ($personInHouse->hasPermissionShowName()) echo "<li>".$personInHouse->Name;
    				    else echo "<li>(Vill inte visa sitt namn)";
    				    echo "<br>";
    				}
    				
    				?>
    				</ul>
    				</p>
    				</td>
				</tr>
    		</table>
    		</td>
    		<?php 
	        if ($house->hasImage()) {
	            $image = $house->getImage();
	            echo "<td>";
	            echo "<img src='../includes/display_image.php?id=$house->ImageId'/>\n";
	            if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
	            echo "</td>";
	        }
            ?>
    		</tr>
    		</table>
    </body>

</html>