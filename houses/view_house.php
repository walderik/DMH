<?php
include_once 'header.php';

$house = House::newWithDefault();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $house = House::loadById($_GET['id']);
    
    if (isset($_GET['operation']) && ($_GET['operation'] == 'position')) {
        
        $house->Lat = ($_GET['lat']);
        $house->Lon = ($_GET['lon']);
        $house->update();
        
        header("Location: view_house.php?id=$house->Id");
        exit;
    }
} else {
    header('Location: index.php');
    exit;
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
    	<h1><?php echo $house->Name; ?>
    	<a href='house_form.php?operation=update&id=<?php echo $house->Id ?>'><i class='fa-solid fa-pen'> </i></a>
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
        				<td>Information till deltagare som ska bo i huset</td>
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
        				<td colspan = '2'><h2>Husförvaltare är:</h2><br>
        					<table id='caretakers' class='data'>
                				<th>Namn</th><th>Medlem</th><th>Email</th><th>&nbsp;</th>
                				<?php 
                				$caretakers = $house->getHousecaretakers();
                				foreach ($caretakers as $caretaker) {
                				    $person = $caretaker->getPerson();
                				    echo "<tr>\n";
                				    echo "  <td><a href='view_person.php?id=$person->Id'>$person->Name</a></td>\n";
                				    echo "  <td>".showStatusIcon($caretaker->isMember())."</td>\n";
                				    echo "  <td>".contactEmailIcon($person)."</td>\n";
                				    $txt = '"Är du säker '.$person->Name.' inte ska vara husförvaltare?"';
                				    $confirm = "onclick='return confirm($txt)'";
                				    echo "  <td><a href='logic/remove_caretaker.php?id=$person->Id&houseId=$house->Id' $confirm><i class='fa-solid fa-trash'></i></a></td>\n";
                				    echo "</tr>\n";
                				}
                				?>
        					</table>
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

    </body>

</html>