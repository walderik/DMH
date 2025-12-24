<?php
include_once 'header.php';

    $house = House::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
         $house = House::loadById($_GET['id']);
    }
      
    
    include "navigation.php";
    
    
    $larp_house = Larp_House::loadByIds($house->Id, $current_larp->Id);
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
    				<?php 
    				if ($house->IsHouse()) {
    				   echo "<td>Komfortantal</td><td>$house->ComfortNumber</td>";
    				   echo "</tr>";
                       echo "<tr>"; 
    				   echo "<td>Maxantal</td><td>$house->MaxNumber</td>";

    				} else {
    				    echo "<td>Antal tältplatser</td><td>$house->NumberOfBeds</td>";
    				}

                    ?>
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
    				<td>Husbrev<br>(Information till deltagare som ska bo i huset)</td>
    				<td><?php echo nl2br(htmlspecialchars($house->NotesToUsers)); ?></td>
    			</tr>
    			<?php }?>
    			<tr><td></td></tr>
     			<tr>
    				<td colspan = '2'><b>Kommentarer för <?php echo $current_larp->Name ?></b> <a href="edit_larp_house.php?id=<?php if (isset($larp_house)) echo $larp_house->Id?>"><i class='fa-solid fa-pen'></i></a>
    				</td>
				</tr>
    			<tr>
    				<td>Arrangörens anteckningar<br>(Visas inte för deltagare)</td>
    				<td><?php if (isset($larp_house)) echo nl2br(htmlspecialchars($larp_house->OrganizerComment)); ?></td>
    			</tr>
    			<tr>
    				<td>Kommentar till deltagare</td>
    				<td><?php if (isset($larp_house)) echo nl2br(htmlspecialchars($larp_house->PublicComment)); ?></td>
    			</tr>
    			
    			<tr><td></td></tr>
    			<tr>
    				<td colspan = '2'><b>Boende under <?php echo $current_larp->Name ?></b><br>
    				<p>
    				<?php 
    				$personsInHouse = Person::personsAssignedToHouse($house, $current_larp);
    				if (!empty($personsInHouse)) {
    				    $emails = array();
    				    $personIdArr = array();
    				    foreach ($personsInHouse as $personInHouse) {
        				    if ($personInHouse->isNotComing($current_larp)) continue;
        				    $personIdArr[] = $personInHouse->Id;
        				    echo $personInHouse->getViewLink();
        				    
        				    echo "<br>";
        				}
        				echo "</p>";
        				echo contactSeveralEmailIcon("Maila alla som bor i huset", $personIdArr, 
    				                                    "Boende i $house->Name på $current_larp->Name", 
    				                                    "Meddelande till alla som bor i $house->Name på $current_larp->Name");
    				} else {
    				    echo "Ingen tilldelad</p>";
    				}
    				?>
    				</td>
    			</tr>
    		</table>
    		</td>
    		<?php 
	        if ($house->hasImage()) {
	            $image = $house->getImage();
	            echo "<td>";
	            echo "<img width='300' src='../includes/display_image.php?id=$house->ImageId'/>\n";
	            if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
	            echo "</td>";
	        }
            ?>
    		</tr>
    		</table>
		</div>
    </body>

</html>