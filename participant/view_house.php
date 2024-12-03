<?php
include_once 'header.php';

$house = House::newWithDefault();

if (isset($_GET['id']))  {
     $house = House::loadById($_GET['id']);
     if (empty($house)) {
         header('Location: index.php?error=no_house');
     }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    if (isset($_POST['operation']) && $_POST['operation']=='updateNotesToUser' && $house->IsHouse() && $current_user->hasEditRightToHouse($house) )  { # isset($_GET['NotesToUsers'])
        $house->NotesToUsers = $_POST['NotesToUsers'];
        $house->update();
    }
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


<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet"/>  

    <div class="content"> 
    	<h1><?php echo $house->Name;
    	if ($house->IsHouse() && $current_user->hasEditRightToHouse($house)) {
    	    echo " &nbsp;<a href='house_form.php?operation=update&id=$house->Id' title='Redigera husbrevet'><i class='fa-solid fa-pen'> </i></a>";
    	}
    	?></h1>
    	<table>
    		<tr>
    		<td>
    		<table>
    			<tr>
    				<td><b>Antal 
        				<?php 
        				if ($house->IsHouse()) echo "sovplatser"; 
        				else echo "tältplatser";
        				?></b>
    				</td>
    				<td>
    					<?php echo htmlspecialchars($house->NumberOfBeds); 
    					if ($house->IsHouse()) echo " (Hus)";
                        else echo " (Lägerplats)";?>
    				</td>
    			</tr>
    			<tr>
    				<td><b>Plats</b></td>
    				<td><?php echo nl2br(htmlspecialchars($house->PositionInVillage)); ?></td>
    			</tr>
    			<tr>
    				<td><b>Beskrivning</b></td>
    				<td><?php echo nl2br(htmlspecialchars($house->Description)); ?></td>
    			</tr>
    			<?php if ($house->IsHouse() && isset($house->NotesToUsers)) {?>
        			<tr>
        				<td><b>Information om att bo i huset</b></td>
        				<td><?php echo nl2br(htmlspecialchars($house->NotesToUsers)); ?></td>
        			</tr>
    			<?php }
    			if (isset($house->Lat) && isset($house->Lon)) echo "<tr><td></td></tr><tr><td colspan='3'><div id='osm-map'></div></td></tr><tr><td></td></tr>"; 
    			
    			if ($house->IsHouse() && $current_user->hasEditRightToHouse($house)) {
    			    $caretakers = $house->getHousecaretakers();
                    echo "<table id='caretakers' class='data' style='width:40%;' >";
                	echo "<th>Husförvaltare</th>";
    				foreach ($caretakers as $caretaker) {
    				    $person = $caretaker->getPerson();
    				    echo "<tr>\n";
    				    echo "  <td>$person->Name</td>\n";
    				    echo "</tr>\n";
    				}
        			echo "</table>\n";
    			} ?>
    			<?php if ($current_larp->isHousingReleased()) { ?>
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
				<?php } ?>
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
    		
    		
    		<?php  if (isset($house->Lat) && isset($house->Lon)) { ?>
    	<script>

        //Where you want to render the map.
        var element = document.getElementById('osm-map');
        
        // Height has to be set. You can do this in CSS too.
        element.style = 'height:300px;';
        
        // Create Leaflet map on map element.
        var map = L.map(element);
        
        // Add OSM tile layer to the Leaflet map.
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Target's GPS coordinates.
        <?php if (isset($house->Lat) && isset($house->Lon)) { 
            echo "var target = L.latLng('$house->Lat', '$house->Lon');";
            // Place a marker on the same location.
            echo "L.marker(target).addTo(map);";
        } else {
            echo "var target = L.latLng('57.47008', '13.93714');";
        } 
        ?>

        // Set map's center to target with zoom 20.
        map.setView(target, 20);
        </script>
        <?php } ?>
    		
    </body>

</html>