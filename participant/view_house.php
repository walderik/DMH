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
    if (isset($_POST['operation']) && $_POST['operation']=='updateNotesToUser' && $house->IsHouse() && $current_person->hasEditRightToHouse($house) )  { # isset($_GET['NotesToUsers'])
        $house->NotesToUsers = $_POST['NotesToUsers'];
        $house->update();
    }
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
			<?php echo $house->Name;
        	if ($house->IsHouse() && $current_person->hasEditRightToHouse($house)) {
        	    echo " &nbsp;<a href='house_form.php?operation=update&id=$house->Id' title='Redigera husbrevet'><i class='fa-solid fa-pen'> </i></a>";
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

		
	   <div class='itemcontainer'>
       <div class='itemname'>Antal 
			<?php 
			if ($house->IsHouse()) echo "sovplatser"; 
			else echo "tältplatser";
			?>
		</div>
		<?php echo htmlspecialchars($house->NumberOfBeds); 
		if ($house->IsHouse()) echo " (Hus)";
        else echo " (Lägerplats)";?>
	   </div>
		
	   <div class='itemcontainer'>
       <div class='itemname'>Plats</div>
		<?php echo nl2br(htmlspecialchars($house->PositionInVillage)); ?>
		</div>
		
	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning</div>
	   <?php echo nl2br(htmlspecialchars($house->Description)); ?>
	   </div>

		<?php if ($house->IsHouse() && !empty(trim($house->NotesToUsers))) {?>
     	   <div class='itemcontainer'>
           <div class='itemname'>Information om att bo i huset</div>
    	   <?php echo nl2br(htmlspecialchars($house->NotesToUsers)); ?>
    	   </div>
		<?php } ?>

		<?php if (isset($house->Lat) && isset($house->Lon)) {
			    echo "<div class='itemcontainer'>";
 			    echo "<div id='osm-map'></div>"; 
 			    echo "</div>";
			} ?>
    		
		<?php if ($house->IsHouse() && $current_person->hasEditRightToHouse($house)) {
    		    echo "<div class='itemcontainer'>";
    		    echo "<div class='itemname'>Husförvaltare</div>";
    		    $caretakers = $house->getHousecaretakers();

				foreach ($caretakers as $caretaker) {
				    $person = $caretaker->getPerson();

				    echo "$person->Name<br>\n";

				}
    			echo "</div>";
			} ?>
			
		<?php if ($current_larp->isHousingReleased()) {
    		    echo "<div class='itemcontainer'>";
    		    echo "<div class='itemname'>Boende under $current_larp->Name är</div>";
    		    $personsInHouse = Person::personsAssignedToHouse($house, $current_larp);
    		    $anonymousCount = 0;
    		    foreach ($personsInHouse as $personInHouse) {
    		        if ($personInHouse->isNotComing($current_larp)) continue;
    		        if ($personInHouse->hasPermissionShowName()) echo $personInHouse->Name."<br>";
    		        else $anonymousCount++;
    		    }
    		    if ($anonymousCount > 0) echo $anonymousCount." st som inte vill visa sitt namn";
    		    
    		    ?>
    		    
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