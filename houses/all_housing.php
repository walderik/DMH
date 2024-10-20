<?php
include_once 'header.php';

$house = House::newWithDefault();


include "navigation.php";

?>


<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet"/>



    <div class="content"> 
    	<h1>Alla hus och lägerplatser
    	</h1>
    	<table width='90%'>
    			<tr><td c>
    				<div id="osm-map"></div>
    				</td></tr>
					
    	</table>
    	
    	
    	<script>

//Where you want to render the map.
var element = document.getElementById('osm-map');

// Height has to be set. You can do this in CSS too.
element.style = 'height:800px;';

// Create Leaflet map on map element.
var map = L.map(element);

// Add OSM tile layer to the Leaflet map.
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);


<?php 
$houses = House::all();
foreach ($houses as $house) {
    if (isset($house->Lat) && isset($house->Lon)) {
        //echo "var popup = L.popup()".
        //".setLatLng([$house->Lat, $house->Lon])".
        //".setContent('$house->Name')".
        //".openOn(map);";
        
        
        echo "var target = L.latLng('$house->Lat', '$house->Lon');";
        
        echo "marker = L.marker(target).addTo(map);";
        
        $text = "<a href='view_house.php?operation=update&id=$house->Id' target='_blank'><b>$house->Name</b></a><br>";
        
        if ($house->hasImage()) {
            $text .= "<br><img width='100px' height='100px' alt='House image' src='../includes/display_image.php?id=$house->ImageId'/>";
        }      
        
        echo "marker.bindPopup(\"$text\").openPopup();";

    }
}

?>

// Target's GPS coordinates.
var target = L.latLng('57.47008', '13.93714');



// Set map's center to target with zoom 20.
map.setView(target, 20);

</script>

    </body>

</html>

