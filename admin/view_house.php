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
    				<td>Information till deltagare som ska bo i huset</td>
    				<td><?php echo nl2br(htmlspecialchars($house->NotesToUsers)); ?></td>
    			</tr>
    			<?php }?>
    			<tr><td></td></tr>
    			<tr>
    				<td colspan = '2'><b>De som bor i huset under <?php echo $current_larp->Name ?> är:</b><br>
    				<p>
    				<?php 
    				$personsInHouse = Person::personsAssignedToHouse($house, $current_larp);
    				$emails = array();
    				foreach ($personsInHouse as $personInHouse) {
    				    if ($personInHouse->isNotComing($current_larp)) continue;
    				    $personIdArr[] = $personInHouse->Id;
    				    echo "<a href='view_person.php?id=$personInHouse->Id'>$personInHouse->Name</a>";
    				    
    				    echo "<br>";
    				}
    				
    				?>
    				</p>
    				<?php echo contactSeveralEmailIcon("Maila alla som bor i huset", $personIdArr, 
    				                                    "Boende i $house->Name på $current_larp->Name", 
    				                                    "Meddelande till alla som bor i $house->Name på $current_larp->Name"); ?>
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
		</div>
    </body>

</html>