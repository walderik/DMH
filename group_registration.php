<?php 

	include_once 'includes/db.inc.php';

?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Gruppanmälan DMH 2023</title>
<link rel="stylesheet" href="includes/registration_system.css">
</head>
  <body>
  		<div id="site-header">
		<a href="https://dmh.berghemsvanner.se/" rel="home">
			<img src="images//IMG_1665485583436.png" width="1080" height="237" alt="Död mans hand" />
		</a>
	</div>
	
			<nav id="primary-navigation" class="site-navigation primary-navigation">
				<button class="menu-toggle">Primär meny</button>
				<a class="screen-reader-text skip-link" href="#content">Hoppa till innehåll</a>
				<div class="menu-huvud-container"><ul id="primary-menu" class="nav-menu"><li id="menu-item-917" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-199 current_page_item menu-item-917"><a href="https://dmh.berghemsvanner.se/" aria-current="page">#199 (ingen rubrik)</a></li>
<li id="menu-item-906" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-906"><a href="https://dmh.berghemsvanner.se/praktiskt-vinterlajv/">Jullajv: Praktiskt</a></li>
<li id="menu-item-914" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-914"><a href="https://dmh.berghemsvanner.se/anmalan-till-lajvet/">JULLAJV: ANMÄLAN</a></li>
<li id="menu-item-907" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-907"><a href="https://dmh.berghemsvanner.se/dod-mans-hand-2022/">HÖSTLAJV 2023</a></li>
</ul></div>			</nav>
	<div id="main" class="site-main">

<div id="main-content" class="main-content">
    <form>
      
      <h2> Gruppanmälan !
        </h2>
      <p>
        En grupp är en gruppering av roller som gör något tillsammans på lajvet. Exempelvis en familj på lajvet, en rånarliga eller ett rallarlag.
      </p>   
      <h2>Gruppledare</h2> 
      <p>
      	Gruppledaren är den som arrangörerna kommer att kontakta när det uppstår frågor kring gruppen.<br>
        <label for="group_leader_name">Gruppledarens namn:</label><br>
  		<input type="text" id="group_leader_name" name="group_leader_name"><br>
        <label for="email">E-post:</label><br>
  		<input type="email" id="email" name="email"><br>
      </p>
      <h2>Information om gruppen</h2> 
      <p>
        <label for="group_name">Gruppens namn:</label><br>
  		<input type="text" id="group_name" name="group_name"><br>
  		
        <label for="approximate_number_of_participants">Ungefär hur många gruppmedlemmar kommer ni att bli?:</label><br>
  		<input type="text" id="approximate_number_of_participants" name="approximate_number_of_participants"><br>

        <label for="housing_request">Hur vill ni bo som grupp?:</label><br>
        
        <?php 

        	$sql = "SELECT * FROM HousingRequests;";
        	$result = mysqli_query($conn, $sql);
        	$resultCheck = mysqli_num_rows($result);
        	
        	if ($resultCheck > 0) {
        		while ($row = mysqli_fetch_assoc($result)) {
        		    echo "<input type='radio' id='housing_request" . $row['Id'] . "' name='housing_request' value='" . $row['Id'] . "'>";
        		    echo "<label for='housing_request" .  $row['Id'] . "'>" . $row['Name'] . "</label><br>";
        		}
        	}
        		
	?> 
        
        <label for="need_fireplace">Behöver ni eldplats?:</label><br>
		<input type="radio" id="html" name="need_fireplace" value="yes">
  		<label for="html">Ja</label><br>
  		<input type="radio" id="css" name="need_fireplace" value="no">
  		<label for="css">Nej</label><br>

        <label for="friends">Vänner:</label><br>
		<textarea id="friends" name="friends" rows="4" cols="50">
		</textarea><br>
		 		
        <label for="enemies">Fiender:</label><br>
		<textarea id="enemies" name="enemies" rows="4" cols="50">
		</textarea><br> 		
  		
	</p>
      
    </form>
    </div>
    </div>
  </body>
</html>