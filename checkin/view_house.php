<?php
include_once 'header.php';

if (isset($_GET['id']))  {
     $house = House::loadById($_GET['id']);
}
$action = "";
if (isset($_GET['action']))  {
    $action = $_GET['action'];
}
if (empty($action)) {
    if ($current_larp->isCheckoutTime()) $action = "checkout";
    else $action = "checkin";
}

if (empty($house)) {
    header('Location: index.php?error=no_house');
}


include "navigation.php";

?>
     
<style>


ul.list {
    list-style-type: disc;
}
</style>


<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet"/>  

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-house"></i>
			<?php echo $house->Name;  ?>
		</div>

		<?php if ($current_larp->isEnded()) {
		    $larp_house = Larp_House::loadByIds($house->Id, $current_larp->Id);
		    if (empty($larp_house)) {
		        $larp_house = Larp_House::newWithDefault();
		        $larp_house->HouseId = $house->Id;
		        $larp_house->LARPId = $current_larp->Id;
		        $larp_house->create();
		    }
		    ?>
    	   <div class='itemcontainer'>
    	     <div class='itemname'>Status städning</div>
    	   		<?php echo $larp_house->getStatusText(); ?>
    	   		<?php  
    	   		if (isset($larp_house->StatusPerson)) {
    	   		    $person = Person::loadById($larp_house->StatusPerson);
    	   		    echo "<br>Status satt av <a href='checkout_person.php?id=$person->Id'>$person->Name</a>, $larp_house->StatusTime";
    	   		    if (isset($larp_house->CleaningNotes)) echo "<br>$larp_house->CleaningNotes";
    	   		}
    	   		?>
    	   		<br><br>
	     		<form action='logic/house_status.php' method='post'><input type='hidden' id='houseId' name='houseId' value='<?php echo $house->Id ?>'>


				<?php selectionDropDownBySimpleArray('CleaningStatus', Larp_House::STATUS_TYPES, $larp_house->CleaningStatus); ?>
				<br>Noteringar om städningen, visas för deltagarna<br>
				<textarea id="CleaningNotes" name="CleaningNotes" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($larp_house->CleaningNotes); ?></textarea>
	     		
	     		<input type='submit' value='Ändra status/uppdatera anteckningar'>
	     		
	     		
	     		</form>
	     		
	     		
	     		
	     		
	     		
    	   </div>
    	   
		<?php } ?>

	   <div class='itemcontainer'>
		<?php 
    	    echo "<div class='itemname'>Boende under $current_larp->Name är</div>";
    	    $personsInHouse = Person::personsAssignedToHouse($house, $current_larp);
    	    foreach ($personsInHouse as $personInHouse) {
    	        if ($personInHouse->isNotComing($current_larp)) continue;
    	        $registration = $personInHouse->getRegistration($current_larp);
    	        if ($action == "checkin") {
    	            echo "<a href='checkin_person.php?id=$personInHouse->Id'>";
    	        } elseif ($action == "checkout") {
    	            echo "<a href='checkout_person.php?id=$personInHouse->Id'>";
    	        }
    	        echo $personInHouse->Name."</a> ";
    	        if ($action == "checkin") {
    	            echo showStatusIcon($registration->isCheckedIn(), "checkin_person.php?id=".$personInHouse->Id, null, "Inte incheckad", "Redan incheckad");
    	        } elseif ($action == "checkout") {
    	            echo showStatusIcon($registration->isCheckedIn(), "checkout_person.php?id=".$personInHouse->Id, null, "Inte utcheckad", "Redan utcheckad");
    	        }
    	            
    	        echo "<br>";
    	    }
	    ?>
    		    
	    </div>




		<?php 
        if ($house->hasImage()) {
            echo "<div class='itemcontainer'>";
            $image = $house->getImage();
            echo "<img width='300' src='../includes/display_image.php?id=$house->ImageId'/>\n";
            if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
            echo "</div>";
        }
        ?>

		<?php if (!$house->IsActive())  { ?>
		   	<div class='itemcontainer'>
       			<div class='itemname'>Aktiv</div>
				Huset får inte användas nu.
			</div>
		<?php } ?>
		
	   <div class='itemcontainer'>
		<?php 
		if ($house->IsHouse()) {
		    echo "<div class='itemname'>Antal sovplatser</div>";
		    if ($house->ComfortNumber != $house->MaxNumber) echo "$house->ComfortNumber - $house->MaxNumber";
		    else echo $house->ComfortNumber;
		} else {
		    echo "<div class='itemname'>Antal tältplatser</div>";
		    echo $house->NumberOfBeds;
		}
	     ?>
	   </div>
		
	   <div class='itemcontainer'>
       <div class='itemname'>Plats</div>
		<?php echo nl2br(htmlspecialchars($house->PositionInVillage)); ?>
		</div>
		
		<?php if (isset($house->Lat) && isset($house->Lon)) {
			    echo "<div class='itemcontainer'>";
 			    echo "<div id='osm-map'></div>"; 
 			    echo "</div>";
			} ?>

    	
		<?php if ($house->IsHouse()) {
		    echo "<div class='itemcontainer'>";
		    echo "<div class='itemname'>Husförvaltare</div>";
		    $caretakers = $house->getHousecaretakers();

			foreach ($caretakers as $caretaker) {
			    $person = $caretaker->getPerson();

			    echo "$person->Name<br>\n";

			}
			echo "</div>";
		} ?>
			
    	
    		    
	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning</div>
	   <?php echo nl2br(htmlspecialchars($house->Description)); ?>
	   </div>
	   
	   <?php 
	   $larp_house = Larp_House::loadByIds($house->Id, $current_larp->Id);
	   if (!empty($larp_house) && !empty($larp_house->PublicNotes)) {
	   
	   
	   ?>
	   <div class='itemcontainer'>
       <div class='itemname'>Särskild kommentar för <?php echo $current_larp->Name?></div>
	   <?php echo nl2br(htmlspecialchars($larp_house->PublicNotes)); ?>
	   </div>
	   <?php } ?>
		
		<?php if ($house->IsHouse() && !empty(trim($house->NotesToUsers))) {?>
     	   <div class='itemcontainer'>
           <div class='itemname'>Brev till deltagare som bor i huset</div>
    	   <?php echo nl2br(htmlspecialchars($house->NotesToUsers)); ?>
    	   </div>
		<?php } ?>

      		
    		
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