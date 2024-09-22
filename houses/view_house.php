<?php
include_once 'header.php';

$house = House::newWithDefault();

include "navigation.php";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $house = House::loadById($_GET['id']);
    if (empty($house)) {
        header('Location: index.php?error=no_house');
    }
    
    if (isset($_GET['operation']) && ($_GET['operation'] == 'position')) {
        
        $house->Lat = ($_GET['lat']);
        $house->Lon = ($_GET['lon']);
        $house->update();
        
        header("Location: view_house.php?id=$house->Id");
        exit;
    } elseif (isset($_GET['person_id'])) {
        $person_id = $_GET['person_id'];
        if ($person_id == 0) {
            $error_message = "Kan du bara välja en ny husförvaltare bland de föreslagna personerna.";
        } else {
            $person = Person::loadById($_GET['person_id']);
            if (!empty($person)) {
                $house->addCaretakerPerson($person);
                $message_message = "Lägger till $person->Name som husförvaltare på $house->Name";
            }
        }
    }
} else {
    header('Location: index.php');
    exit;
}

if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}

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
    	<h1><?php echo $house->Name; ?>
    	<a href='house_form.php?operation=update&id=<?php echo $house->Id ?>' title="Redigera beskrivningen"><i class='fa-solid fa-pen'> </i></a>
    	</h1>
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

        				<?php 
            	        if ($house->hasImage()) {
            	            $image = $house->getImage();
            	            $photografer = (!empty($image->Photographer) && $image->Photographer!="") ? "Fotograf $image->Photographer" : "";
            	            echo "<td rowspan='6'>";
            	            echo "<img src='../includes/display_image.php?id=$house->ImageId' title='$photografer'/>\n";
            	            echo "<br>$photografer";
            	            echo "</td>";
            	        }
                        ?>
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
        			<?php if ($house->IsHouse()) {?>
        			<tr>
        				<td>Husbrev<br>(Information till deltagare som ska bo i huset)</td>
        				<td><?php echo nl2br(htmlspecialchars($house->NotesToUsers)); ?></td>
        			</tr>
        			<tr>
        				<td>Noteringar från besiktning</td>
        				<td><?php echo nl2br(htmlspecialchars($house->InspectionNotes)); ?></td>
        			</tr>
        			<?php }?>
        			<tr><td colspan='3'>
        				<a href='choose_coords.php?id=<?php echo $house->Id ?>'>
        				<?php if (isset($house->Lat) && isset($house->Lon)) { 
        				    echo "Ändra position <i class='fa fa-map' aria-hidden='true'></i></a> Nuvarande position: Lat $house->Lat, Lon $house->Lon<br>"; 
        				    echo "<div id='osm-map'></div>";
        				} else {
        				    echo "Sätt position <i class='fa fa-map' aria-hidden='true'></i></a>";
        				    
        				    
        				}?>
         				</td></tr>
					
					<?php  if ($house->IsHouse()) { ?>
        			<tr><td>&nbsp;</td></tr>
        			<tr>
        				<td colspan = '2'>
        					<h2 style='display:inline;text-align: left;'>Husförvaltare är:</h2> &nbsp;
        					<?php
        					    $persons = array();
        					    $personIdArr = array();
            			        $caretakers = $house->getHousecaretakers();
            					foreach ($caretakers as $caretaker) {
            					    $persons[] = $caretaker->getPerson();
            					    $personIdArr[] = $caretaker->PersonId;
            					}
        					    
            					echo contactSeveralEmailIcon("Maila alla husförvaltare", $personIdArr,
                    	            "Bäste husförvaltare av $house->Name!",
                    	            "Meddelande till alla som är husförvaltare av $house->Name", false);?>
        					
        					<table id='caretakers' class='data'>
                				<th>Namn</th><th>Medlem</th><th>Email</th><th>&nbsp;</th>
                				<?php 
                				$caretakers = $house->getHousecaretakers();
                				foreach ($persons as $person) {
                				    echo "<tr>\n";
                				    echo "  <td><a href='view_person.php?id=$person->Id'>$person->Name</a></td>\n";
                				    echo "  <td>";
                				   
                				    //showStatusIcon($caretaker->isMember()).
                				    echo "</td>\n";
                				    echo "  <td>".contactEmailIcon($person, false)."</td>\n";
                				    $txt = '"Är du säker '.$person->Name.' inte ska vara husförvaltare?"';
                				    $confirm = "onclick='return confirm($txt)'";
                				    echo "  <td><a href='logic/remove_caretaker.php?id=$person->Id&houseId=$house->Id' $confirm><i class='fa-solid fa-trash'></i></a></td>\n";
                				    echo "</tr>\n";
                				}
                				?>
        					</table>
        					<br>
        					<form method="get"  autocomplete="off" style="display: inline;">
                            	<input type="hidden" id="id"  name="id" value="<?php echo $house->Id ?>" style='display:inline;'>
                				<?php  autocomplete_person_id('60%', true); ?> 
            				</form>		
        				</td>
        			</tr>
        			<?php } ?>
        			<tr><td>&nbsp;</td></tr>
        			<tr>
        				<td colspan = '2'><h2>Historik Boende</h2>
        					<?php 
        					$larps = $house->getAllLarpsWithHousing();
        					foreach (array_reverse($larps) as $larp) {
            				    echo "<h3>$larp->Name</h3>";
            				    echo substr($larp->StartDate, 2, 8) .' -> '.substr($larp->EndDate, 2, 8)."<br><br>\n";
            					echo "<table id='caretakers' class='data'>";
                				echo "<th>Namn</th><th>Email</th>";
                				
            				    $personsInHouse = Person::personsAssignedToHouse($house, $larp);
            				    foreach ($personsInHouse as $person) {
                				    echo "<tr>\n";
                				    echo "  <td><a href='view_person.php?id=$person->Id'>$person->Name</a></td>\n";
                				    echo "  <td>".contactEmailIcon($person)."</td>\n";
                				    echo "</tr>\n";
                				}
                				echo "</table><br>\n";
            				}
        				    ?>
        				</td>
        			</tr>
        		</table>
        	</td>
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