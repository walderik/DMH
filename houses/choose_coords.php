<?php
include_once 'header.php';

$house = House::loadById($_GET['id']);
 

include "navigation.php";

?>
    
<style>

#osm-map {
	cursor: crosshair;
	}



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
    	<h1>Sätt position för <?php echo $house->Name; ?>
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
map.on('click', onMapClick);

function onMapClick(e) {
    var lat  = e.latlng.lat.toFixed(8);
    var lon  = e.latlng.lng.toFixed(8);
    window.location.href ="view_house.php?id=<?php echo $house->Id?>&operation=position&lat="+lat+"&lon="+lon;
}
</script>

    </body>

</html>

